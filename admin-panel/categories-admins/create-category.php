<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

$errors = [];
$max_file_size = 5 * 1024 * 1024; // 5MB in bytes

if (isset($_POST["submit"])) {
    $catName = trim($_POST["name"]);

    // --- File Data Extraction ---
    $img_name = $_FILES['img']['name'] ?? '';
    $img_tmp_name = $_FILES['img']['tmp_name'] ?? '';
    $img_size = $_FILES['img']['size'] ?? 0;
    $img_error = $_FILES['img']['error'] ?? UPLOAD_ERR_NO_FILE;

    // --- Validation and Error Checking ---

    // 1. Check for required fields and upload errors
    if (empty($catName) || $img_error === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Category name and image file are required.";
    } elseif ($img_error !== UPLOAD_ERR_OK) {
        $errors[] = "File upload failed with error code: " . $img_error;
    } 
    
    if (empty($errors)) {
        // 2. File Size Validation
        if ($img_size > $max_file_size) {
            $errors[] = "Image size is too large. Maximum allowed size is " . ($max_file_size / 1024 / 1024) . "MB.";
        }

        // 3. File Extension and Type Validation
        $img_extension = pathinfo($img_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (!in_array(strtolower($img_extension), $allowed_extensions)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, and PNG are allowed extensions.";
        } else {
            // Using exif_imagetype is highly recommended for MIME check on the actual file content
            $image_type = exif_imagetype($img_tmp_name);
            $allowed_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
            
            if (!in_array($image_type, $allowed_types)) {
                $errors[] = "File content is not a valid image type (JPEG or PNG).";
            }
        }
    }

    // --- Database Insertion and File Handling ---
    if (empty($errors)) {
        // Generate a unique filename for security
        $new_img_name = uniqid("service", true) . "." . $img_extension;
        $dir = "../uploads/services/" . $new_img_name;

        try {
            // Start Transaction to ensure data integrity
            $db->beginTransaction();

            // 1. Insert record into database
            $stmt = $db->prepare("INSERT INTO categories (category_name, img) VALUES (:category, :image)");
            $stmt->bindParam(':category', $catName);
            $stmt->bindParam(':image', $new_img_name);
            $stmt->execute();

            // 2. Move uploaded file
            if (move_uploaded_file($img_tmp_name, $dir)) {
                $db->commit(); // Commit transaction only if file move succeeds
                $success = "Category added successfully! Redirecting...";
                
                // Redirect user after success
                echo "<script> setTimeout(function() { window.location.href = 'show-categories.php'; }, 2000); </script>";
            } else {
                $db->rollBack(); // Rollback DB insert if file move fails
                $errors[] = "File upload failed after database entry. The record was rolled back.";
            }
        } catch (PDOException $e) {
            // Catch any DB errors and roll back
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Category</title>
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
<div id="wrapper">
    <div class="sidebar">
      <div class="logo">MEDIXAL</div>
      <a href="../index.php">Home</a>
      <a href="../admins/admins.php">Admins</a>
      <a href="show-categories.php" class="active">Categories</a>
      <a href="../services-admins/show-services.php">Services</a>
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
                    <h5 class="card-title mb-4 d-inline">Create Categories</h5>

                    <form method="POST" action="" enctype="multipart/form-data">
                      <div class="mb-3 mt-3">
                        <label for="category" class="form-label fw-bold">Category</label>
                        <input type="text" name="name" id="form2Example1" class="form-control mb-2" placeholder="Category name" />
                        <input type="file" name="img" id="form2Example1" class="form-control mb-2" placeholder="Category image" />
                      </div>

                      <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary">Create Category</button>
                      </div>
                      
                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <ul>
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