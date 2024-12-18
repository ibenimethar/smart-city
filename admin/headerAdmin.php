<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ville Intelligent</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100">

<!-- Navbar -->
<nav class="bg-blue-400 p-2">
    <div class="container mx-auto flex items-center justify-between">
        <!-- Logo à gauche -->
        <a class="text-white font-mono" href="./">Ville intelligent <strong class="text-red-800">(Admin)</strong></a>
        
        <!-- Liens de navigation au centre -->
        <div class="flex">
            <a class="text-white ml-4" href="success.php">Accueil</a>
            <a class="text-white ml-4" href="ajoutTerrain.php">Ajouter Terrain</a>
            <a class="text-white ml-4" href="addPlaceTouristique.php">Place Touristique</a>
            <a class="text-white ml-4" href="suiviDechetAdmin.php">Suivi des déchets</a>
            <a class="text-white ml-4" href="ajoutPatrimoine.php">Patrimoine</a>
        </div>

        <!-- Bouton de déconnexion avec l'username à droite -->
        <div class="flex items-center">
            <a class="text-white ml-4 border rounded-md p-1 px-4 hover:bg-white hover:text-purple-400" href="logout.php">Déconnexion</a>

            <span class="text-white ml-2 text-xs font-mono">
                <?php 
                if (isset($_SESSION['username'])) {
                    echo "Bonjour, " . ($_SESSION['username']); 
                } else {
                    echo "Non connecté";
                }
                ?>
            </span>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container mt-4 mx-auto">
