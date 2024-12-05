<?php
include_once '../admin/ConnectionSingleton.php'; 

$connection = new mysqli('localhost', 'root', '', 'smartcity');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_GET['id'])) {
    $idToDelete = $_GET['id'];

    $deleteSql = "DELETE FROM signalements WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param('i', $idToDelete);

    if ($stmt->execute()) {
        header("Location: reclamations.php?message=Reclamation supprimée avec succès!");
        exit();
    } else {
        echo "Erreur lors de la suppression: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Aucune réclamation spécifiée pour la suppression.";
}

$connection->close();
?>
