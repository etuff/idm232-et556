<?php
function getDBConnection() {
    // Direct MAMP connection settings
    $host = 'localhost';
    $port = 8889;  
    $user = 'root';
    $pass = 'root';
    $dbname = 'recipes_db';
    
    // Create connection with port
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    return $conn;
}
?>