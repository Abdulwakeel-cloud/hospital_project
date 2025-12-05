<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Initialize errors array outside the POST block if you intend to display them later
$errors = []; 


if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])){
    
    // 1. Sanitize and Get Inputs
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // 2. Input Validation (Must happen BEFORE database query)
    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } 

    // 3. Database Query and Authentication (Only proceed if no input errors)
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("SELECT id, username, password FROM admins WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if user was found AND password is correct
            if ($user && password_verify($password, $user['password'])) {
                
                // 4. SUCCESS: Set Session and Redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['username'];
                header("Location: ../index.php");
                exit(); // Crucial to stop execution
                
            } else {
                // Generic error for security
                $errors[] = "Invalid email or password.";
            }

        } catch (PDOException $e) {
            die("Error during login: " . $e->getMessage()); 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../styles/style.css" rel="stylesheet">
  
</head>
<body>

  <div class="login-container">
    <div class="card">
      <div class="card-header">
        <h4>Admin Login</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="">
          <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email"  />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label fw-bold">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password"  />
          </div>

          <button type="submit" name="submit" class="btn btn-primary mb-3">Login</button>

          <p class="text-center text-muted small">
            <a href="../index.php">Back to Home</a>
          </p>
        </form>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger mt-3">
            <?php foreach ($errors as $msg) echo $msg . "<br>"; ?>
          </div>
          <?php endif; ?>
      </div>
    </div>
  </div>

  <!--  Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>