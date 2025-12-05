<?php
// Blog page data loader with pagination support.

require_once __DIR__ . '/dbh.php';

// Pagination settings
$perPage = 6;
$page = isset($_GET['page']) && ctype_digit($_GET['page']) && (int) $_GET['page'] > 0
    ? (int) $_GET['page']
    : 1;

// Count total posts
$countStmt = $db->prepare("SELECT COUNT(*) FROM posts");
$countStmt->execute();
$totalPosts = (int) $countStmt->fetchColumn();

$totalPages = $totalPosts > 0 ? (int) ceil($totalPosts / $perPage) : 1;

// Clamp current page within valid range
if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

// Fetch paginated posts
$select = "SELECT * FROM posts ORDER BY id DESC LIMIT :limit OFFSET :offset";
$statement = $db->prepare($select);
$statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
$statement->bindValue(':offset', $offset, PDO::PARAM_INT);
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_OBJ);


