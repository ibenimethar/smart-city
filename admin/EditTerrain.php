<?php
include_once 'headerAdmin.php';
include_once 'ConnectionSingleton.php';

if (isset($_GET['id'])) {
    $terrainId = $_GET['id'];
    $sql = "SELECT * FROM terrains WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $terrainId);
    $stmt->execute();
    $result = $stmt->get_result();
    $terrain = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;

        // Check if a new image file was uploaded
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/"; // Make sure this directory exists
            $targetFile = $targetDir . basename($_FILES['image']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Allow only certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    // Update the image path in the database
                    $updateSql = "UPDATE terrains SET nom = ?, description = ?, disponibilite = ?, image = ? WHERE id = ?";
                    $updateStmt = $connection->prepare($updateSql);
                    $updateStmt->bind_param("ssisi", $nom, $description, $disponibilite, $targetFile, $terrainId);
                    $updateStmt->execute();
                } else {
                    echo "There was an error uploading the file.";
                }
            }
        } else {
            // Update without changing the image
            $updateSql = "UPDATE terrains SET nom = ?, description = ?, disponibilite = ? WHERE id = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("ssii", $nom, $description, $disponibilite, $terrainId);
            $updateStmt->execute();
        }

        header("Location: adminTerrains.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier Terrain</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto p-4 max-w-lg mt-10 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-center">Modifier le Terrain</h1>
    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Nom:</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($terrain['nom']) ?>" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Description:</label>
            <textarea name="description" required
                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500"><?= htmlspecialchars($terrain['description']) ?></textarea>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="disponibilite" <?= $terrain['disponibilite'] ? 'checked' : '' ?>
                   class="mr-2">
            <label class="text-gray-700">Disponible</label>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Image:</label>
            <input type="file" name="image" class="w-full">
            <?php if (!empty($terrain['image'])): ?>
                <p class="mt-2">Image actuelle:</p>
                <img src="<?= htmlspecialchars($terrain['image']) ?>" alt="Image du terrain" class="w-full h-48 object-cover rounded-md mt-2">

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
