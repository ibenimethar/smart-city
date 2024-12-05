<?php 
$host = 'localhost';
$dbname = 'smartcity'; 
$db_username = 'root'; 
$db_password = ''; 

$connection = new mysqli($host, $db_username, $db_password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>