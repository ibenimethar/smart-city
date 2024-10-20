<?php 

include_once 'headerAdmin.php';
include_once '../admin/ConnectionSingleton.php'; 

// Initialize search variable
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Create the database connection
$connection = new mysqli('localhost', 'root', '', 'smartcity');

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Prepare SQL query to filter based on the search input
$sql = "SELECT `description`, `latitude`, `longitude`, `localisation`, `typeReclamation`, `photo`, `prenom`, `nom`, `telephone` 
        FROM signalements 
        WHERE `typeReclamation` LIKE '%" . $connection->real_escape_string($search) . "%'"; // Escape the search input to prevent SQL injection

$result = $connection->query($sql);
$signalements = [];

// Check if the query was successful
if ($result) {
    // Fetch results if available
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $signalements[] = $row;
        }
    }
} else {
    echo "Error: " . $connection->error; // Debugging line to show SQL errors
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
        <input type="text" name="search" placeholder="Rechercher..." 
               class="flex-1 py-3 px-4 border-2 border-blue-500 rounded-l-lg focus:outline-none focus:border-purple-700 transition duration-300" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="bg-blue-500 text-white font-bold py-3 px-6 rounded-r-lg hover:bg-purple-800 transition duration-300">Rechercher</button>
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
            const { jsPDF } = window.jspdf; // Using jsPDF library
            const doc = new jsPDF();
            const pageHeight = doc.internal.pageSize.height; // Get page height
            let y = 30; // Starting y position for the first reclamation
            let reclamationHeight = 40; // Estimated height of each reclamation section

            // Title
            doc.setFontSize(22);
            doc.text("Liste des Réclamations", 14, 22);

            // Loop through the reclamations to add them to the PDF
            <?php foreach ($signalements as $signalement) : ?>
                // Check if y position is too low, if so, add a new page
                if (y + reclamationHeight > pageHeight) {
                    doc.addPage();
                    y = 20; // Reset y position for the new page
                }

                // Add Type
                doc.setFontSize(14);
                doc.text("Type: <?php echo addslashes($signalement['typeReclamation']); ?>", 10, y);
                y += 10;

                // Add Description
                doc.setFontSize(12);
                doc.text("Description:", 10, y);
                y += 10;

                var description = "<?php echo addslashes($signalement['description']); ?>";
                var lines = doc.splitTextToSize(description, 190); // Wrap text to fit in the PDF
                doc.text(lines, 10, y);
                y += lines.length * 10; // Adjust y based on the number of lines

                // Add Localisation
                var address = "<?php echo addslashes($signalement['localisation']); ?>";
                var addressLines = doc.splitTextToSize(address, 190); // Wrap address to fit in the PDF
                doc.text("Localisation:", 10, y);
                y += 10;
                doc.text(addressLines, 10, y);
                y += addressLines.length * 10; // Adjust y based on the number of lines

                // Add Name
                doc.text("Nom: <?php echo addslashes($signalement['prenom']) . ' ' . addslashes($signalement['nom']); ?>", 10, y);
                y += 10;

                // Add Phone
                doc.text("Téléphone: <?php echo addslashes($signalement['telephone']); ?>", 10, y);
                y += 10;

                // Draw a horizontal line for separation
                doc.setDrawColor(0); // Set the color of the line (black)
                doc.line(10, y, 200, y); // Draw the line from (10, y) to (200, y)
                y += 5; // Add space after the line

                // Additional space between entries
                y += 10;
            <?php endforeach; ?>

            // Save the PDF
            doc.save("reclamations.pdf"); // Name of the generated PDF
        }
    </script>

<?php
include_once 'footerAdmin.php';
?>
</body>
</html>
