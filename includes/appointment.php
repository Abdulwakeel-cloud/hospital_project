<?php
// Handles appointment form submission from the homepage.
// NOTE: Adjust the INSERT query/table/columns to match your actual database schema.

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/dbh.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Collect and trim input
$departmentId    = isset($_POST['department_id']) ? trim($_POST['department_id']) : '';
$doctorId        = isset($_POST['doctor_id']) ? trim($_POST['doctor_id']) : '';
$name            = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone           = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$appointmentDate = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';

$errors = [];

// Enhanced validation
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
} elseif (strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters long.';
} elseif (strlen($name) > 100) {
    $errors[] = 'Name is too long. Maximum 100 characters allowed.';
} elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $name)) {
    $errors[] = 'Name contains invalid characters.';
}

if ($phone === '') {
    $errors[] = 'Phone number is required.';
} else {
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
    $errors[] = 'Invalid date format. Please use YYYY-MM-DD format.';
} else {
    // Validate date is not in the past
    $today = date('Y-m-d');
    if ($appointmentDate < $today) {
        $errors[] = 'Appointment date cannot be in the past.';
    }
    
    // Optional: Prevent booking too far in the future (e.g., more than 1 year)
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

// If there are errors, redirect back with messages
if (!empty($errors)) {
    $_SESSION['appointment_errors'] = $errors;
    $_SESSION['appointment_old'] = [
        'department_id'    => $departmentId,
        'doctor_id'        => $doctorId,
        'name'             => $name,
        'phone'            => $phone,
        'appointment_date' => $appointmentDate,
    ];

    header('Location: ../index.php#appointment');
    exit;
}

try {
    // Verify department and doctor exist
    $deptCheck = $db->prepare("SELECT id FROM categories WHERE id = :id LIMIT 1");
    $deptCheck->bindParam(':id', $departmentId, PDO::PARAM_INT);
    $deptCheck->execute();
    if ($deptCheck->rowCount() === 0) {
        $errors[] = 'Selected department does not exist.';
        throw new Exception('Invalid department');
    }
    
    $docCheck = $db->prepare("SELECT id FROM doctors WHERE id = :id LIMIT 1");
    $docCheck->bindParam(':id', $doctorId, PDO::PARAM_INT);
    $docCheck->execute();
    if ($docCheck->rowCount() === 0) {
        $errors[] = 'Selected doctor does not exist.';
        throw new Exception('Invalid doctor');
    }
    
    // Check for duplicate appointments (same doctor, same date)
    // Optional: You can uncomment this if you want to prevent duplicate bookings
    /*
    $duplicateCheck = $db->prepare("SELECT id FROM patients WHERE doctor_id = :doctor_id AND appointment_date = :appointment_date LIMIT 1");
    $duplicateCheck->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
    $duplicateCheck->bindParam(':appointment_date', $appointmentDate, PDO::PARAM_STR);
    $duplicateCheck->execute();
    if ($duplicateCheck->rowCount() > 0) {
        $errors[] = 'This time slot is already booked. Please choose another date.';
        throw new Exception('Duplicate appointment');
    }
    */
    
    // Insert appointment into database
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

    $_SESSION['appointment_success'] = 'Your appointment has been booked successfully. We will contact you soon to confirm.';
    
    // Optional: Send email confirmation
    // You can uncomment and configure this if you want email notifications
    /*
    $to = "patient@example.com"; // Use patient's email if you add email field
    $subject = "Appointment Confirmation - Medixal";
    $message = "Dear $name,\n\n";
    $message .= "Your appointment has been booked successfully.\n\n";
    $message .= "Appointment Details:\n";
    $message .= "Date: " . date('F j, Y', strtotime($appointmentDate)) . "\n";
    $message .= "We will contact you at $phone to confirm.\n\n";
    $message .= "Thank you for choosing Medixal.";
    $headers = "From: noreply@medixal.com\r\n";
    mail($to, $subject, $message, $headers);
    */

} catch (PDOException $e) {
    // Log the real error for debugging
    error_log('Appointment insert error: ' . $e->getMessage());
    if (empty($errors)) {
        $_SESSION['appointment_errors'] = ['An error occurred while saving your appointment. Please try again later.'];
    } else {
        $_SESSION['appointment_errors'] = $errors;
    }
    $_SESSION['appointment_old'] = [
        'department_id'    => $departmentId,
        'doctor_id'        => $doctorId,
        'name'             => $name,
        'phone'            => $phone,
        'appointment_date' => $appointmentDate,
    ];
    header('Location: ../index.php#appointment');
    exit;
} catch (Exception $e) {
    // Handle validation errors
    if (!empty($errors)) {
        $_SESSION['appointment_errors'] = $errors;
        $_SESSION['appointment_old'] = [
            'department_id'    => $departmentId,
            'doctor_id'        => $doctorId,
            'name'             => $name,
            'phone'            => $phone,
            'appointment_date' => $appointmentDate,
        ];
        header('Location: ../index.php#appointment');
        exit;
    }
}

header('Location: ../index.php#appointment');
exit;


