<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Fetch all doctors, joining with the CATEGORIES table to get the category name
$sql = "
SELECT 
d.*, 
c.category_name AS job_name  -- Selects the category name and aliases it as job_name
FROM doctors d
LEFT JOIN categories c ON d.title = c.id  -- Joins d.title (the category ID) to c.id
ORDER BY d.id DESC
";

$stmt = $db->prepare($sql);
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Panel - Categories</title>
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
<a href="../index.php">Home</a>
<a href="../admins/admins.php">Admins</a>
<a href="../categories-admins/show-categories.php">Categories</a>
<a href="../services-admins/show-services.php">Services</a>
<a href="show-doctors.php" class="active">Doctors</a>
<a href="../testimony-admins/show-posts.php">Posts</a>
<a href="../patients-say-admins/show-testimonials.php">What Patients Say</a>
<a href="../patients-admins/show-patients.php">Patients</a>
<?php if(isset($_SESSION["admin_name"])) : ?>
<a href="../logout.php" class="logout text-white">Logout</a>
<?php   else :   ?>
<a href="../admins/login-admins.php" class="logout text-white">Login</a>
<?php endif; ?>
</div>

<div class="main-content">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">Doctors</h5>
  <?php if(isset($_SESSION["admin_name"])) : ?>
    <a href="add-doctors.php" class="btn btn-primary">Add Doctors</a>
          <?php else : ?>
<span></span>
<?php endif; ?>

</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">FirstName</th>
                    <th scope="col">LastName</th> 
                    <th scope="col">Email</th> 
                    <th scope="col">Job Position</th> 
                    <th scope="col">Twitter Handle</th> 
                    <th scope="col">LinkedIn Handle</th> 
                    <th scope="col">Facebook Handle</th> 
                    <th scope="col">Image</th> 
                    <th scope="col">Update</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>

            <tbody>
              <?php if(isset($_SESSION["admin_name"])) : ?>
                <?php if (!empty($doctors)): ?>
                    <?php $i = 1; ?> 
                    <?php foreach ($doctors as $doctor): ?>
                        <tr>
                            <th scope="row"><?php echo $i++; ?></th> 
                            <td><?php echo htmlspecialchars($doctor["firstname"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($doctor["lastname"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($doctor["email"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($doctor["job_name"] ?? "N/A"); ?></td>
                            <td><?php echo htmlspecialchars($doctor["twitter"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($doctor["linkedin"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($doctor["facebook"] ?? ""); ?></td>
                            
                            <td><img src="../uploads/profile/<?php echo htmlspecialchars($doctor["image"] ?? "default.png"); ?>" 
                                      alt="<?php echo htmlspecialchars($doctor["firstname"] ?? "Doctor Image"); ?>" 
                                      width="70px" height="70px" style="object-fit: cover; border-radius: 5px;">
                            </td>
                            
                            <td><a href="update-doctors.php?upd_id=<?php echo htmlspecialchars($doctor["id"]); ?>" class="btn btn-warning text-white">Update</a></td>
                            <td><a href="delete.php?del_id=<?php echo htmlspecialchars($doctor["id"]); ?>" class="btn btn-danger">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" class="text-center text-muted">No Doctors Added.</td></tr> 
                    <?php endif; ?>
                    <?php else: ?>
                      <tr><td colspan="11" class="text-center text-muted">Please log in to view doctors.</td></tr>
              <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>