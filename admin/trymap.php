 <?php // Include the header file
include_once 'headerAdmin.php';

$host = 'localhost';
$dbname = 'smartcity'; 
$db_username = 'root'; 
$db_password = ''; 

// Create a new connection using MySQLi
$connection = new mysqli($host, $db_username, $db_password, $dbname);

// Check for connection errors
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$message = ''; // Initialize message variable

// Handle form submission for adding or editing a record
// Handle form submission for adding or editing a record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idSuivi = isset($_POST['idSuivi']) ? mysqli_real_escape_string($connection, $_POST['idSuivi']) : null;
    $latitude = mysqli_real_escape_string($connection, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($connection, $_POST['longitude']);
    $dateHeurePrevue = mysqli_real_escape_string($connection, $_POST['dateHeurePrevue']);
    $statut = mysqli_real_escape_string($connection, $_POST['statut']);
    $amenity = mysqli_real_escape_string($connection, $_POST['amenity']);
    $road = mysqli_real_escape_string($connection, $_POST['road']);
    $suburb = mysqli_real_escape_string($connection, $_POST['suburb']);
    $city = mysqli_real_escape_string($connection, $_POST['city']);
    $localisation = mysqli_real_escape_string($connection, $_POST['localisation']);
    $camion = mysqli_real_escape_string($connection, $_POST['camion']);
    $newlocalisation = mysqli_real_escape_string($connection, $_POST['newlocalisation']);

    // Use newlocalisation if it's provided; otherwise, use localisation
    $finalLocalisation = !empty($newlocalisation) ? $newlocalisation : $localisation;

    if (isset($_GET['action']) && $_GET['action'] == 'ajout') {
        // Insert new record
        $sql = "INSERT INTO suivi_dechets (latitude, longitude, date_heure_prevue, statut, amenity, road, suburb, city, localisation, camion, newlocation) 
                VALUES ('$latitude', '$longitude', '$dateHeurePrevue', '$statut', '$amenity', '$road', '$suburb', '$city', '$finalLocalisation', '$camion', '$newlocalisation')";
    } elseif (isset($_GET['action']) && $_GET['action'] == 'edit') {
        // Update existing record
        $sql = "UPDATE suivi_dechets SET latitude='$latitude', longitude='$longitude', date_heure_prevue='$dateHeurePrevue', 
                statut='$statut', amenity='$amenity', road='$road', suburb='$suburb', city='$city', localisation='$finalLocalisation', camion='$camion' 
                WHERE id='$idSuivi'";
    }

    // Execute the SQL query
    if ($connection->query($sql) === TRUE) {
        $message = '<p class="text-green-600">Opération réussie!</p>';
        header("Location: SuiviDechetAdmin.php");
        exit();
    } else {
        error_log("MySQL Error: " . $connection->error); // Log error for debugging
        $message = '<p class="text-red-600">Erreur: ' . $connection->error . '</p>';
    }
}


// Handle deletion of a record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'suppression') {
    if (isset($_POST['idSuivi'])) {
        $idSuivi = mysqli_real_escape_string($connection, $_POST['idSuivi']);
        $sql = "DELETE FROM suivi_dechets WHERE id='$idSuivi'";

        if ($connection->query($sql) === TRUE) {
            $message = '<p class="text-green-600">Suivi supprimé avec succès!</p>';
        } else {
            error_log("MySQL Error: " . $connection->error); // Log error for debugging
            $message = '<p class="text-red-600">Erreur lors de la suppression: ' . $connection->error . '</p>';
        }
    }
}

// Fetch existing records
$sql = "SELECT * FROM suivi_dechets";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi Des Déchets - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 300px;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-4xl font-bold mb-8 text-blue-500 text-center">Suivi Des Déchets - Admin</h1>

    <!-- Message display -->
    <div class="mb-4 text-center">
        <?php echo $message; ?>
    </div>

    <!-- Table for displaying existing records -->
    <table class="border-collapse w-full mb-8">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2 no-wrap">ID</th>
                <th class="border p-2 no-wrap">Location</th>
                <th class="border p-2 no-wrap" style="width: 200px;">Date et Heure Prévues</th>
                <th class="border p-2 no-wrap">Amenity</th>
                <th class="border p-2 no-wrap">Road</th>
                <th class="border p-2 no-wrap">Suburb</th>
                <th class="border p-2 no-wrap">City</th>
                <th class="border p-2 no-wrap">Camion</th>
                <th class="border p-2 no-wrap">Statut</th>
                <th class="border p-2 no-wrap">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($collecte = $result->fetch_assoc()) { ?>
                <tr>
                    <td class="border p-2"><?php echo $collecte['id']; ?></td>
                    <td class="border p-2"><?php echo $collecte['localisation']; ?></td>
                    <td class="border p-2"><?php echo date("Y-m-d H:i:s", strtotime($collecte['date_heure_prevue'])); ?></td>
                    <td class="border p-2"><?php echo $collecte['amenity']; ?></td>
                    <td class="border p-2"><?php echo $collecte['road']; ?></td>
                    <td class="border p-2"><?php echo $collecte['suburb']; ?></td>
                    <td class="border p-2"><?php echo $collecte['city']; ?></td>
                    <td class="border p-2"><?php echo $collecte['camion']; ?></td>
                    <td class="border p-2"><?php echo $collecte['statut']; ?></td>
                    <td class="border p-2">
                        <a href="?action=edit&id=<?php echo $collecte['id']; ?>" class="text-blue-500 hover:underline">Modifier</a>
                        <form method="POST" action="?action=suppression" style="display:inline;">
                            <input type="hidden" name="idSuivi" value="<?php echo $collecte['id']; ?>">
                            <button type="submit" class="text-red-500 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php
    // Handle edit action if needed
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $idSuivi = mysqli_real_escape_string($connection, $_GET['id']);
        $sqlEdit = "SELECT * FROM suivi_dechets WHERE id='$idSuivi'";
        $resultEdit = $connection->query($sqlEdit);
        $collecteEdit = $resultEdit->fetch_assoc();
    ?>
        <!-- Form for editing a record -->
        <h2 class="text-2xl font-bold mb-4 text-blue-500 text-center">Modifier un Suivi</h2>
        <form method="POST" action="?action=edit" class="max-w-2xl mx-auto bg-white p-8 rounded-md shadow-2xl">
            <input type="hidden" name="idSuivi" value="<?php echo $collecteEdit['id']; ?>">
            <div id="map" class="mb-4"></div>

            <div class="mb-4">
                <input type="text" name="latitude" id="latitude" placeholder="Latitude" value="<?php echo $collecteEdit['latitude']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <input type="text" name="longitude" id="longitude" placeholder="Longitude" value="<?php echo $collecteEdit['longitude']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <input type="text" name="localisation" placeholder="Localisation" value="<?php echo $collecteEdit['localisation']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
            <label for="localisation" class="block text-sm font-medium text-gray-600"> nouvelle Adresse:</label>
            <input type="text" name="newlocalisation" id="newlocalisation"  class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
        </div>
            <div class="mb-4">
                <input type="datetime-local" name="dateHeurePrevue" value="<?php echo date("Y-m-d\TH:i", strtotime($collecteEdit['date_heure_prevue'])); ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <select name="statut" class="border p-2 w-full">
                    <option value="Prévu" <?php echo ($collecteEdit['statut'] == 'Prévu') ? 'selected' : ''; ?>>Prévu</option>
                    <option value="Complété" <?php echo ($collecteEdit['statut'] == 'Complété') ? 'selected' : ''; ?>>Complété</option>
                </select>
            </div>
            <div class="mb-4">
                <input type="text" name="amenity" placeholder="Amenity" value="<?php echo $collecteEdit['amenity']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <input type="text" name="road" placeholder="Road" value="<?php echo $collecteEdit['road']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <input type="text" name="suburb" placeholder="Suburb" value="<?php echo $collecteEdit['suburb']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <input type="text" name="city" placeholder="City" value="<?php echo $collecteEdit['city']; ?>" class="border p-2 w-full">
            </div>
           
            <div class="mb-4">
                <input type="text" name="camion" placeholder="Camion" value="<?php echo $collecteEdit['camion']; ?>" class="border p-2 w-full">
            </div>
            <div class="text-center">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded">Mettre à jour</button>
            </div>
        </form>
    <?php }  ?>
      

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([34.020882, -6.840378], 13); // Default center of the map

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Add a marker for the existing location if editing
        var marker;
        <?php if (isset($collecteEdit)) { ?>
            marker = L.marker([<?php echo $collecteEdit['latitude']; ?>, <?php echo $collecteEdit['longitude']; ?>]).addTo(map);
        <?php } else { ?>
            marker = L.marker([34.020882, -6.840378]).addTo(map); // Default marker for adding new
        <?php } ?>

        // Update latitude and longitude fields when marker is dragged
        marker.on('dragend', function(e) {
            var position = e.target.getLatLng();
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
        });

        // Handle map clicks to set new location
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('newlocalisation').value = data.display_name; // Fill address input
                })
                .catch(err => console.error(err));
        });
    </script>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>