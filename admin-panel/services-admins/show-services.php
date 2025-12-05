<?php 
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

// Fetch all services
$sql = "SELECT * FROM services ORDER BY id DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel - Services</title>
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

    /* Main Content */
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
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    button:hover{
      background: linear-gradient(135deg, #2b8c8a, #3eb5b3);
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 6px 20px rgba(43, 140, 138, 0.4);
    }
    .btn-danger, .btn-warning {
      padding: 5px 15px;
      border-radius: 5px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }

    .btn-danger:hover {
      background: linear-gradient(135deg, #dc3545, #c82333) !important;
      transform: translateY(-2px) scale(1.08);
      box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .btn-warning:hover {
      background: linear-gradient(135deg, #ffc107, #ff9800) !important;
      transform: translateY(-2px) scale(1.08);
      box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
      color: #fff !important;
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

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">MEDIXAL</div>
    <a href="../index.php">Home</a>
    <a href="../admins/admins.php">Admins</a>
    <a href="../categories-admins/show-categories.php">Categories</a>
    <a href="show-services.php" class="active">Services</a>
    <a href="../doctor-admins/show-doctors.php">Doctors</a>
    <a href="../testimony-admins/show-posts.php">Posts</a>
    <a href="../patients-say-admins/show-testimonials.php">What Patients Say</a>
    <a href="../patients-admins/show-patients.php">Patients</a>
    <?php if(isset($_SESSION["admin_name"])) : ?>
    <a href="../logout.php" class="logout text-white">Logout</a>
    <?php  else :   ?>
    <a href="../admins/login-admins.php" class="logout text-white">Login</a>
    <?php endif; ?>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="card-title mb-0">Services</h5>
        <?php if (isset($_SESSION["admin_name"])): ?>
      <a href="create-service.php" class="btn btn-primary">Create Service</a>
      <?php else : ?>
      <span></span>
      <?php endif; ?>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <table class="table table-hover">
          <thead class="table-dark">
            <tr>
              <th scope="col">No</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Image</th>
              <th scope="col">Update</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
 <tbody>
    <?php if (isset($_SESSION["admin_name"])): ?>
        <?php if (!empty($services)): ?>
            <?php $i = 1; ?> 
            <?php foreach ($services as $service): ?>
                <tr>
                    <th scope="row"><?php echo $i++; ?></th> 
                    <td><?php echo htmlspecialchars($service["service_name"] ?? "Unnamed Service"); ?></td>
                    <td><?php echo htmlspecialchars(substr($service["description"] ?? "No description", 0, 50)) . (strlen($service["description"] ?? "") > 50 ? '...' : ''); ?></td>
                    <td>
                        <img src="../uploads/services/<?php echo htmlspecialchars($service["image"] ?? "default.png"); ?>" 
                             alt="<?php echo htmlspecialchars($service["service_name"] ?? "Service Image"); ?>" 
                             width="70px" height="70px" style="object-fit: cover; border-radius: 5px;">
                    </td>
                    <td><a href="update-service.php?upd_id=<?php echo htmlspecialchars($service["id"]); ?>" class="btn btn-warning text-white">Update</a></td>
                    <td><a href="delete.php?del_id=<?php echo htmlspecialchars($service["id"]); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">No services found.</td></tr>
        <?php endif; ?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">No Admin Found.</td></tr> 
    <?php endif; ?>
</tbody>
        </table>
      </div>
    </div>
  </div>

  <!--  Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

