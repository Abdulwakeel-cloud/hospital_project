<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

if (!isset($_GET['upd_id'])) {
    header("Location: show-patients.php");
    exit();
}

$id = (int) $_GET['upd_id'];

// Fetch existing patient
$stmt = $db->prepare("SELECT * FROM patients WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header("Location: show-patients.php");
    exit();
}

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
    // Optionally log: error_log('Update patient load error: ' . $e->getMessage());
}

$errors = [];
$success = '';

if (isset($_POST["submit"])) {
    $departmentId    = isset($_POST['department_id']) ? trim($_POST['department_id']) : '';
    $doctorId        = isset($_POST['doctor_id']) ? trim($_POST['doctor_id']) : '';
    $name            = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone           = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $appointmentDate = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';

    if ($departmentId === '') {
        $errors[] = "Department is required.";
    }
    if ($doctorId === '') {
        $errors[] = "Doctor is required.";
    }
    if ($name === '') {
        $errors[] = "Name is required.";
    }
    if ($phone === '') {
        $errors[] = "Phone number is required.";
    }
    if ($appointmentDate === '') {
        $errors[] = "Appointment date is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) {
        $errors[] = "Invalid date format.";
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                UPDATE patients
                SET department_id = :department_id,
                    doctor_id = :doctor_id,
                    name = :name,
                    phone = :phone,
                    appointment_date = :appointment_date
                WHERE id = :id
            ");
            $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
            $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':appointment_date', $appointmentDate, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $success = "Patient / appointment updated successfully! Redirecting...";
            echo "<script> setTimeout(function() { window.location.href = 'show-patients.php'; }, 2000); </script>";
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update Patient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../styles/style.css" rel="stylesheet">
    <style>  
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f6fa;
      margin: 0;
      padding: 0;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 240px;
      background-color: #1e1e2d;
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: start;
      padding: 1rem;
    }

    .sidebar .logo {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 2rem;
      text-transform: uppercase;
      color: #3eb5b3;
      display: block;
      width: 100%;
      text-align: center;
    }

    .sidebar a {
      color: #ccc;
      text-decoration: none;
      display: block;
      width: 100%;
      padding: 0.7rem 1rem;
      border-radius: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .sidebar a::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: linear-gradient(180deg, #3eb5b3, #2b8c8a);
      transform: scaleY(0);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border-radius: 0 4px 4px 0;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: linear-gradient(135deg, #3eb5b3, #2b8c8a);
      color: #fff;
      transform: translateX(8px);
      box-shadow: 0 4px 12px rgba(62, 181, 179, 0.3);
      padding-left: 1.2rem;
    }

    .sidebar a:hover::before,
    .sidebar a.active::before {
      transform: scaleY(1);
    }

    .sidebar .logout {
      margin-top: auto;
      background-color: #dc3545;
      text-align: center;
      border-radius: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar .logout:hover {
      background: linear-gradient(135deg, #dc3545, #c82333);
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .main-content {
      margin-left: 250px;
      padding: 2rem;
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .error-box {
      background-color: #f8d7da;
      color: #721c24;
      padding: 1rem;
      border-radius: 5px;
      margin-top: 1rem;
      border: 1px solid #f5c6cb;
      animation: slideIn 0.3s ease-out;
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    </style>
</head>
<body>

  <div class="sidebar">
    <div class="logo">MEDIXAL</div>
    <a href="../index.php">Home</a>
    <a href="../admins/admins.php">Admins</a>
    <a href="../categories-admins/show-categories.php">Categories</a>
    <a href="../services-admins/show-services.php">Services</a>
    <a href="../doctor-admins/show-doctors.php">Doctors</a>
    <a href="../testimony-admins/show-posts.php">Posts</a>
    <a href="../patients-say-admins/show-testimonials.php">What Patients Say</a>
    <a href="show-patients.php" class="active">Patients</a>
    <?php if(isset($_SESSION["admin_name"])) : ?>
    <a href="../logout.php" class="logout text-white">Logout</a>
    <?php  else :   ?>
    <a href="../admins/login-admins.php" class="logout text-white">Login</a>
    <?php endif; ?>
  </div>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">Update Patient / Appointment</h4>
    </div>

    <div class="card p-4">
      <?php if (!empty($errors)) : ?>
        <div class="error-box">
          <ul class="mb-0">
            <?php foreach ($errors as $error) : ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (!empty($success)) : ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="department_id" class="form-label">Department</label>
            <select name="department_id" id="department_id" class="form-select" required>
              <option value="">Select Department</option>
              <?php
                $currentDept = $_POST['department_id'] ?? $patient['department_id'] ?? '';
                foreach ($categories as $cat):
              ?>
                <option value="<?php echo htmlspecialchars($cat['id']); ?>"
                  <?php echo ((string)$currentDept === (string)$cat['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3 col-md-6">
            <label for="doctor_id" class="form-label">Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select" required>
              <option value="">Select Doctor</option>
              <?php
                $currentDoc = $_POST['doctor_id'] ?? $patient['doctor_id'] ?? '';
                foreach ($doctors as $doc):
              ?>
                <option value="<?php echo htmlspecialchars($doc['id']); ?>"
                  <?php echo ((string)$currentDoc === (string)$doc['id']) ? 'selected' : ''; ?>>
                  Dr. <?php echo htmlspecialchars($doc['firstname'] . ' ' . $doc['lastname']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label for="name" class="form-label">Patient Name</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control"
            required
            value="<?php echo htmlspecialchars($_POST['name'] ?? $patient['name']); ?>"
          >
        </div>

        <div class="row">
          <div class="mb-3 col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input
              type="text"
              name="phone"
              id="phone"
              class="form-control"
              required
              value="<?php echo htmlspecialchars($_POST['phone'] ?? $patient['phone']); ?>"
            >
          </div>
          <div class="mb-3 col-md-6">
            <label for="appointment_date" class="form-label">Appointment Date</label>
            <input
              type="date"
              name="appointment_date"
              id="appointment_date"
              class="form-control"
              required
              value="<?php echo htmlspecialchars($_POST['appointment_date'] ?? ($patient['appointment_date'] ?? '')); ?>"
            >
          </div>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update Patient / Appointment</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


