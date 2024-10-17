<?php 
$host = 'localhost';
$dbname = 'smartcity'; 
$db_username = 'root'; 
$db_password = ''; 

// Create a new connection using MySQLi
$connection = new mysqli($host, $db_username, $db_password, $dbname);

// Check for connection errors
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>