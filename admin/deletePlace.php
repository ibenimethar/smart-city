<?php
include_once '../admin/ConnectionSingleton.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $placeId = $_POST['id']; // Get the place ID from the form submission

    // Prepare the SQL statement to delete the place
    $deleteSql = "DELETE FROM places_touristiques WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param("i", $placeId); // Bind the place ID as an integer
    $stmt->execute(); // Execute the deletion

    // Redirect back to the admin places page
    header("Location: adminPlaces.php");
    exit(); // Ensure no further code is executed after redirection
}
?>
