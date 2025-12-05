<?php
// Home page data loader
// Centralizes all DB queries used on the public homepage.

require_once __DIR__ . '/dbh.php';

// Fetch doctors with their job/category name
$sql = "
    SELECT 
        d.*, 
        c.category_name AS job_name
    FROM doctors d
    LEFT JOIN categories c ON d.title = c.id
    ORDER BY d.id DESC LIMIT 4
";

$stmt = $db->prepare($sql);
$stmt->execute();
$doctor = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Latest blog posts shown on the homepage (limit to a few for performance)
$select = "SELECT * FROM posts ORDER BY id DESC LIMIT 3";
$statement = $db->prepare($select);
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_OBJ);

// Departments / categories
$categoriesSql = "SELECT * FROM categories ORDER BY id DESC LIMIT 4";
$category_stmt = $db->prepare($categoriesSql);
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_OBJ);

// Services
$servicesSql = "SELECT * FROM services ORDER BY id DESC LIMIT 7";
$service_stmt = $db->prepare($servicesSql);
$service_stmt->execute();
$services = $service_stmt->fetchAll(PDO::FETCH_OBJ);



$tstmt = "SELECT * FROM testimonials ORDER BY id DESC LIMIT 7";
$tstmt = $db->prepare($tstmt);
$tstmt->execute();
$testimonials = $tstmt->fetchAll(PDO::FETCH_ASSOC);
