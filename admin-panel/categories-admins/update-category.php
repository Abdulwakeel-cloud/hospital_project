<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

$errors = [];
$id = null;
$categories = null;

// Redirect if no ID
if (!isset($_GET["upd_id"])) {
    header('Location: show-categories.php');
    exit();
}

// --- Fetch Existing Record (Read) ---
if (isset($_GET["upd_id"])) {
    $id = $_GET["upd_id"];
    $select = "SELECT * FROM categories WHERE id = :id";
    $statement = $db->prepare($select);
    $statement->bindParam(":id", $id);
    $statement->execute();
    $categories = $statement->fetch(PDO::FETCH_OBJ);
    // If no category found with that ID, redirect or show error
    if (!$categories) {
        header("Location: show-categories.php");
        exit();
    }
}
// --- 2. Handle POST Submission (Update) ---
if (isset($_POST["submit"])) {
    // Ensure we have the ID from POST or GET
    if (!isset($id) && isset($_POST["id"])) {
        $id = $_POST["id"];
    }
    if (!$id) {
        $errors[] = "Category ID is required.";
        header("Location: show-categories.php");
        exit();
    }
    
    // Fetch category data if not already loaded
    if (!$categories) {
        $select = "SELECT * FROM categories WHERE id = :id";
        $statement = $db->prepare($select);
        $statement->bindParam(":id", $id);
        $statement->execute();
        $categories = $statement->fetch(PDO::FETCH_OBJ);
        if (!$categories) {
            $errors[] = "Category not found.";
            header("Location: show-categories.php");
            exit();
        }
    }
    
    $name = trim($_POST["name"]);
    // --- 3. BASIC VALIDATION ---
    if (empty($name)) {
        $errors[] = "Category name is required.";
    }
    // Upload directory (always define this)
    $upload_dir = __DIR__ . '/../uploads/services/';
    $image_uploaded = isset($_FILES['img']) && !empty($_FILES['img']['name']) && $_FILES['img']['error'] === UPLOAD_ERR_OK;
    $new_img_name = null;
    $img_tmp_name = null;
    $targetPath = null;
    
    if ($image_uploaded) {
        $img_tmp_name = $_FILES['img']['tmp_name'];
        $img_extension = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($img_extension, $allowed)) {
            $errors[] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            // Generate unique filename and define path
            $new_img_name = uniqid("Service_", true) . "." . $img_extension;
            $targetPath = $upload_dir . $new_img_name;
        }
    }
    // --- EXECUTE UPDATE (Only if no errors occurred) ---
    if (empty($errors)) {
        try {
            // Store old image name before update
            $old_img_name = $categories->img ?? null;
            
            // Dynamically build the SQL query
            $sql = "UPDATE categories SET category_name = :cat_name";
            if ($image_uploaded) {
                $sql .= ", img = :img";
            }
            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            // Bind common parameters
            $stmt->bindParam(':cat_name', $name);
            $stmt->bindParam(':id', $id);
            // Bind image parameter only if uploaded
            if ($image_uploaded) {
                $stmt->bindParam(':img', $new_img_name);
            }
            $stmt->execute();
            
            // Move uploaded file only after successful DB update
            if ($image_uploaded) {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                if (move_uploaded_file($img_tmp_name, $targetPath)) {
                    // Delete old image after successful upload
                    if (!empty($old_img_name) && file_exists($upload_dir . $old_img_name)) {
                        unlink($upload_dir . $old_img_name);
                    }
                } else {
                    // Rollback: if file move fails, we should revert the database change
                    // For now, just add error (in production, consider transaction rollback)
                    $errors[] = "Database updated but failed to move image. Please try again.";
                }
            }
            if (empty($errors)) {
                header('Location: show-categories.php');
                exit();
            }
        } catch (Exception $e) {
            $errors[] = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    
    <title>Update Category</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- <link href="../styles/style.css" rel="stylesheet"> -->
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

    .sidebar a:active {
      transform: translateX(4px) scale(0.98);
    }

    .sidebar .logout {
      margin-top: auto;
      background-color: #dc3545;
      text-align: center;
      border-radius: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .sidebar .logout::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, #dc3545, #c82333);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .sidebar .logout:hover {
      background: linear-gradient(135deg, #dc3545, #c82333);
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .sidebar .logout:active {
      transform: translateY(0) scale(0.98);
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

    .btn-primary:active {
      transform: translateY(-1px) scale(1.02);
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
      cursor: pointer;
    }

    button::after {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.15);
      transform: translate(-50%, -50%);
      transition: width 0.5s ease, height 0.5s ease;
    }

    button:hover{
      background: linear-gradient(135deg, #2b8c8a, #3eb5b3);
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 6px 20px rgba(43, 140, 138, 0.4);
    }

    button:hover::after {
      width: 200px;
      height: 200px;
    }

    button:active {
      transform: translateY(0) scale(1);
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

    .btn-danger:active, .btn-warning:active {
      transform: translateY(0) scale(1);
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

    .error-box ul {
      margin: 0;
      padding-left: 1.5rem;
    }

    .error-box li {
      margin: 0.5rem 0;
      transition: all 0.2s ease;
    }

    .error-box li:hover {
      transform: translateX(4px);
      color: #a00;
    }

    /* Form input hover effects */
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

    /* Image preview hover */
    .form-outline img {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
    }

    .form-outline img:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      border-color: #3eb5b3;
    }
  </style>

  <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div id="wrapper">
    <div class="sidebar">
      <div class="logo">MEDIXAL</div>
      <a href="../index.php">Home</a>
      <a href="../admins/admins.php">Admins</a>
      <a href="show-categories.php" class="active">Categories</a>
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
    <div class="main-content">
    <div class="container-fluid">
       <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-5 d-inline">Update Categories</h5>
          <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($categories->id ?? ''); ?>">
                
                <!-- Category Name input -->
                <div class="form-outline mb-4 mt-4">
                  <label for="category-name" class="form-label fw-bold">Category Name</label>
                  <input type="text" name="name" id="category-name" class="form-control" placeholder="Category name" value="<?php echo htmlspecialchars($categories->category_name ?? ''); ?>" required />
                </div>
                
                <!-- Image input -->
                <div class="form-outline mb-4">
                  <label for="image-upload" class="form-label fw-bold">Category Image</label>
                  <?php if (!empty($categories->img)): ?>
                    <div class="mb-2">
                      <p class="text-muted small">Current Image:</p>
                      <img src="../uploads/services/<?php echo htmlspecialchars($categories->img); ?>" 
                           alt="Current category image" 
                           width="150" height="150" 
                           style="object-fit: cover; border-radius: 5px; border: 2px solid #ddd;" 
                           class="mb-2">
                    </div>
                  <?php endif; ?>
                  <input type="file" name="img" id="image-upload" class="form-control" accept="image/jpeg,image/jpg,image/png,image/gif">
                  <small class="form-text text-muted">Leave empty to keep current image. Only JPG, JPEG, PNG, and GIF are allowed.</small>
                </div>

      
                <!-- Submit button -->
                <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">update</button>

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
<script type="text/javascript">

</script>
</body>
</html>