<?php
include_once 'headerAdmin.php';
include_once 'ConnectionSingleton.php';

if (isset($_GET['id'])) {
    $placeId = $_GET['id'];
    $sql = "SELECT * FROM places_touristiques WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $placeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $place = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $ville = $_POST['ville'];

        // Check if a new image file was uploaded
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/"; // Ensure this directory exists
            $targetFile = $targetDir . basename($_FILES['image']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                echo "Le fichier n'est pas une image.";
                $uploadOk = 0;
            }

            // Allow only certain file formats
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "Seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    // Update the image path in the database
                    $updateSql = "UPDATE places_touristiques SET nom = ?, description = ?, ville = ?, image = ? WHERE id = ?";
                    $updateStmt = $connection->prepare($updateSql);
                    $updateStmt->bind_param("ssssi", $nom, $description, $ville, $targetFile, $placeId);
                    $updateStmt->execute();
                } else {
                    echo "Une erreur s'est produite lors du téléchargement du fichier.";
                }
            }
        } else {
            // Update without changing the image
            $updateSql = "UPDATE places_touristiques SET nom = ?, description = ?, ville = ? WHERE id = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("sssi", $nom, $description, $ville, $placeId);
            $updateStmt->execute();
        }

        header("Location: adminPlaces.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier Place Touristique</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto p-4 max-w-lg mt-10 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-center">Modifier la Place Touristique</h1>
    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Nom:</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($place['nom']) ?>" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Description:</label>
            <textarea name="description"  rows="5" cols="50" maxlength="340" required
                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500"><?= htmlspecialchars($place['description']) ?></textarea>
        </div>
      

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Ville:</label>
            <input type="text" name="ville" value="<?= htmlspecialchars($place['ville']) ?>" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Image:</label>
            <input type="file" name="image" class="w-full">
            <?php if (!empty($place['image'])): ?>
                <p class="mt-2">Image actuelle:</p>
                <img src="<?= htmlspecialchars($place['image']) ?>" alt="Image du lieu" class="w-full h-48 object-cover rounded-md mt-2">
            <?php endif; ?>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded-md shadow-lg hover:bg-purple-600">Enregistrer les modifications</button>
        </div>
    </form>
</div>

</body>
</html>

<?php include_once 'footerAdmin.php'; ?>
