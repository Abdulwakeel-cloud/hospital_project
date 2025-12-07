<?php
// Handles appointment form submission from the homepage.
// NOTE: Adjust the INSERT query/table/columns to match your actual database schema.

require_once __DIR__ . '/session_config.php';
require_once  'dbh.php';


// Fetch departments and doctors for selects
$categories = [];
$doctors = [];

try {
    $catStmt = $db->prepare("SELECT id, category_name FROM categories ORDER BY category_name ASC");
    $catStmt->execute();
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    $docStmt = $db->prepare("SELECT id, firstname, lastname FROM doctors ORDER BY firstname ASC, lastname ASC");
    $docStmt->execute();
    $doctors = $docStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo $e->getMessage();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departmentId    = isset($_POST['department_id']) ? trim($_POST['department_id']) : '';
    $doctorId        = isset($_POST['doctor_id']) ? trim($_POST['doctor_id']) : '';
    $name            = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone           = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $appointmentDate = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';

    if ($departmentId === '') {
        $errors[] = 'Department is required.';
    } elseif (!is_numeric($departmentId)) {
    $errors[] = 'Invalid department selected.';
}

    if ($doctorId === '') {
        $errors[] = 'Doctor is required.';
    } elseif (!is_numeric($doctorId)) {
    $errors[] = 'Invalid doctor selected.';
}

    if ($name === '') {
        $errors[] = 'Name is required.';
    }elseif (strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters long.';
} elseif (strlen($name) > 100) {
    $errors[] = 'Name is too long. Maximum 100 characters allowed.';
} elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $name)) {
    $errors[] = 'Name contains invalid characters.';
}

    if ($phone === '') {
        $errors[] = 'Phone is required.';
    }else {
    // Remove common phone formatting characters
    $phoneDigits = preg_replace('/[\s\-\(\)\+]/', '', $phone);
    // Check if phone contains only digits and has reasonable length
    if (!preg_match('/^\d+$/', $phoneDigits)) {
        $errors[] = 'Phone number contains invalid characters.';
    } elseif (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
        $errors[] = 'Phone number must be between 10 and 15 digits.';
    }
}

    if ($appointmentDate === '') {
        $errors[] = 'Appointment date is required.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) {
        $errors[] = 'Invalid date format. Please use YYYY-MM-DD format. ';
    } else {
    // Validate date is not in the past
    $today = date('Y-m-d');
    if ($appointmentDate < $today) {
        $errors[] = 'Appointment date cannot be in the past.';
    } 
   $maxDate = date('Y-m-d', strtotime('+1 year'));
    if ($appointmentDate > $maxDate) {
        $errors[] = 'Appointment date cannot be more than 1 year in the future.';
    }
    
    // Optional: Prevent booking on weekends (Saturday = 6, Sunday = 0)
    $dayOfWeek = date('w', strtotime($appointmentDate));
    if ($dayOfWeek == 0 || $dayOfWeek == 6) {
        $errors[] = 'Appointments are only available on weekdays (Monday-Friday).';
    }
  }

    if (empty($errors)) {
        try {
            $sql = "
                INSERT INTO patients
                    (department_id, doctor_id, name, phone, appointment_date, created_at)
                VALUES
                    (:department_id, :doctor_id, :name, :phone, :appointment_date, NOW())
            ";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
            $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':appointment_date', $appointmentDate, PDO::PARAM_STR);
            $stmt->execute();

            $success = 'Patient / appointment created successfully.';
            // Clear POST values for form
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error while creating patient.';
        }
    }
}

