<?php
// Include the header file
include_once 'headerAdmin.php';
include_once 'ConnectionSingleton.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomPatrimoine = $_POST['nomPatrimoine'];
    $description = $_POST['description'];
    $prixEstime = $_POST['prixEstime'];
    $imagePatrimoine = $_FILES['imagePatrimoine']['name'];
    $tempname = $_FILES['imagePatrimoine']['tmp_name'];
    $targetDir = "uploads/"; // Directory to store images
    $targetFile = $targetDir . basename($imagePatrimoine);
    

    // Move the uploaded file to the target directory
    if (move_uploaded_file($tempname, $targetFile)) {
        // Prepare and bind
        $stmt = $connection->prepare("INSERT INTO patrimoine (`nomPatrimoine`, `description`, `prixEstime`, `imagePatrimoine`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $nomPatrimoine, $description, $prixEstime, $targetFile);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<div class='text-center text-green-500'>Patrimoine ajouté avec succès!</div>";
        } else {
            echo "<div class='text-center text-red-500'>Erreur: " . $stmt->error . "</div>";
        }

        // Close the statement
        $stmt->close();
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
    <title>Ajout de Patrimoine</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100">

    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-8 text-purple-700 text-center">Ajout de Patrimoine</h1>
        <form action="" method="post" enctype="multipart/form-data" class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
            
            <div class="mb-4">
                <label for="nomPatrimoine" class="block text-sm font-medium text-gray-600">Nom du patrimoine:</label>
                <input type="text" id="nomPatrimoine" name="nomPatrimoine" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-600">Description:</label>
                <textarea id="description" name="description" required class="mt-1 p-2 border border-gray-300 rounded-md w-full"></textarea>
            </div>

            <div class="mb-4">
                <label for="prixEstime" class="block text-sm font-medium text-gray-600">Prix Estimé:</label>
                <input type="number" id="prixEstime" name="prixEstime" step="0.01" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="imagePatrimoine" class="block text-sm font-medium text-gray-600">Image du patrimoine:</label>
                <input type="file" id="imagePatrimoine" name="imagePatrimoine" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="flex items-center justify-center mt-6">
                <button type="submit" class="bg-purple-500 text-white p-2 rounded-md shadow-2xl w-1/3">Ajouter</button>
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
