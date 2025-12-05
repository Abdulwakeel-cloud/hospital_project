<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbh = "medixa";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbh", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // if ($db) {
    //     // Connection successful
    //     echo "Database connection established.";
    // }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
