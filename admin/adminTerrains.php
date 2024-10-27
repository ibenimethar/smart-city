<?php
// Include the header file
include_once 'headerAdmin.php'; // Adjust path as necessary
include_once 'ConnectionSingleton.php'; 

// Fetch terrains from the database
$sql = "SELECT * FROM terrains";
$result = $connection->query($sql);

$terrains = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $terrains[] = $row; // Store each terrain in the array
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Terrains</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Additional styles for hover effects */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-sans bg-gray-100">

<div class="container mx-auto p-4">
    <h1 class="text-4xl font-bold mb-8  text-blue-500  text-center">Gestion des Terrains</h1>

    <?php if (!empty($terrains)) { ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($terrains as $terrain) { ?>
                <div class="card bg-white rounded-lg overflow-hidden shadow-lg mb-4 transition-transform duration-300">
                    <img src="<?= htmlspecialchars($terrain['image']) ?>" alt="Image du terrain" class="w-full h-40 object-cover object-center cursor-pointer" data-modal-target="modal-<?= $terrain['id'] ?>" onclick="openModal('modal-<?= $terrain['id'] ?>')">
                    <div class="p-4">
                        <h2 class="text-lg font-bold mb-2"><?= htmlspecialchars($terrain['nom']) ?></h2>
                        <p class="text-gray-700 mb-2"><strong>Description:</strong> <?= htmlspecialchars($terrain['description']) ?></p>
                        <p class="text-gray-700 mb-2"><strong>Disponibilité:</strong> <?= $terrain['disponibilite'] ? 'Disponible' : 'Indisponible' ?></p>

                        <div class="flex space-x-2 mt-4">
                            <a href="editTerrain.php?id=<?= $terrain['id'] ?>" class="bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 transition">Modifier</a>
                            <form action="deleteTerrain.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce terrain ?');">
                                <input type="hidden" name="id" value="<?= $terrain['id'] ?>">
                                <button type="submit" class="bg-red-500 text-white p-2 rounded-md hover:bg-red-600 transition">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal for image preview -->
                <div id="modal-<?= $terrain['id'] ?>" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
                    <div class="bg-white rounded-lg p-4 relative">
                        <span class="absolute top-0 right-0 p-2 cursor-pointer" onclick="closeModal('modal-<?= $terrain['id'] ?>')">&times;</span>
                        <img src="<?= htmlspecialchars($terrain['image']) ?>" alt="Image du terrain" class="w-full h-auto rounded-lg">
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="text-gray-700 text-center">Aucun terrain à gérer pour le moment.</p>
    <?php } ?>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>

</body>
</html>

<?php
// Include the footer file
include_once 'footerAdmin.php';
$connection->close(); // Close the database connection
?>
