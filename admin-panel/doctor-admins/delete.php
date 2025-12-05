<?php 
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

if (isset($_GET["del_id"])) {
    $id = $_GET["del_id"];



    // Delete the post
    $delete = "DELETE FROM doctors WHERE id = :id";
    $statement = $db->prepare($delete);
    $statement->bindParam(':id', $id);
    $statement->execute();
    header("Location: http://localhost/hospital_project/admin-panel/doctor-admins/show-doctors.php");
    exit();
} 