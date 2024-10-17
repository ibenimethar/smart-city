<?php 
include_once '../user/header.php'; 
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
    <style>
        body {
            background-color: #f7fafc;
        }
        #map {
            height: 400px;
            width: 100%;
            border: 2px solid #6b46c1;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .reclamation-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .reclamation-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .reclamation-card-header {
            background-color: #6b46c1;
            color: white;
            padding: 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            font-weight: bold;
            text-align: center; /* Center the title */
        }
        .reclamation-card-body {
            padding: 16px;
            background-color: #fff;
        }
        .reclamation-card img {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            margin-top: 10px; /* Added some margin to separate image from header */
        }
        .search-container {
            display: flex; /* Aligns the search input and button in a row */
            max-width: 100%; /* Set to 100% for full width */
            margin: 0 auto; /* Center the container */
            margin-bottom: 20px; /* Space between search and map */
        }
        .search-input {
            flex: 1; /* Takes remaining space */
            padding: 0.75rem; /* Increased padding for better appearance */
            border: 2px solid #6b46c1; /* Match border with the theme */
            border-radius: 8px 0 0 8px; /* Rounded corners for the left side */
            outline: none; /* Removes the default outline */
            transition: border-color 0.3s; /* Smooth border color transition */
            width: 70%; /* Increased width */
        }
        .search-input:focus {
            border-color: #4c51bf; /* Darker purple on focus */
        }
        .search-button {
            background-color: #6b46c1; /* Purple background */
            color: white; /* White text */
            padding: 0.75rem 1.5rem; /* Padding for button */
            border: none; /* Removes default border */
            border-radius: 0 8px 8px 0; /* Rounded corners for the right side */
            cursor: pointer; /* Changes cursor on hover */
            transition: background-color 0.3s; /* Smooth background color transition */
        }
        .search-button:hover {
            background-color: #4c51bf; /* Darker purple on hover */
        }
      
        .label-purple {
            color: #6b46c1; /* Change text color to purple */
            font-weight: bold; /* Bold text for labels */
            width: 150px; /* Fixed width for uniformity */
            height: 20px; /* Fixed height for uniformity */
            text-align: left; /* Align text to the left */
            line-height: 40px; /* Center the text vertically */
            margin-right: 5px; /* Optional: Add a small right margin for spacing */
        }
    </style>
</head>
<body class="p-4">
    <h2 class="text-3xl font-bold mb-4 text-purple-700 text-center">Réclamations</h2>

    <div class="search-container mb-4">
        <input type="text" name="search" placeholder="Rechercher..." 
               class="search-input" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="search-button">Rechercher</button>
    </div>

    <div id="map" class="max-w-full mx-auto mb-4"></div>

    <div class="max-w-full mx-auto p-2 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <h3 class="text-xl font-bold mb-2 text-purple-700 col-span-full">Réclamations:</h3>
        <?php if (!empty($signalements)) : ?>
            <?php foreach ($signalements as $signalement) : ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-lg reclamation-card">
                    <div class="reclamation-card-header"><?php echo htmlspecialchars($signalement['typeReclamation']); ?></div>
                    <?php if (!empty($signalement['photo'])) : ?>
                        <img src="<?php echo htmlspecialchars($signalement['photo']); ?>" alt="Photo de la réclamation" class="w-full h-48 object-cover">
                    <?php endif; ?>
                    <div class="reclamation-card-body">
                        <p class="text-lg font-semibold mb-1"><?php echo htmlspecialchars($signalement['description']); ?></p>
                        <p class="text-gray-700 mb-1">
                            <span class="label-purple">Localisation:</span> <?php echo htmlspecialchars($signalement['localisation']); ?>
                        </p>
                        <p class="text-gray-700 mb-1">
                            <span class="label-purple">Nom:</span> <?php echo htmlspecialchars($signalement['prenom']) . " " . htmlspecialchars($signalement['nom']); ?>
                        </p>
                        <p class="text-gray-700 mb-1">
                            <span class="label-purple">Téléphone:</span> <?php echo htmlspecialchars($signalement['telephone']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-gray-700 col-span-full">Aucune réclamation trouvée.</p>
        <?php endif; ?>
    </div>
    <div class="flex justify-center mt-8" style="margin">
    <button onclick="generatePDF()" class="bg-purple-700 text-white font-bold py-2 px-6 rounded-lg hover:bg-purple-800 transition-colors duration-200">
        Imprimer les réclamations
    </button>
</div>

<div id="map" class="max-w-full mx-auto mb-4"></div>


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
            y +=10;
        <?php endforeach; ?>

        // Save the PDF
        doc.save("reclamations.pdf"); // Name of the generated PDF
    }
</script>

</body>
</html>
