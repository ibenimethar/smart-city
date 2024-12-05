<?php
include_once 'header.php';

$host = 'localhost';
$dbname = 'smartcity'; 
$db_username = 'root'; 
$db_password = ''; 

// Créer une nouvelle connexion MySQLi
$connection = new mysqli($host, $db_username, $db_password, $dbname);

// Vérifier les erreurs de connexion
if ($connection->connect_error) {
    die("Échec de la connexion : " . $connection->connect_error);
}

// Requête SQL pour obtenir les données
$sql = "SELECT * FROM suivi_dechets";
$result = $connection->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Suivi des Déchets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-8">
    <h2 class="text-4xl font-bold mb-8 text-purple-700">Suivi des Déchets</h2>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2 ">ID</th>
                <th class=" border p-2 ">Location</th>
                <th class="border p-2 whitespace-nowrap">Date et Heure Prévues</th>
                <th class="border p-2 ">camion</th>
                <th class="border p-2 ">Amenity</th>
                <th class="border p-2 ">Road</th>
                <th class="border p-2 ">Suburb</th>
                <th class="border p-2 ">City</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if ($result && $result->num_rows > 0) {
        // Boucle sur chaque ligne de données
        while ($row = $result->fetch_assoc()) {
    ?>
    <tr class="border-t border-gray-300">
    <td class="border p-2"><?php echo isset($row['id']) ? $row['id'] : 'N/A'; ?></td>
    <td class=" border p-2 "><?php echo isset($row['localisation']) ? $row['localisation'] : 'N/A'; ?></td>
    <td class="border p-2"><?php echo isset($row['date_heure_prevue']) ? $row['date_heure_prevue'] : 'N/A'; ?></td>
    <td class="border p-2"><?php echo isset($row['camion']) ? $row['camion'] : 'N/A'; ?></td>
    <td class="border p-2"><?php echo isset($row['amenity']) ? $row['amenity'] : 'N/A'; ?></td>
    <td class="border p-2"><?php echo isset($row['road']) ? $row['road'] : 'N/A'; ?></td>
    <td class="border p-2"><?php echo isset($row['suburb']) ? $row['suburb'] : 'N/A'; ?></td>
    <td class="border p-2"><?php echo isset($row['city']) ? $row['city'] : 'N/A'; ?></td>
</tr>

    <?php
        }
    } else {
        echo '<tr><td colspan="9" class="p-2 text-center">Aucune donnée disponible</td></tr>';
    }
    ?>
</tbody>

    </table>
    <div class="mt-8 flex items-center">
        <div class="w-1/2 flex-shrink-0">
            <img src="https://media.istockphoto.com/id/1029284106/vector/funny-garbage-truck-car-with-eyes-municipal-machinery.jpg?s=612x612&w=0&k=20&c=8JCcJqftesYB5DdkMArLnpMlVCPps3cvCfMlvwm5eLI=" class="rounded-2xl shadow-lg object-cover h-1/2">
        </div>
        <div class="w-1/2 ml-8 flex-1">
            <h3 class="text-2xl font-bold mb-4 text-purple-700 font-mono">À propos du Suivi des Déchets</h3>
            <p class="text-gray-700">
                Le suivi des déchets fournit des informations essentielles sur la collecte des déchets, y compris les détails sur la localisation,
                la date et l'heure prévues, le camion et d'autres informations pertinentes. Explorez les données pour une gestion efficace des déchets
                dans votre ville.
            </p>
        </div>
    </div>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$connection->close();

// Inclure le fichier de pied de page
include_once 'footer.php';
?>
