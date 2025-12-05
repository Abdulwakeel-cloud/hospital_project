<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

    if(isset($_POST['submit'])){
        $title = trim($_POST['title']);
        $subtitle = trim($_POST['subtitle']);
        $body = trim($_POST['body']);
        $img = $_FILES['img']['name'];

        $error = [];
        $success = "";

        $dir = "../uploads/blog/".basename($img); 

        try {
            if (empty($title) || empty($subtitle) || empty($body) || empty($img)) {
                $error[] = "All fields are required .";
            } else {
                $stmt = $db->prepare("INSERT INTO posts (title, subtitle, body, image) VALUES (:title, :subtitle, :body, :image)");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':subtitle', $subtitle);
                $stmt->bindParam(':body', $body);
                $stmt->bindParam(':image', $img);
                // $stmt->bindParam(':category_id', $category_id);

                $stmt->execute();

                // $error[] = "Post created successfully!";
    
                if (move_uploaded_file($_FILES['img']['tmp_name'], $dir)) {
                   //  Success message before redirect
                $success = " Post created successfully! Redirecting...";
                echo "<script>
                setTimeout(function() {
                    window.location.href = 'http://localhost/hospital_project/admin-panel/testimony-admins/show-posts.php';
                }, 2000);
              </script>";
                }
            }

        } catch (PDOException $e) {
            die("Error creating post: " . $e->getMessage());
        }
    }

          
    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- This file has been downloaded from Bootsnipp.com. Enjoy! -->
    <title>Create Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- <link href="../styles/style.css" rel="stylesheet"> -->
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

    textarea{
        max-width: 100%;
        min-width: 100%;
        border: 2px solid #ccc;
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
  <!-- sidebar -->
    <div class="sidebar">
      <div class="logo">MEDIXAL</div>
      <a href="../index.php">Home</a>
      <a href="../admins/admins.php">Admins</a>
      <a href="../categories-admins/show-categories.php">Categories</a>
      <a href="../services-admins/show-services.php">Services</a>
      <a href="../doctor-admins/show-doctors.php">Doctors</a>
      <a href="show-posts.php" class="active">Posts</a>
      <a href="../patients-say-admins/show-testimonials.php">What Patients Say</a>
      <a href="../patients-admins/show-patients.php">Patients</a>
      <?php if(isset($_SESSION["admin_name"])) : ?>
      <a href="../logout.php" class="logout text-white">Logout</a>
      <?php else: ?>
      <a href="login-admins.php" class="logout text-white">Login</a>
      <?php endif; ?>
    </div>

<div class="main-content">    
    <div class="container-fluid">
       <div class="row justify-content-center">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-5 d-inline">Create Admins</h5>
          <form method="POST" action="" enctype="multipart/form-data">

                <div class="form-outline mb-4 mt-4">     
                  <input type="title" name="title" id="form2Example1" class="form-control" placeholder="Enter your title" />
                 
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="subtitle" name="subtitle" id="form2Example1" class="form-control" placeholder="Enter your subtitle" />
                 
                </div>
                <div class="form-outline mb-4 mt-4">
                  <textarea name="body" placeholder="Post Content" rows="8"></textarea><br>
                </div>

                <input type="file" name="img" id="image-upload" class="form-control mb-2" placeholder="Choose Image">
      


                <!-- Submit button -->
                <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">Create Post</button>

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