<?php

include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCity - Page d'accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
      
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .fade-in-visible {
            opacity: 1;
            transform: translateY(0);
        }

    </style>
</head>
<body class="font-sans bg-gray-100">

<div class="container mx-auto flex mt-8 fade-in">
    <div class="w-1/2 pr-8">
        <h1 class="text-4xl text-purple-700 font-bold mb-4">Bienvenue sur SmartCity!</h1>
        <p class="text-lg font-light">
            SmartCity offre une approche innovante pour rendre la vie urbaine plus pratique et connectée.
            Explorez nos services pour réserver des terrains publics, signaler des problèmes, suivre la gestion des déchets
            et découvrir des lieux touristiques exceptionnels.
        </p>
    </div>
    <div class="w-1/2 pr-8 " >
        <img src="https://media.gettyimages.com/id/985889774/fr/photo/tapis-faits-main-et-des-tapis-maroc.jpg?s=612x612&w=0&k=20&c=wQeX4IWf4kSOOUXjhOGT7B65JX6YkaBLEFB37H0rtu0=" 
             alt="Description of the image" 
             class="rounded-lg shadow-lg hover-zoom w-full h-auto">
    </div>
</div>

<div class="container mx-auto mt-8 fade-in">
    <h2 class="text-2xl text-purple-700 font-bold mb-4">Nos Services</h2>

    <div class="flex mb-8 fade-in">
        <div class="w-1/3 pr-4">
            <img src="https://img.freepik.com/photos-gratuite/vue-du-terrain-football-herbe_23-2150887305.jpg" 
                 alt="Image Service 1" 
                 class="rounded-lg shadow-lg mb-4 hover-zoom">
        </div>
        <div class="w-2/3">
            <h3 class="text-xl font-bold mb-2">Réservation des Terrains Publics</h3>
            <p class="text-gray-700">
                Réservez facilement les terrains publics pour vos activités sportives ou événements spéciaux.
            </p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-full mt-2 hover:bg-blue-600" 
                    onclick="window.location.href='../admin/terrainsDisponible.php'">Réserver</button>
        </div>
    </div>

    <div class="flex mb-8 fade-in">
        <div class="w-1/3 pr-4">
            <img src="https://www.merci-app.com/app/uploads/2023/07/643960ac1999ef0f2b66b178_62dfb171bd353b3faaedffff_recalamation-client-blog-cover.png" 
                 alt="Image Service 2" 
                 class="rounded-lg shadow-lg mb-4 hover-zoom">
        </div>
        <div class="w-2/3">
            <h3 class="text-xl font-bold mb-2">Signalement des Problèmes</h3>
            <p class="text-gray-700">
                Signalez rapidement les problèmes de la ville pour une résolution efficace et une meilleure qualité de vie.
            </p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-full mt-2 hover:bg-blue-600" 
        onclick="window.location.href='../admin/signalement.php'">Signaler</button>

        </div>
    </div>

    <div class="flex mb-8 fade-in">
        <div class="w-1/3 pr-4">
            <img src="https://blog.chimirec.fr/img/articles_a/1579164329.blog.chimirec.bsd.140120.ok.jpg" 
                 alt="Image Service 3" 
                 class="rounded-lg shadow-lg mb-4 hover-zoom">
        </div>
        <div class="w-2/3">
            <h3 class="text-xl font-bold mb-2">Suivi des Déchets</h3>
            <p class="text-gray-700">
                Contribuez à un environnement propre en suivant la gestion des déchets de votre ville.
            </p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-full mt-2 hover:bg-blue-600" 
                    onclick="window.location.href='../user/suiviDechets.php'">Suivre</button>
        </div>
    </div>

    <div class="flex mb-8 fade-in">
        <div class="w-1/3 pr-4">
            <img src="https://passporterapp.com/fr/blog/wp-content/uploads/2023/09/que-faire-a-Essaouira.webp" 
                 alt="Image Service 4" 
                 class="rounded-lg shadow-lg mb-4 hover-zoom">
        </div>
        <div class="w-2/3">
            <h3 class="text-xl font-bold mb-2">Visualisation des Lieux Touristiques</h3>
            <p class="text-gray-700">
                Explorez et découvrez les endroits touristiques fascinants de votre ville avec notre guide interactif.
            </p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-full mt-2 hover:bg-blue-600" 
                    onclick="window.location.href='../admin/displayPlacesTouristiques.php'">Explorer</button>
        </div>
    </div>

    <div class="container mx-auto mt-8 border border-purple-400 p-6 rounded-lg shadow-lg my-8 fade-in">
        <h2 class="text-2xl text-purple-700 font-bold mb-4 py-2 border-b-8">À propos de SmartCity</h2>
        <p class="text-lg font-light">
            SmartCity est une application innovante qui vise à rendre la vie urbaine plus pratique, connectée et agréable.
            Notre plateforme offre une gamme de services conçus pour améliorer la qualité de vie des citoyens.
            Découvrez comment nous facilitons la réservation des terrains publics, la gestion des problèmes de la ville, le suivi des déchets,
            et l'exploration des lieux touristiques. Rejoignez-nous pour contribuer à construire une ville plus intelligente et durable.
        </p>
    </div>
</div>


<script>
    var fadeInElements = document.querySelectorAll('.fade-in');
    
    function fadeInOnScroll() {
        fadeInElements.forEach(function(el) {
            var rect = el.getBoundingClientRect();
            if (rect.top <= window.innerHeight - 100) {
                el.classList.add('fade-in-visible');
            }
        });
    }

    window.addEventListener('scroll', fadeInOnScroll);
    fadeInOnScroll(); 
</script>

<?php

include_once 'footer.php';
?>
</body>
</html>
