<?php
// Include the header file
include_once '../admin/headerAdmin.php'; // Adjust path as necessary
include_once '../admin/ConnectionSingleton.php';

// Fetch patrimoine from the database
$query = "SELECT id, nomPatrimoine, description, prixEstime, imagePatrimoine FROM patrimoine";
$result = $connection->query($query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $connection->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Patrimoines</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100 text-center">

    <div class="container mx-auto p-4 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <h1 class="text-4xl font-mono text-purple-600 mb-8 col-span-full text-center border-b-8 border-blue py-6">Liste des Patrimoines</h1>

        <?php
        // Check if there are any patrimoines to display
        if ($result->num_rows > 0) {
            // Fetch and display each patrimoine
            while ($patrimoine = $result->fetch_assoc()) {
                ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                    <img src="<?php echo htmlspecialchars($patrimoine['imagePatrimoine']); ?>" 
                         alt="<?php echo htmlspecialchars($patrimoine['nomPatrimoine']); ?>" 
                         class="w-full h-40 object-cover object-center">
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($patrimoine['nomPatrimoine']); ?></h2>
                        <p class="text-gray-700 mb-2"><strong>Description:</strong> <?php echo htmlspecialchars($patrimoine['description']); ?></p>
                        <p class="text-gray-800"><strong>Prix Estimé:</strong> <?php echo htmlspecialchars($patrimoine['prixEstime']); ?></p>
                        <div class="mt-4">
                            <a href="editPatrimoine.php?id=<?php echo $patrimoine['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Modifier</a>
                            <a href="deletePatrimoine.php?id=<?php echo $patrimoine['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Supprimer</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-gray-700 col-span-full">Aucun patrimoine trouvé.</p>';
        }

        // Free result set
        $result->free();
        ?>

    </div>

</body>
</html>

<?php
// Include the footer file
include_once '../admin/footerAdmin.php';
$connection->close(); // Close the database connection
?>
