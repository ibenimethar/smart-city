<?php
include_once '../admin/headerAdmin.php';
include_once '../admin/ConnectionSingleton.php';

if (isset($_GET['id'])) {
    $patrimoineId = $_GET['id'];
    $sql = "SELECT * FROM patrimoine WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $patrimoineId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patrimoine = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prixEstime = $_POST['prixEstime'];

        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES['image']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $updateSql = "UPDATE patrimoine SET nomPatrimoine = ?, description = ?, prixEstime = ?, imagePatrimoine = ? WHERE id = ?";
                    $updateStmt = $connection->prepare($updateSql);
                    $updateStmt->bind_param("ssisi", $nom, $description, $prixEstime, $targetFile, $patrimoineId);
                    $updateStmt->execute();
                } else {
                    echo "There was an error uploading the file.";
                }
            }
        } else {
            $updateSql = "UPDATE patrimoine SET nomPatrimoine = ?, description = ?, prixEstime = ? WHERE id = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("ssii", $nom, $description, $prixEstime, $patrimoineId);
            $updateStmt->execute();
        }

        header("Location: adminPatrimoine.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Patrimoine</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto p-4 max-w-lg mt-10 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-center">Modifier le Patrimoine</h1>
    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Nom:</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($patrimoine['nomPatrimoine']) ?>" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Description:</label>
            <textarea name="description"  rows="5" cols="50" maxlength="400" required
                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500"><?= htmlspecialchars($patrimoine['description']) ?></textarea>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Prix Estimé:</label>
            <input type="number" name="prixEstime" value="<?= htmlspecialchars($patrimoine['prixEstime']) ?>" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Image:</label>
            <input type="file" name="image" class="w-full">
            <?php if (!empty($patrimoine['imagePatrimoine'])): ?>
                <p class="mt-2">Image actuelle:</p>
                <img src="<?= htmlspecialchars($patrimoine['imagePatrimoine']) ?>" alt="Image du patrimoine" class="w-full h-48 object-cover rounded-md mt-2">
            <?php endif; ?>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded-md shadow-lg hover:bg-purple-600">Enregistrer les modifications</button>
        </div>
    </form>
</div>

</body>
</html>

<?php include_once '../admin/footerAdmin.php'; ?>
