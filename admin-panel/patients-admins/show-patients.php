<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

// Fetch all patients with department and doctor info
$sql = "
    SELECT 
        p.*,
        c.category_name AS department_name,
        d.firstname AS doctor_firstname,
        d.lastname AS doctor_lastname
    FROM patients p
    LEFT JOIN categories c ON p.department_id = c.id
    LEFT JOIN doctors d ON p.doctor_id = d.id
    ORDER BY p.id DESC
";
$stmt = $db->prepare($sql);
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel - Patients</title>
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

    .table thead {
      background-color: #174E70;
      color: #fff;
    }

    .btn-primary {
      background-color: #3eb5b3;
      border: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      transform: translate(-50%, -50%);
      transition: width 0.6s ease, height 0.6s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #3eb5b3, #2b8c8a);
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 8px 24px rgba(62, 181, 179, 0.4);
    }

    .btn-primary:hover::before {
      width: 300px;
      height: 300px;
    }

    @media (max-width: 992px) {
      .sidebar {
        width: 100%;
        height: auto;
        flex-direction: row;
        justify-content: space-between;
      }
      .main-content {
        margin-left: 0;
        margin-top: 70px;
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
      <h4 class="fw-bold">Patients</h4>
      <?php if (isset($_SESSION["admin_name"])) : ?>
      <a href="create-patient.php" class="btn btn-primary">Add Patient</a>
      <?php endif; ?>
    </div>

    <div class="card p-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Phone</th>
              <th>Department</th>
              <th>Doctor</th>
              <th>Appointment Date</th>
              <th>Created At</th>
              <th>Delete</th>
              <th>Update</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($patients)) : ?>
              <?php $i = 1; ?>
              <?php foreach ($patients as $p) : ?>
              <tr>
                <th scope="row"><?php echo $i++; ?></th> 
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['phone']); ?></td>
                <td><?php echo htmlspecialchars($p['department_name'] ?? ''); ?></td>
                <td>
                  <?php 
                    $docName = trim(($p['doctor_firstname'] ?? '') . ' ' . ($p['doctor_lastname'] ?? ''));
                    echo htmlspecialchars($docName);
                  ?>
                </td>
                <td><?php echo htmlspecialchars($p['appointment_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($p['created_at']); ?></td>
                <td>
                  <a href="delete.php?del_id=<?php echo $p['id']; ?>">
                    <button class="btn btn-danger">Delete</button>
                  </a>
                </td>
                <td>
                  <a href="update-patient.php?upd_id=<?php echo $p['id']; ?>">
                    <button class="btn btn-warning text-white">Update</button>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted">No patients found.</td></tr> 
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


