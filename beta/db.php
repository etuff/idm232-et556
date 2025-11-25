<?php
// db.php - PDO connection
$DB_HOST = '127.0.0.1';
$DB_NAME = 'recipes_db';
$DB_USER = 'root';
$DB_PASS = ''; 

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production, hide the detailed error
    die("Database connection failed: " . $e->getMessage());
}
