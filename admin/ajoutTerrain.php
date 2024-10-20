<?php
// Include the header file
include_once 'headerAdmin.php';
include_once 'ConnectionSingleton.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0; // Checkbox value
    $imageTerrain = $_FILES['imageTerrain']['name'];
    $tempname= $_FILES['imageTerrain']['tmp_name'];
    $targetDir = "uploads/"; // Directory to store images
    $targetFile = $targetDir . $imageTerrain;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($tempname,$targetFile)) {
        // Insert into database
        $sql = "INSERT INTO terrains (`nom`, `description`, `image`, `disponibilite`) VALUES ('$nom', '$description', '$targetFile', '$disponibilite')";
        
        if ($connection->query($sql) === TRUE) {
            echo "<div class='text-center text-green-500'>Terrain ajouté avec succès!</div>";
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
    <title>Ajout de Terrain</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100">

    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-8 text-blue-500 text-center">Ajout de Terrain</h1>
        <form action="" method="post" enctype="multipart/form-data" class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">

            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-600">Nom du terrain:</label>
                <select id="nom" name="nom" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                    <option value="Terrain Agadir">Terrain Agadir</option>
                    <option value="Terrain Tanger">Terrain Tanger</option>
                    <option value="Terrain Rabat">Terrain Rabat</option>
                    <option value="Terrain Ouarzazat">Terrain Ouarzazat</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-600">Description:</label>
                <textarea id="description" name="description" required class="mt-1 p-2 border border-gray-300 rounded-md w-full"></textarea>
            </div>

            <div class="mb-4">
                <label for="imageTerrain" class="block text-sm font-medium text-gray-600">Image du terrain:</label>
                <input type="file" id="imageTerrain" name="imageTerrain" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="disponibilite" class="flex items-center">
                    <input type="checkbox" id="disponibilite" name="disponibilite" class="form-checkbox">
                    <span class="ml-2 text-sm">Disponibilité</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-6">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md shadow-2xl w-1/3">Ajouter</button>
            </div>

        </form>
    </div>

</body>
<br><br>
</html>

<?php
// Include the footer file
include_once 'footerAdmin.php';

// Close the connection
$connection->close();
?>
