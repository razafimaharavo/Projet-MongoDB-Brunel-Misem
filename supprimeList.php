<?php

// Remplacez ces valeurs par les informations de votre base de données MongoDB
$serveur = "localhost"; // Adresse du serveur MongoDB
$port = 27017; // Port MongoDB par défaut
$base_de_donnees = "todolist"; // Nom de votre base de données

// Connexion à MongoDB
$connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Suppression
if (isset($_GET['idList'])) {
    try {
        // Créer une requête de suppression
        $suppression_requete = new MongoDB\Driver\BulkWrite;
        
        // Spécifier la suppression du document avec l'ID correspondant
        $suppression_requete->delete(['_id' => new MongoDB\BSON\ObjectId($_GET['idList'])]);

        // Exécuter la requête de suppression
        $resultat = $connexion->executeBulkWrite("$base_de_donnees.tasks", $suppression_requete);

        // Rediriger vers la page d'affichage après la suppression
        header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$_GET['IdUser']);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "Erreur de suppression : " . $e->getMessage();
    }
}

// Fermer la connexion à MongoDB si nécessaire
// $connexion = null;

?>
