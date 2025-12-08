<?php
function getDBConnection() {
    // Direct MAMP connection settings
    $host = 'localhost';
    $port = 8889;  // MAMP on Windows uses 8889
    $user = 'root';
    $pass = 'root';
    $dbname = 'recipes_db';
    
    // Create connection with port
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($conn->connect_error) {
        die("MySQL Connection Failed!<br>" .
            "Error: " . $conn->connect_error . "<br>" .
            "Settings tried:<br>" .
            "- Host: $host<br>" .
            "- Port: $port<br>" .
            "- Username: $user<br>" .
            "- Database: $dbname<br><br>" .
            "Please check:<br>" .
            "1. MAMP MySQL is running (green light)<br>" .
            "2. Database 'recipes_db' exists in phpMyAdmin<br>" .
            "3. Password is correct (try empty password if 'root' doesn't work)");
    }
    
    return $conn;
}
?>