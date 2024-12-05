<?php
include_once 'header.php';
include_once '../admin/ConnectionSingleton.php';

$idTerrain = $_GET['idTerrain'] ?? '';
$nomTerrain = urldecode($_GET['nomTerrain'] ?? '');
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'reserve') {
        $idTerrain = $_POST['idTerrain'] ?? '';
        $nomTerrain = $_POST['nomTerrain'] ?? '';
        $dateDebut = $_POST['dateDebut'] ?? '';
        $dateFin = $_POST['dateFin'] ?? '';
        $nomReservant = $_POST['nomReservant'] ?? '';
        $numeroTel = $_POST['numeroTel'] ?? '';
        $emailReservant = $_POST['emailReservant'] ?? '';

        $checkStmt = $connection->prepare("SELECT COUNT(*) FROM reservations WHERE idTerrain = ? AND ((dateDebut BETWEEN ? AND ?) OR (dateFin BETWEEN ? AND ?))");
        $checkStmt->bind_param("issss", $idTerrain, $dateDebut, $dateFin, $dateDebut, $dateFin);
        $checkStmt->execute();
        $checkStmt->bind_result($exists);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($exists > 0) {
            $message = "Une réservation existe déjà pour cette période. Veuillez choisir une autre date.";
        } else {
            $stmt = $connection->prepare("INSERT INTO reservations (idTerrain, nomTerrain, dateDebut, dateFin, nomReservant, numeroTel, emailReservant) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $idTerrain, $nomTerrain, $dateDebut, $dateFin, $nomReservant, $numeroTel, $emailReservant);

            if ($stmt->execute()) {
                $message = "Réservation réussie pour : $nomReservant pour le terrain $nomTerrain (ID: $idTerrain) du $dateDebut au $dateFin.";
            } else {
                $message = "Erreur : " . $stmt->error;
            }

            $stmt->close();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'cancel') {
        $idTerrain = $_POST['idTerrain'] ?? '';
        $stmt = $connection->prepare("DELETE FROM reservations WHERE idTerrain = ?");
        $stmt->bind_param("i", $idTerrain);

        if ($stmt->execute()) {
            $message = "Réservation annulée avec succès.";
        } else {
            $message = "Erreur lors de l'annulation de la réservation : " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Formulaire de Réservation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex flex-col h-full justify-between bg-gray-100">
    <div class="container mx-auto mt-8">
        <h1 class="text-4xl font-bold mb-4 text-purple-700 text-center">Formulaire de Réservation</h1>

        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
            <div class="mb-4">
                <label for="idTerrain" class="block text-sm font-medium text-gray-600">ID du Terrain :</label>
                <input type="text" id="idTerrain" name="idTerrain" value="<?php echo htmlspecialchars($idTerrain); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
            </div>

            <div class="mb-4">
                <label for="nomTerrain" class="block text-sm font-medium text-gray-600">Nom du Terrain :</label>
                <input type="text" id="nomTerrain" name="nomTerrain" value="<?php echo htmlspecialchars($nomTerrain); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
            </div>

            <div class="mb-4">
                <label for="dateDebut" class="block text-sm font-medium text-gray-600">Date de Début :</label>
                <input type="datetime-local" id="dateDebut" name="dateDebut" value="<?php echo htmlspecialchars($dateDebut ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="dateFin" class="block text-sm font-medium text-gray-600">Date de Fin :</label>
                <input type="datetime-local" id="dateFin" name="dateFin" value="<?php echo htmlspecialchars($dateFin ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="nomReservant" class="block text-sm font-medium text-gray-600">Nom du Réservant :</label>
                <input type="text" id="nomReservant" name="nomReservant" value="<?php echo htmlspecialchars($nomReservant ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="numeroTel" class="block text-sm font-medium text-gray-600">Numéro de Téléphone :</label>
                <input type="text" id="numeroTel" name="numeroTel" value="<?php echo htmlspecialchars($numeroTel ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="emailReservant" class="block text-sm font-medium text-gray-600">Email :</label>
                <input type="email" id="emailReservant" name="emailReservant" value="<?php echo htmlspecialchars($emailReservant ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="flex justify-center text-center space-x-4 mt-4">
                <button type="submit" name="action" value="reserve" class="bg-purple-500 text-white p-2 rounded-md shadow-2xl w-1/3">Réserver</button>
                <button type="submit" name="action" value="cancel" class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md transition-colors w-1/3">Annuler</button>
            </div>
        </form>
    </div>

</body>
</html>

<?php
include_once 'footer.php'; 

$connection->close();
?>
