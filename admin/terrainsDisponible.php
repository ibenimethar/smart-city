<?php
// Include the header file
include_once '../user/header.php'; // Adjust path as necessary
include_once '../admin/ConnectionSingleton.php'; 

// Fetch terrains from the database
$sql = "SELECT * FROM terrains";
$result = $connection->query($sql);

$terrainsDisponibles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $terrainsDisponibles[] = $row; // Store each terrain in the array
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terrains Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100">

    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-8">Terrains Disponibles</h1>

        <?php if (!empty($terrainsDisponibles)) { ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 justify-center items-center">
                <?php foreach ($terrainsDisponibles as $terrain) { ?>
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg mb-4">
                        <img src="<?= $terrain['image'] ?>" alt="Image du terrain" class="w-full h-40 object-cover object-center">
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2"><?= $terrain['nom'] ?></h2>
                            <p class="text-gray-700 mb-2"><strong>Description:</strong> <?= $terrain['description'] ?></p>
                            <p class="text-gray-700 mb-2"><strong>Disponibilité:</strong> <?= $terrain['disponibilite'] ? 'Disponible' : 'Indisponible' ?></p>
                            <a href="../user/reservationForm.php?idTerrain=<?= $terrain['id'] ?>&nomTerrain=<?= urlencode($terrain['nom']) ?>" class="bg-purple-500 text-white p-2 rounded-md shadow-2xl inline-block">Réserver</a>


                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="text-gray-700">Aucun terrain disponible pour le moment.</p>
        <?php } ?>
    </div>

</body>
</html>

<?php
// Include the footer file
include_once '../user/footer.php';
$connection->close(); // Close the database connection
?>
