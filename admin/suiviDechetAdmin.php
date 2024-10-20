<?php
// Include the header file
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

// Handle form submission for adding a new record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'ajout') {
    // Validate that all required POST data is set
    if (isset($_POST['latitude'], $_POST['longitude'], $_POST['dateHeurePrevue'], $_POST['camion'], $_POST['amenity'], $_POST['road'], $_POST['suburb'], $_POST['city'])) {
        $latitude = mysqli_real_escape_string($connection, $_POST['latitude']);
        $longitude = mysqli_real_escape_string($connection, $_POST['longitude']);
        $dateHeurePrevue = mysqli_real_escape_string($connection, $_POST['dateHeurePrevue']);
        $camion = mysqli_real_escape_string($connection, $_POST['camion']);
        $amenity = mysqli_real_escape_string($connection, $_POST['amenity']);
        $road = mysqli_real_escape_string($connection, $_POST['road']);
        $suburb = mysqli_real_escape_string($connection, $_POST['suburb']);
        $city = mysqli_real_escape_string($connection, $_POST['city']);
        $localisation = mysqli_real_escape_string($connection, $_POST['localisation']);
        $statut = mysqli_real_escape_string($connection, $_POST['statut']);

        // Prepare the insert SQL statement
        $sql = "INSERT INTO suivi_dechets (`latitude`, `longitude`, `date_heure_prevue`, `camion`, `amenity`, `road`, `suburb`, `city`, `localisation`, `statut`) VALUES ('$latitude', '$longitude', '$dateHeurePrevue', '$camion', '$amenity', '$road', '$suburb', '$city', '$localisation', '$statut')";
        
        // Execute the insert SQL query
        if ($connection->query($sql) === TRUE) {
            // Set success message
            $message = '<p class="text-green-600">Suivi ajouté avec succès!</p>';
            // Redirect to avoid form resubmission
            header("Location: SuiviDechetAdmin.php");
            exit();
        } else {
            error_log("MySQL Error: " . $connection->error); // Log error for debugging
            $message = '<p class="text-red-600">Erreur lors de l\'ajout: ' . $connection->error . '</p>';
        }
    } else {
        $message = '<p class="text-red-600">Veuillez remplir tous les champs.</p>';
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
        <?php 
        // Display success message if action is successful
        if (isset($_GET['success'])) {
            echo '<p class="text-green-600">Suivi ajouté avec succès!</p>';
        }
        ?>
        <?php echo $message; ?>
    </div>



    <!-- Tableau pour afficher les champs existants -->
    <table class="border-collapse w-full mb-8">
        <thead>
            <tr class="bg-gray-200">
                <th class="border  py-2 ">ID</th>
                <th class="border  py-2 ">Location</th>
                <th class="border  py-2  " style="width: 200px  ;">Date et Heure Prévues</th>
                <th class="border  py-2 ">camion</th>
                <th class="border  py-2 ">Amenity</th>
                <th class="border  py-2 ">Road</th>
                <th class="border  py-2 ">Suburb</th>
                <th class="border  py-2 ">City</th>
                <th class="border  py-2 ">statut</th> <!-- New statut Column -->

            </tr>
        </thead>
        <tbody>
            <?php while ($collecte = $result->fetch_assoc()) { ?>
                <tr>
                    <td class="border p-2 "><?php echo $collecte['id']; ?></td>
                    <td class="border p-2 "><?php echo $collecte['localisation']; ?></td>
                    <td class="border p-2 "><?php echo date("Y-m-d H:i:s", strtotime($collecte['date_heure_prevue'])); ?></td>
                    <td class="border p-2 "><?php echo $collecte['camion']; ?></td>
                    <td class="border p-2 "><?php echo $collecte['amenity']; ?></td>
                    <td class="border p-2 "><?php echo $collecte['road']; ?></td>
                    <td class="border p-2 "><?php echo $collecte['suburb']; ?></td>
                    <td class="border p-2 "><?php echo $collecte['city']; ?></td>
                    <td class="border p-2"><?php echo $collecte['statut']; ?></td> <!-- Display statut -->
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Formulaire pour l'ajout -->
    <h2 class="text-2xl font-bold mb-4 text-blue-500 text-center">Ajouter un Suivi</h2>
    <form action="?action=ajout" method="post" class="max-w-2xl mx-auto bg-white p-8 rounded-md shadow-2xl">
    <div id="map" class="mb-4"></div>

        <div class="mb-4">
            <label for="latitude" class="block text-sm font-medium text-gray-600">Latitude:</label>
            <input type="text" name="latitude" id="latitude" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>

        <div class="mb-4">
            <label for="longitude" class="block text-sm font-medium text-gray-600">Longitude:</label>
            <input type="text" name="longitude" id="longitude" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>
        <div class="mb-4">
            <label for="localisation" class="block text-sm font-medium text-gray-600">Adresse:</label>
            <input type="text" name="localisation" id="localisation" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
        </div>

        <!-- Map Container -->

        <div class="mb-4">
            <label for="camion" class="block text-sm font-medium text-gray-600">camion:</label>
            <select id="camion" name="camion" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                <option value="camion1">camion 1</option>
                <option value="camion2">camion 2</option>
                <option value="camion3">camion 3</option>
                <option value="camion4">camion 4</option>
                <option value="camion5">camion 5</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="dateHeurePrevue" class="block text-sm font-medium text-gray-600">Date et Heure Prévues:</label>
            <input type="datetime-local" name="dateHeurePrevue" id="dateHeurePrevue" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>

        <div class="mb-4">
            <label for="amenity" class="block text-sm font-medium text-gray-600">Amenity:</label>
            <input type="text" name="amenity" id="amenity" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>

        <div class="mb-4">
            <label for="road" class="block text-sm font-medium text-gray-600">Road:</label>
            <input type="text" name="road" id="road" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>

        <div class="mb-4">
            <label for="suburb" class="block text-sm font-medium text-gray-600">Suburb:</label>
            <input type="text" name="suburb" id="suburb" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>
        <div class="mb-4">
            <label for="statut" class="block text-sm font-medium text-gray-600">statut:</label>
            <select id="statut" name="statut" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="city" class="block text-sm font-medium text-gray-600">City:</label>
            <input type="text" name="city" id="city" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>

        <button type="submit" class="bg-blue-500 text-white p-2 rounded-md w-full">Ajouter</button>
    </form>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([34.020882, -6.840668], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marker = L.marker([34.020882, -6.840668]).addTo(map);

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Update the marker position
            marker.setLatLng([lat, lng]);

            // Fill the inputs with coordinates
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // Fetch the address
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('localisation').value = data.display_name; // Fill address input
                })
                .catch(err => console.error(err));
        });
    </script>
</body>
</html>
