<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

if (!isset($_GET['del_id'])) {
    header("Location: show-testimonials.php");
    exit();
}

$id = (int) $_GET['del_id'];

// Fetch testimonial to delete image
$stmt = $db->prepare("SELECT image FROM testimonials WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$testimonial = $stmt->fetch(PDO::FETCH_ASSOC);

if ($testimonial) {
    if (!empty($testimonial['image'])) {
        $path = "../uploads/testimonials/" . $testimonial['image'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    $del = $db->prepare("DELETE FROM testimonials WHERE id = :id");
    $del->bindParam(':id', $id, PDO::PARAM_INT);
    $del->execute();
}

header("Location: show-testimonials.php");
exit();


