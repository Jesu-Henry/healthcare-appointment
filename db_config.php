<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lil_care";

try {
    $pdo_temp = new PDO("mysql:host=$servername;charset=utf8mb4", $username, $password);
    $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
} catch(PDOException $e) {
    // Optional: log or echo "Database creation failed: " . $e->getMessage();
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
