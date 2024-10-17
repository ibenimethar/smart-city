<?php
include_once 'header.php';
// Processing form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomTerrain = $_POST['nomTerrain'] ?? '';
    $dateDebut = $_POST['dateDebut'] ?? '';
    $dateFin = $_POST['dateFin'] ?? '';
    $nomReservant = $_POST['nomReservant'] ?? '';
    $numeroTel = $_POST['numeroTel'] ?? '';

    // Process the form data (e.g., save to database)
    $message = "Reservation received: $nomReservant for terrain $nomTerrain from $dateDebut to $dateFin.";
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Reservation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex flex-col h-full justify-between bg-gray-100">
    <div class="container mx-auto mt-8">
        <h1 class="text-4xl font-bold mb-4 text-purple-700 text-center">Reservation Form</h1>

        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md" style="height: 560px;">
            <div class="mb-4">
                <label for="nomTerrain" class="block text-sm font-medium text-gray-600">Terrain Name:</label>
                <select id="nomTerrain" name="nomTerrain" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                    <option value="Terrain Mabrouka" <?php if (isset($nomTerrain) && $nomTerrain == 'Terrain Mabrouka') echo 'selected'; ?>>Terrain Mabrouka</option>
                    <option value="Terrain Moulay Abdellah" <?php if (isset($nomTerrain) && $nomTerrain == 'Terrain Moulay Abdellah') echo 'selected'; ?>>Terrain Moulay Abdellah</option>
                    <option value="Grand Terrain Casablanca" <?php if (isset($nomTerrain) && $nomTerrain == 'Grand Terrain Casablanca') echo 'selected'; ?>>Grand Terrain Casablanca</option>
                    <option value="Terrain Olympique Asfi" <?php if (isset($nomTerrain) && $nomTerrain == 'Terrain Olympique Asfi') echo 'selected'; ?>>Terrain Olympique Asfi</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="dateDebut" class="block text-sm font-medium text-gray-600">Start Date:</label>
                <input type="datetime-local" id="dateDebut" name="dateDebut" value="<?php echo htmlspecialchars($dateDebut ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="dateFin" class="block text-sm font-medium text-gray-600">End Date:</label>
                <input type="datetime-local" id="dateFin" name="dateFin" value="<?php echo htmlspecialchars($dateFin ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="nomReservant" class="block text-sm font-medium text-gray-600">Reservant Name:</label>
                <input type="text" id="nomReservant" name="nomReservant" value="<?php echo htmlspecialchars($nomReservant ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="mb-4">
                <label for="numeroTel" class="block text-sm font-medium text-gray-600">Phone Number:</label>
                <input type="text" id="numeroTel" name="numeroTel" value="<?php echo htmlspecialchars($numeroTel ?? ''); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            </div>

            <div class="flex justify-center text-center space-x-4 mt-4">
                <button type="submit" class="bg-purple-500 text-white p-2 rounded-md shadow-2xl w-1/3">Réserver</button>
                <a href="./" class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md transition-colors w-1/3">Annuler</a>
            </div>
        </form>
    </div>

    <?php
    include_once 'footer.php';
    ?>
</body>
</html>
