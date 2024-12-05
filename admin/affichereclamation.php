<?php 
include_once 'headerAdmin.php';
include_once '../admin/ConnectionSingleton.php'; 

$search = isset($_POST['search']) ? $_POST['search'] : '';

$connection = new mysqli('localhost', 'root', '', 'smartcity');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_POST['delete'])) {
    $idToDelete = $_POST['delete'];
    $deleteSql = "DELETE FROM signalements WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param('i', $idToDelete);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['reclamation_id'])) {
    $reclamationId = $_POST['reclamation_id'];
    $updateSql = "UPDATE signalements SET email_sent = 1 WHERE id = ?";
    $stmt = $connection->prepare($updateSql);
    $stmt->bind_param('i', $reclamationId);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT `id`, `description`, `latitude`, `longitude`, `localisation`, `typeReclamation`, `photo`, `prenom`, `nom`, `telephone`, `email`, `email_sent` 
        FROM signalements 
        WHERE `typeReclamation` LIKE '%" . $connection->real_escape_string($search) . "%'";

$result = $connection->query($sql);
$signalements = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $signalements[] = $row;
        }
    }
} else {
    echo "Error: " . $connection->error;
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Réclamations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-100 p-4">
    <h2 class="text-3xl font-bold mb-4 text-blue-500 text-center">Réclamations</h2>

    <div class="mb-4 flex max-w-full mx-auto">
        <form method="post" class="flex w-full">
            <input type="text" name="search" placeholder="Rechercher..." 
                   class="flex-1 py-3 px-4 border-2 border-blue-500 rounded-l-lg focus:outline-none focus:border-purple-700 transition duration-300" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="bg-blue-500 text-white font-bold py-3 px-6 rounded-r-lg hover:bg-purple-800 transition duration-300">Rechercher</button>
        </form>
    </div>

    <div id="map" class="max-w-full mx-auto mb-4 border-2 border-blue-500 rounded-lg shadow-lg" style="height: 400px;"></div>

    <div class="max-w-full mx-auto p-2 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <h3 class="text-xl font-bold mb-2 text-purple-700 col-span-full">Réclamations:</h3>
        <?php if (!empty($signalements)) : ?>
            <?php foreach ($signalements as $signalement) : ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-lg transition-transform transform hover:scale-105 hover:shadow-xl">
                    <div class="bg-blue-500 text-white py-2 text-center font-bold"><?php echo htmlspecialchars($signalement['typeReclamation']); ?></div>
                    <?php if (!empty($signalement['photo'])) : ?>
                        <img src="<?php echo htmlspecialchars($signalement['photo']); ?>" alt="Photo de la réclamation" class="w-full h-48 object-cover">
                    <?php endif; ?>
                    <div class="p-4 bg-white">
                        <p class="text-lg font-semibold mb-1"><?php echo htmlspecialchars($signalement['description']); ?></p>
                        <p class="text-gray-700 mb-1">
                            <span class="text-blue-500 font-bold">Localisation:</span> <?php echo htmlspecialchars($signalement['localisation']); ?>
                        </p>
                        <p class="text-gray-700 mb-1">
                            <span class="text-blue-500 font-bold">Nom:</span> <?php echo htmlspecialchars($signalement['prenom']) . " " . htmlspecialchars($signalement['nom']); ?>
                        </p>
                        <p class="text-gray-700 mb-1">
                            <span class="text-blue-500 font-bold">Téléphone:</span> <?php echo htmlspecialchars($signalement['telephone']); ?>
                        </p>
                        <p class="text-gray-700 mb-1">
                            <span class="text-blue-500 font-bold">Email:</span> <?php echo htmlspecialchars($signalement['email']); ?>
                        </p>
                        <p class="text-gray-700 mb-1">
                            <span class="text-blue-500 font-bold">Statut de l'email:</span> 
                            <?php echo $signalement['email_sent'] ? 'Email envoyé' : 'Email non envoyé'; ?>
                        </p>
                        <div class="mt-4 flex space-x-2 justify-center">
                            <form method="post" class="w-1/2">
                                <input type="hidden" name="delete" value="<?php echo htmlspecialchars($signalement['id']); ?>">
                                <button type="submit" class="bg-red-500 text-white font-bold py-2 w-full rounded hover:bg-red-700 transition duration-300">Supprimer</button>
                            </form>
                            <form method="post" action="emailreclamation.php" class="w-1/2">
                                <input type="hidden" name="reclamation_id" value="<?php echo htmlspecialchars($signalement['id']); ?>">
                                <button type="submit" class="bg-green-500 text-white font-bold py-2 w-full rounded hover:bg-green-700 transition duration-300">Envoyer</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-gray-700 col-span-full">Aucune réclamation trouvée.</p>
        <?php endif; ?>
    </div>

    <div class="text-center my-8">
        <button onclick="generatePDF()" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-purple-800 transition duration-300">
            Imprimer les réclamations
        </button>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        <?php foreach ($signalements as $signalement) : ?>
            var marker = L.marker([<?php echo $signalement['latitude']; ?>, <?php echo $signalement['longitude']; ?>])
                .addTo(map)
                .bindPopup("<b>Description:</b> <?php echo addslashes($signalement['description']); ?><br><b>Type:</b> <?php echo addslashes($signalement['typeReclamation']); ?>");
        <?php endforeach; ?>
    </script>
    
    <script>
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const pageHeight = doc.internal.pageSize.height;
            let y = 30;
            let reclamationHeight = 50;

            doc.setFontSize(22);
            doc.text("Liste des Réclamations", 14, 22);

            <?php foreach ($signalements as $signalement) : ?>
                if (y + reclamationHeight > pageHeight) {
                    doc.addPage();
                    y = 30;
                }
                doc.setFontSize(12);
                doc.text("Description: <?php echo addslashes($signalement['description']); ?>", 14, y);
                y += 10;
                doc.text("Type: <?php echo addslashes($signalement['typeReclamation']); ?>", 14, y);
                y += 20;
            <?php endforeach; ?>

            doc.save("reclamations.pdf");
        }
    </script>
</body>
</html>
