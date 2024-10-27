<?php
// Include database connection
include_once '../admin/ConnectionSingleton.php'; 

// Create the database connection
$connection = new mysqli('localhost', 'root', '', 'smartcity');

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the id is set in the GET request
if (isset($_GET['id'])) {
    $idToDelete = $_GET['id'];

    // Prepare the SQL delete statement
    $deleteSql = "DELETE FROM signalements WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param('i', $idToDelete);

    // Execute the delete operation
    if ($stmt->execute()) {
        // Redirect back to the main reclamation list page with a success message
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
