<?php 
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

if (isset($_GET["del_id"])) {
    $id = $_GET["del_id"];

    // First, get the service to delete its image
    $select = "SELECT image FROM services WHERE id = :id";
    $stmt = $db->prepare($select);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $service = $stmt->fetch(PDO::FETCH_OBJ);
    
    // Delete the service from database
    $delete = "DELETE FROM services WHERE id = :id";
    $statement = $db->prepare($delete);
    $statement->bindParam(':id', $id);
    $statement->execute();
    
    // Delete the image file if it exists
    if ($service && !empty($service->image)) {
        $image_path = __DIR__ . '/../uploads/services/' . $service->image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    header("Location: show-services.php");
    exit();
}

