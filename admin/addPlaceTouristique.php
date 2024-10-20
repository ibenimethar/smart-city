<?php
// Include the header file
include_once 'headerAdmin.php';
include_once 'ConnectionSingleton.php'; // Assuming this file initializes your database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $ville = $_POST['ville'];
    $image = $_FILES['image']['name'];
    $tempname = $_FILES['image']['tmp_name'];
    $targetDir = "uploads/"; // Directory to store images
    $targetFile = $targetDir . basename($image);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($tempname, $targetFile)) {
        // Insert into database
        $sql = "INSERT INTO places_touristiques (`nom`, `description`, `ville`, `image`) VALUES ('$nom', '$description', '$ville', '$targetFile')";

        if ($connection->query($sql) === TRUE) {
            echo "<div class='text-center text-green-500'>Place touristique ajoutée avec succès!</div>";
        } else {
            echo "<div class='text-center text-red-500'>Erreur: " . $sql . "<br>" . $connection->error . "</div>";
        }
    } else {
        echo "<div class='text-center text-red-500'>Erreur lors de l'upload de l'image.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Place Touristique</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex flex-col h-full justify-between bg-gray-100">
	
    <div class="container mx-auto mt-8">
        <h1 class="text-4xl font-bold mb-4 text-blue-500 text-center">Ajouter une Place Touristique</h1>
        <form action="" method="post" enctype="multipart/form-data" class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">

            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-600">Nom:</label>
                <input type="text" id="nom" name="nom" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-600">Description:</label>
                <textarea id="description" name="description" required class="mt-1 p-2 border border-gray-300 rounded-md w-full"></textarea>
            </div>

            <div class="mb-4">
                <label for="ville" class="block text-sm font-medium text-gray-600">Ville:</label>
                <select id="ville" name="ville" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                    <option value="Marrakech">Marrakech</option>
                    <option value="Essaouira">Essaouira</option>
                    <option value="Casablanca">Casablanca</option>
                    <option value="Fes">Fes</option>
                    <option value="Rabat">Rabat</option>
                    <option value="Agadir">Agadir</option>
                    <option value="Tanger">Tanger</option>
                    <option value="Chefchaouen">Chefchaouen</option>
                    <option value="Ouarzazate">Ouarzazate</option>
                    <!-- Ajoutez d'autres options selon vos besoins -->
                </select>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-600">Image:</label>
                <input type="file" id="image" name="image" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="flex items-center justify-center mt-6">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md shadow-2xl w-1/3">Ajouter</button>
            </div>

        </form>
    </div>
    <br><br>

</body>
</html>

<?php
// Include the footer file
include_once 'footerAdmin.php';

// Close the connection
$connection->close();
?>
