<?php
include_once 'headerAdmin.php';

$host = 'localhost';
$dbname = 'smartcity'; 
$db_username = 'root'; 
$db_password = ''; 

$connection = new mysqli($host, $db_username, $db_password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idSuivi = isset($_POST['idSuivi']) ? $_POST['idSuivi'] : null;

    if (isset($_GET['action']) && $_GET['action'] == 'suppression') {
        if ($idSuivi) {
            $sql = "DELETE FROM suivi_dechets WHERE id='$idSuivi'";

            if ($connection->query($sql) === TRUE) {
                $message = '<p class="text-green-600">Suivi supprimé avec succès!</p>';
            } else {
                error_log("MySQL Error: " . $connection->error);
                $message = '<p class="text-red-600">Erreur lors de la suppression: ' . $connection->error . '</p>';
            }
        } else {
            $message = '<p class="text-red-600">Erreur : ID manquant pour la suppression.</p>';
        }
    } else {
        $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
        $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
        $dateHeurePrevue = isset($_POST['dateHeurePrevue']) ? $_POST['dateHeurePrevue'] : null;
        $statut = isset($_POST['statut']) ? $_POST['statut'] : null;
        $amenity = isset($_POST['amenity']) ? $_POST['amenity'] : null;
        $road = isset($_POST['road']) ? $_POST['road'] : null;
        $suburb = isset($_POST['suburb']) ? $_POST['suburb'] : null;
        $city = isset($_POST['city']) ? $_POST['city'] : null;
        $localisation = isset($_POST['localisation']) ? $_POST['localisation'] : null;
        $camion = isset($_POST['camion']) ? $_POST['camion'] : null;
        $newlocalisation = isset($_POST['newlocalisation']) ? $_POST['newlocalisation'] : null;

        $finalLocalisation = !empty($newlocalisation) ? $newlocalisation : $localisation;

        if (isset($_GET['action']) && $_GET['action'] == 'ajout') {
            $sql = "INSERT INTO suivi_dechets (latitude, longitude, date_heure_prevue, statut, amenity, road, suburb, city, localisation, camion, newlocation) 
                    VALUES ('$latitude', '$longitude', '$dateHeurePrevue', '$statut', '$amenity', '$road', '$suburb', '$city', '$finalLocalisation', '$camion', '$newlocalisation')";
        } elseif (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $sql = "UPDATE suivi_dechets SET latitude='$latitude', longitude='$longitude', date_heure_prevue='$dateHeurePrevue', 
                    statut='$statut', amenity='$amenity', road='$road', suburb='$suburb', city='$city', localisation='$finalLocalisation', camion='$camion' 
                    WHERE id='$idSuivi'";
        }

        if ($connection->query($sql) === TRUE) {
            $message = '<p class="text-green-600">Opération réussie!</p>';
        } else {
            error_log("MySQL Error: " . $connection->error);
            $message = '<p class="text-red-600">Erreur: ' . $connection->error . '</p>';
        }
    }
}

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

    <div class="mb-4 text-center">
        <?php echo $message; ?>
    </div>

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
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $idSuivi = mysqli_real_escape_string($connection, $_GET['id']);
        $sqlEdit = "SELECT * FROM suivi_dechets WHERE id='$idSuivi'";
        $resultEdit = $connection->query($sqlEdit);
        $collecteEdit = $resultEdit->fetch_assoc();
    ?>
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
                <input type="text" name="newlocalisation" id="newlocalisation" class="mt-1 p-2 rounded-md border w-full" value="<?php echo $collecteEdit['newlocation']; ?>" />
            </div>

            <div class="mb-4">
                <input type="text" name="camion" placeholder="Camion" value="<?php echo $collecteEdit['camion']; ?>" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <input type="text" name="statut" placeholder="Statut" value="<?php echo $collecteEdit['statut']; ?>" class="border p-2 w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white p-2 rounded-md w-full">Modifier</button>
        </form>
    <?php } ?>

</body>
</html>

<?php
$connection->close();
?>
