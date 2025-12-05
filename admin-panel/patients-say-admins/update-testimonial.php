<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

if (!isset($_GET['upd_id'])) {
    header("Location: show-testimonials.php");
    exit();
}

$id = (int) $_GET['upd_id'];

// Fetch existing testimonial
$stmt = $db->prepare("SELECT * FROM testimonials WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$testimonial = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$testimonial) {
    header("Location: show-testimonials.php");
    exit();
}

$errors = [];
if (isset($_POST["submit"])) {
    $name = trim($_POST["name"]);
    $profession = trim($_POST["profession"]);
    $message = trim($_POST["message"]);

    $img_name = $_FILES['img']['name'] ?? '';
    $img_tmp_name = $_FILES['img']['tmp_name'] ?? '';
    $current_image = $testimonial['image'];
    $new_img_name = $current_image;

    if (empty($name)) {
        $errors[] = "Patient name is required.";
    }
    if (empty($profession)) {
        $errors[] = "Profession is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    if (!empty($img_name)) {
        $img_extension = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($img_extension, $allowed)) {
            $errors[] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            $new_img_name = uniqid("Testimonial_", true) . "." . $img_extension;
            $dir = "../uploads/testimonials/" . $new_img_name;
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE testimonials SET name = :name, profession = :profession, message = :message, image = :image WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':profession', $profession);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':image', $new_img_name);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!empty($img_name) && !empty($img_tmp_name)) {
                if (!is_dir("../uploads/testimonials")) {
                    mkdir("../uploads/testimonials", 0777, true);
                }
                move_uploaded_file($img_tmp_name, $dir);

                if (!empty($current_image)) {
                    $oldPath = "../uploads/testimonials/" . $current_image;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            $success = "Testimonial updated successfully! Redirecting...";
            echo "<script> setTimeout(function() { window.location.href = 'show-testimonials.php'; }, 2000); </script>";
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
    <title>Update Testimonial</title>
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
    <a href="show-testimonials.php" class="active">What Patients Say</a>
    <a href="../patients-admins/show-patients.php">Patients</a>
    <?php if(isset($_SESSION["admin_name"])) : ?>
    <a href="../logout.php" class="logout text-white">Logout</a>
    <?php  else :   ?>
    <a href="../admins/login-admins.php" class="logout text-white">Login</a>
    <?php endif; ?>
  </div>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">Update Testimonial</h4>
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

      <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="name" class="form-label">Patient Name</label>
          <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? $testimonial['name']); ?>">
        </div>
        <div class="mb-3">
          <label for="profession" class="form-label">Profession</label>
          <input type="text" name="profession" id="profession" class="form-control" required value="<?php echo htmlspecialchars($_POST['profession'] ?? $testimonial['profession']); ?>">
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" id="message" rows="4" class="form-control" required><?php echo htmlspecialchars($_POST['message'] ?? $testimonial['message']); ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Current Image</label><br>
          <?php if (!empty($testimonial['image'])) : ?>
            <img src="../uploads/testimonials/<?php echo htmlspecialchars($testimonial['image']); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" width="100">
          <?php else: ?>
            <span class="text-muted">No image</span>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="img" class="form-label">Change Image (optional)</label>
          <input type="file" name="img" id="img" class="form-control">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Update Testimonial</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


