<?php
require_once "../../includes/session_config.php";
require_once "../../includes/dbh.php";

// Security: Check if user is logged in as admin
if (!isset($_SESSION["admin_name"])) {
    header("Location: ../admins/login-admins.php");
    exit();
}

if (!isset($_GET['del_id'])) {
    header("Location: show-patients.php");
    exit();
}

$id = (int) $_GET['del_id'];

$del = $db->prepare("DELETE FROM patients WHERE id = :id");
$del->bindParam(':id', $id, PDO::PARAM_INT);
$del->execute();

header("Location: show-patients.php");
exit();


