<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

$errors = [];
if (isset($_POST["submit"])) {
    $serviceName = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    
    // Image file handling
    $img_name = $_FILES['img']['name'] ?? '';
    $img_tmp_name = $_FILES['img']['tmp_name'] ?? '';
    
    // Validation
    if (empty($serviceName)) {
        $errors[] = "Service name is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }
    if (empty($img_name)) {
        $errors[] = "Image is required.";
    } elseif (!empty($img_name)) {
        $img_extension = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($img_extension, $allowed)) {
            $errors[] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            // Generate a unique filename for security and to prevent overwrites
            $new_img_name = uniqid("Service_", true) . "." . $img_extension;
            // Define the upload directory
            $dir = "../uploads/services/" . $new_img_name;
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO services (service_name, description, image, created_at) VALUES (:name, :description, :image, NOW())");
            $stmt->bindParam(':name', $serviceName);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image', $new_img_name);
            $stmt->execute();
            
            // Move uploaded file only if DB insertion succeeds
            if (move_uploaded_file($img_tmp_name, $dir)) {
                $success = "Service added successfully! Redirecting...";
                // Redirect user after success
                echo "<script> setTimeout(function() { window.location.href = 'show-services.php'; }, 2000); </script>";
            } else {
                $errors[] = "Database entry successful, but file upload failed.";
            }
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
    <title>Create Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../styles/style.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    .form-control {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-control:hover {
      border-color: #3eb5b3;
      box-shadow: 0 0 0 3px rgba(62, 181, 179, 0.1);
    }

    .form-control:focus {
      border-color: #3eb5b3;
      box-shadow: 0 0 0 3px rgba(62, 181, 179, 0.2);
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
<div id="wrapper">
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
      <?php else: ?>
      <a href="../admins/login-admins.php" class="logout text-white">Login</a>
      <?php endif; ?>
    </div>
    <div class="container-fluid main-content">
        <div class="row">
          <div class="col">
            <div class="row justify-content-center">
              <div class="col-12 col-md-8 col-lg-6">
                <div class="card mx-auto" style="max-width:720px;">
                  <div class="card-body">
                    <h5 class="card-title mb-4 d-inline">Create Service</h5>

                    <form method="POST" action="" enctype="multipart/form-data">
                      <div class="mb-3 mt-3">
                        <label for="service-name" class="form-label fw-bold">Service Name</label>
                        <input type="text" name="name" id="service-name" class="form-control mb-2" placeholder="Service name" required />
                      </div>

                      <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea name="description" id="description" class="form-control mb-2" rows="4" placeholder="Service description" required></textarea>
                      </div>

                      <div class="mb-3">
                        <label for="service-image" class="form-label fw-bold">Service Image</label>
                        <input type="file" name="img" id="service-image" class="form-control mb-2" accept="image/jpeg,image/jpg,image/png,image/gif" required />
                        <small class="form-text text-muted">Only JPG, JPEG, PNG, and GIF are allowed.</small>
                      </div>

                      <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary">Create Service</button>
                      </div>
                      
                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
          
                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
<script type="text/javascript">

</script>
</body>
</html>

