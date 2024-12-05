<?php
include_once '../admin/ConnectionSingleton.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $terrainId = $_POST['id'];
    $deleteSql = "DELETE FROM terrains WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param("i", $terrainId);
    $stmt->execute();
    

    //

    header("Location: adminTerrains.php");
}
?>
