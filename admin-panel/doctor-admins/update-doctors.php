<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Redirect if no ID
if (!isset($_GET["upd_id"])) {
    header('Location: show-doctors.php');
    exit();
}

$id = (int) $_GET["upd_id"];

// Fetch doctor
$statement = $db->prepare("SELECT * FROM doctors WHERE id = :id LIMIT 1");
$statement->bindValue(':id', $id, PDO::PARAM_INT);
$statement->execute();
$doctors = $statement->fetch(PDO::FETCH_OBJ);

if (!$doctors) {
    header('Location: show-doctors.php');
    exit();
}

// Error handler
$error = [];
$success = "";

// Fetch categories
$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_OBJ);

// --- Handle POST ---
if (isset($_POST['submit'])) {

    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $title = trim($_POST['title']);
    $twitter = trim($_POST['twitter']);
    $linkedin = trim($_POST['linkedin']);
    $facebook = trim($_POST['facebook']);

    // Validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($title)) {
        $error[] = "First name, last name, email, and title are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Invalid email format.";
    }

    // Upload directory (always define this)
    $upload_dir = __DIR__ . '/../uploads/profile/';

    // Image logic
    $image_uploaded = !empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;
    $img_size = $_FILES['image']['size'] ?? 0;
    $new_img_name = null;

    if ($image_uploaded) {
        $img_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        // Enforce max size 5MB at application level
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($img_size > $maxSize) {
            $error[] = "Image is too large. Maximum allowed size is 5MB.";
        } elseif (!in_array($img_extension, $allowed)) {
            $error[] = "Invalid image format. Only JPG, JPEG, PNG, and GIF allowed.";
        } else {
            $new_img_name = uniqid('DOCTOR_', true) . '.' . $img_extension;
            $targetPath = $upload_dir . $new_img_name;
        }
    }

    // Execute update
    if (empty($error)) {
        try {
            $sql = "UPDATE doctors SET
                firstname = :firstname,
                lastname = :lastname,
                email = :email,
                title = :title,
                twitter = :twitter,
                linkedin = :linkedin,
                facebook = :facebook";

            if ($image_uploaded) {
                $sql .= ", image = :image";
            }

            $sql .= " WHERE id = :id";

            $stmt = $db->prepare($sql);

            // Bind fields
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':twitter', $twitter);
            $stmt->bindParam(':linkedin', $linkedin);
            $stmt->bindParam(':facebook', $facebook);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            if ($image_uploaded) {
                $stmt->bindParam(':image', $new_img_name);
            }

            $stmt->execute();

            // Handle image upload
            if ($image_uploaded) {

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Move new file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {

                    // Delete old image
                    if (!empty($doctors->image) && file_exists($upload_dir . $doctors->image)) {
                        unlink($upload_dir . $doctors->image);
                    }

                } else {
                    $error[] = "Database updated but failed to move image.";
                }
            }

            if (empty($error)) {
                header('Location: show-doctors.php');
                exit();
            }

        } catch (Exception $e) {
            $error[] = "Update failed: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- This file has been downloaded from Bootsnipp.com. Enjoy! -->
    <title>Admin Panel</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
  </style>  <!-- <link href="../styles/style.css" rel="stylesheet"> -->
  <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div id="wrapper">
    <div class="sidebar">
      <div class="logo">MEDIXAL</div>
      <a href="../index.php">Home</a>
      <a href="../admins/admins.php">Admins</a>
      <a href="../categories-admins/show-categories.php">Categories</a>
      <a href="../services-admins/show-services.php">Services</a>
      <a href="show-doctors.php"  class="active">Doctors</a>
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
                    <h5 class="card-title mb-4 d-inline">Update Doctor</h5>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mt-3">
                            <?php foreach ($error as $msg) echo htmlspecialchars($msg) . "<br>"; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success mt-3">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                      <div class="mb-3 mt-3">
                        <input type="text" name="firstname" id="firstname" class="form-control mb-2" placeholder="First Name" required value="<?php echo htmlspecialchars($doctors->firstname ?? ''); ?>" />
                        <input type="text" name="lastname" id="lastname" class="form-control mb-2" placeholder="Last Name" required value="<?php echo htmlspecialchars($doctors->lastname ?? ''); ?>" />
                        <input type="email" name="email" id="email" class="form-control mb-2" placeholder="Email Address" required value="<?php echo htmlspecialchars($doctors->email ?? ''); ?>" />

                       <select name="title" id="title-select" class="form-control mb-2">
                          <option value="" disabled>Select Category/Specialization</option>

                          <?php foreach($categories as $cat): ?>
                              <option value="<?php echo htmlspecialchars($cat->id); ?>"
                                  <?php echo ($doctors->title == $cat->id) ? 'selected' : ''; ?>>
                                  <?php echo htmlspecialchars($cat->category_name); ?>
                              </option>
                          <?php endforeach; ?>
                      </select>

                        <input type="text" name="twitter" id="twitter" class="form-control mb-2" placeholder="Twitter Username" value="<?php echo htmlspecialchars($doctors->twitter ?? ''); ?>" />
                        <input type="text" name="linkedin" id="linkedin" class="form-control mb-2" placeholder="LinkedIn Address" value="<?php echo htmlspecialchars($doctors->linkedin ?? ''); ?>" />
                        <input type="text" name="facebook" id="facebook" class="form-control mb-2" placeholder="Facebook Username" value="<?php echo htmlspecialchars($doctors->facebook ?? ''); ?>" />
                        <input type="file" name="image" id="image-upload" class="form-control mb-2" placeholder="Choose Image">
                      </div>

                      <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
                      </div>
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