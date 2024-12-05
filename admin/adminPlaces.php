<?php
include_once 'headerAdmin.php'; 
include_once 'ConnectionSingleton.php';

$query = "SELECT id, nom, description, ville, image FROM places_touristiques";
$result = $connection->query($query);

if (!$result) {
    die("Query failed: " . $connection->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Places Touristiques</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100 text-center">

    <div class="container mx-auto p-4 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <h1 class="text-4xl font-mono text-purple-600 mb-8 col-span-full text-center border-b-8 border-blue py-6">Gestion des Places Touristiques</h1>

        <?php
        if ($result->num_rows > 0) {
            while ($placeTouristique = $result->fetch_assoc()) {
                ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                    <img src="<?php echo htmlspecialchars($placeTouristique['image']); ?>" 
                         alt="<?php echo htmlspecialchars($placeTouristique['nom']); ?>" 
                         class="w-full h-40 object-cover object-center">
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($placeTouristique['nom']); ?></h2>
                        <p class="text-gray-700 mb-2"><strong>Ville:</strong> <?php echo htmlspecialchars($placeTouristique['ville']); ?></p>
                        <p class="text-gray-800"><?php echo htmlspecialchars($placeTouristique['description']); ?></p>

                        <a href="editPlace.php?id=<?php echo $placeTouristique['id']; ?>" class="bg-blue-500 text-white p-2 rounded-md mt-2 inline-block">Modifier</a>

                        <form action="deletePlace.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette place touristique ?');" class="inline-block">
                            <input type="hidden" name="id" value="<?php echo $placeTouristique['id']; ?>">
                            <button type="submit" class="bg-red-500 text-white p-2 rounded-md mt-2">Supprimer</button>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-gray-700 col-span-full">Aucune place touristique trouvée.</p>';
        }

        $result->free();
        ?>

    </div>

</body>
</html>

<?php
include_once 'footerAdmin.php';
$connection->close(); 
?>
