<?php
include_once '../admin/ConnectionSingleton.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $placeId = $_POST['id']; 

    $deleteSql = "DELETE FROM places_touristiques WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param("i", $placeId); 
    $stmt->execute(); 

    header("Location: adminPlaces.php");
    exit(); 
}
?>
