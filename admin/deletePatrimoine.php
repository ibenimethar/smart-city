<?php
include_once '../admin/ConnectionSingleton.php';

if (isset($_GET['id'])) {
    $patrimoineId = $_GET['id'];

    $deleteSql = "DELETE FROM patrimoine WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param("i", $patrimoineId);

    if ($stmt->execute()) {
        header("Location: adminPatrimoine.php?message=Patrimoine supprimé avec succès");
        exit();
    } else {
        echo "Erreur lors de la suppression du patrimoine: " . $connection->error;
    }
}

$connection->close();
?>
