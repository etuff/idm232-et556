<?php
// db.php — central database loader

$config = require __DIR__ . '/.htaccess.php';

$conn = new mysqli(
    $config['db_host'],
    $config['db_user'],
    $config['db_pass'],
    $config['db_name']
);

if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("Database connection error.");
}

$conn->set_charset('utf8mb4');
