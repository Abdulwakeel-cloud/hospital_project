<?php 
require_once "../includes/session_config.php"; 
require_once "../includes/dbh.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel - Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles/style.css" rel="stylesheet">
  <style>  
  body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f6fa;
      margin: 0;
      padding: 0;
    }

    /* Sidebar */
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
      transition: all 0.3s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #3eb5b3;
      color: #fff;
    }

    .sidebar .logout {
      margin-top: auto;
      background-color: #dc3545;
      text-align: center;
      border-radius: 6px;
    }

    /* Main Content */
    .main-content {
      margin-left: 250px;
      padding: 2rem;
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .table thead {
      background-color: #174E70;
      color: #fff;
    }

    .btn-primary {
      background-color: #3eb5b3;
      border: none;
    }

    .btn-primary:hover {
      background-color: #2b8c8a;
    }

    /* Responsive Sidebar */
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
    button{
      border: none;
      color: #fff;
      background-color: #dc3545;
      border-radius: 5px;
      padding: 5px 20px;
      transition: 0.2s ease-in-out;
    }
    button:hover{
      background-color: #2b8c8a;
    }
    .btn-danger, .btn-warning {
      padding: 5px 15px;
      border-radius: 5px;
      transition: 0.2s ease-in-out;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">MEDIXAL</div>
    <a href="index.php" class="active">Home</a>
    <a href="admins/admins.php">Admins</a>
    <a href="categories-admins/show-categories.php">Categories</a>
    <a href="services-admins/show-services.php">Services</a>
    <a href="doctor-admins/show-doctors.php">Doctors</a>
    <a href="testimony-admins/show-posts.php">Posts</a>
    <a href="patients-say-admins/show-testimonials.php">What Patients Say</a>
    <a href="patients-admins/show-patients.php">Patients</a>
    <?php if(isset($_SESSION["admin_name"])) : ?>
    <a href="logout.php" class="logout text-white">Logout</a>
    <?php  else :   ?>
    <a href="admins/login-admins.php" class="logout text-white">Login</a>
    <?php endif; ?>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h4 class="fw-bold mb-4">Dashboard</h4>
    <div class="row g-4">
      <?php if(isset($_SESSION["admin_name"])) : ?>

      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Doctors</h5>
          <p class="card-text fs-5">
            Total: <strong><?php $stmt = $db->query("SELECT COUNT(*) FROM doctors");
                 echo $stmt->fetchColumn();
               ?></strong>
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Testimonies</h5>
          <p class="card-text fs-5">
            Total: <strong><?php $stmt = $db->query("SELECT COUNT(*) FROM posts");
                 echo $stmt->fetchColumn();
               ?></strong>
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Categories</h5>
          <p class="card-text fs-5">
            Total: <strong><?php  $stmt = $db->query("SELECT COUNT(*) FROM categories");
                echo $stmt->fetchColumn();
                ?></strong>
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Admins</h5>
          <p class="card-text fs-5">
            Total: <strong><?php  $stmt = $db->query("SELECT COUNT(*) FROM admins");
                echo $stmt->fetchColumn();
                ?></strong>
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Services</h5>
          <p class="card-text fs-5">
            Total: <strong><?php  $stmt = $db->query("SELECT COUNT(*) FROM services");
                echo $stmt->fetchColumn();
                ?></strong>
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Patients</h5>
          <p class="card-text fs-5">
            Total: <strong><?php  $stmt = $db->query("SELECT COUNT(*) FROM patients");
                echo $stmt->fetchColumn();
                ?></strong>
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">Clients Say</h5>
          <p class="card-text fs-5">
            Total: <strong><?php  $stmt = $db->query("SELECT COUNT(*) FROM testimonials");
                echo $stmt->fetchColumn();
                ?></strong>
          </p>
        </div>
      </div>  
      <?php  else : ?>
            <div class="col-md-4">
        <div class="card p-4">
          <h5 class="card-title fw-bold">log In to view</h5>
         
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>

  <!--  Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
