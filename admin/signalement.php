<?php
// Include the header file
include_once '../user/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection variables
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

  
    // Collect form data
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email']; // Collect the email address
    $localisation = $_POST['localisation']; // Collect the generated address
    $typeReclamation = $_POST['typeReclamation'];
    $photo = $_FILES['photo']['name']; // Get the uploaded file name
    $tempname = $_FILES['photo']['tmp_name']; // Get the temporary file name
    $targetDir = "uploads/"; // Directory to store uploaded photos
    $targetFile = $targetDir . $photo;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($tempname, $targetFile)) {
        // Prepare the SQL statement
        $stmt = $connection->prepare("INSERT INTO signalements 
        (`description`, `latitude`, `longitude`, `localisation`, `typeReclamation`, `photo`, `prenom`, `nom`, `telephone`, `email`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Make sure to use the correct data types in bind_param
        $stmt->bind_param("sddsssssss", 
        $description, 
        $latitude, 
        $longitude, 
        $localisation, 
        $typeReclamation, 
        $targetFile, 
        $prenom, 
        $nom, 
        $telephone,
        $email // Bind the email address
        );

        if ($stmt->execute()) {
            // Redirect to the same page after successful submission
            header("Location: Signalement.php");
            exit();
        } else {
            echo "Erreur lors de la soumission de la réclamation: " . $stmt->error;
        }
    } else {
        echo "Erreur lors de l'upload de l'image.";
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Réclamation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 300px;
        }
    </style>
</head>

<body class="bg-gray-100 p-8">
    <h2 class="text-4xl font-bold mb-8 text-purple-700 text-center">Formulaire de Réclamation</h2>
    <form method="POST" action="" enctype="multipart/form-data" class="max-w-2xl mx-auto bg-white p-8 rounded-md shadow-2xl">
            <!-- Prénom -->
    <div class="mb-4">
        <label for="prenom" class="block text-sm font-medium text-gray-600">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
    </div>

    <!-- Nom de famille -->
    <div class="mb-4">
        <label for="nom" class="block text-sm font-medium text-gray-600">Nom de famille :</label>
        <input type="text" name="nom" id="nom" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
    </div>

    <!-- Numéro de téléphone -->
    <div class="mb-4">
        <label for="telephone" class="block text-sm font-medium text-gray-600">Numéro de téléphone :</label>
        <input type="text" name="telephone" id="telephone" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
    </div>

<!-- Champ pour l'adresse email -->
<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-600">Email :</label>
    <input type="email" name="email" id="email" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
</div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-600">Description:</label>
            <textarea name="description" rows="5" cols="50" maxlength="340" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" 
        placeholder="Enter your description here (max 4 lines)" ></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="latitude" class="block text-sm font-medium text-gray-600">Latitude:</label>
                <input type="text" name="latitude" id="latitude" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
            </div>

            <div class="mb-4">
                <label for="longitude" class="block text-sm font-medium text-gray-600">Longitude:</label>
                <input type="text" name="longitude" id="longitude" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
            </div>
        </div>

        <!-- Address field (auto-filled by reverse geocoding) -->
        <div class="mb-4">
            <label for="localisation" class="block text-sm font-medium text-gray-600">Adresse:</label>
            <input type="text" name="localisation" id="localisation" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" readonly>
        </div>

        <div class="mb-4">
    <label for="typeReclamation" class="block text-sm font-medium text-gray-600">Type de Réclamation:</label>
    <select name="typeReclamation" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        <option value="probleme_routier">Problème Routier</option>
        <option value="dechet_abandonne">Déchet Abandonné</option>
        <option value="nuisance_sonore">Nuisance Sonore</option>
        <option value="eclairage_publique">Problème d'Éclairage Public</option>
        <option value="qualite_air">Qualité de l'Air</option>
        <option value="infrastructure">Infrastructure Défectueuse</option>
        <option value="animal_errant">Animal Errant</option>
        <option value="autre">Autre</option>
        <option value="propriete_abandonnee">Propriété Abandonnée</option>
        <option value="fuite_eau">Fuite d'Eau</option>
        <option value="projet_de_construction">Problème de Projet de Construction</option>
        <option value="vandalisme">Vandalisme</option>
        <option value="insalubrite">Insalubrité</option>
        <option value="pollution">Pollution</option>
        <option value="protection_environnement">Protection de l'Environnement</option>
        <option value="manque_services_publiques">Manque de Services Publics</option>
        <option value="circulation">Problèmes de Circulation</option>
        <option value="bruit_chantier">Bruit de Chantier</option>
        <option value="danger_securite">Danger à la Sécurité Publique</option>
        <option value="eau_stagnante">Eau Stagnante</option>
        <option value="faux_plants">Faux Plantes ou Arbustes</option>
        <option value="reclamation_generale">Réclamation Générale</option>
       

    </select>
</div>


        <div class="mb-4">
            <label for="photo" class="block text-sm font-medium text-gray-600">Photo:</label>
            <input type="file" name="photo" accept="image/*" required class="mt-1 p-2 border border-gray-300 rounded-md w-full">
        </div>

        <!-- Map -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Choisir sur la carte:</label>
            <div id="map"></div>
        </div>

        <button type="submit" class="bg-purple-500 text-white p-2 rounded-md shadow-2xl w-full">Soumettre Réclamation</button>
    </form>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([31.5, -9.76], 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Add a marker to the map
        var marker = L.marker([31.5, -9.76]).addTo(map);

        // Function to reverse geocode using Nominatim API
        function reverseGeocode(lat, lon) {
            var apiUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`;
            
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('localisation').value = data.display_name;
                    } else {
                        document.getElementById('localisation').value = "Adresse introuvable";
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération de l'adresse:", error);
                    document.getElementById('localisation').value = "Erreur lors de la récupération de l'adresse";
                });
        }

        // Event listener for map clicks
        map.on('click', function(e) {
            // Get the latitude and longitude from the clicked point
            var lat = e.latlng.lat;
            var lon = e.latlng.lng;

            // Update the marker position
            marker.setLatLng([lat, lon]);

            // Set the latitude and longitude values in the form inputs
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;

            // Perform reverse geocoding to get the address
            reverseGeocode(lat, lon);
        });
    </script>
</body>
</html>
<?php
// Include the footer file
include_once '../user/footer.php';
?>
