<?php

$base_de_donnees = "todolist"; // Nom de votre base de données

// Connexion à MongoDB
 $connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Mise à jour
if (isset($_POST['save'])) {
    // Récupération des données du formulaire
    $id = $_POST['id'];
    $planina = $_POST['Description'];
    $StatusNumbrer = intval($_POST['Status']);
    $NomAction = $_POST['Nom'];
    $IdUser = $_POST['idUserProp'];

    $heureInput = $_POST['time'];
    $dateInput = $_POST['date'];

    // Combinaison de la date et de l'heure en un objet DateTime
    $dateTimeStr = $dateInput . ' ' . $heureInput;
    $dateObj = new DateTime($dateTimeStr);

    //$newFormat = $dateTime->format('Y-m-d H:i');

    // Conversion de la date en format BSON
    $dateMongo = new MongoDB\BSON\UTCDateTime($dateObj->getTimestamp() * 1000);

    try {
        // Création d'une requête de mise à jour MongoDB
        $mise_a_jour_requete = new MongoDB\Driver\BulkWrite;
        
        // Spécification de la mise à jour du document avec l'ID correspondant
        $mise_a_jour_requete->update(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => ['Description' => $planina, 'Status' => $StatusNumbrer, 'Nom' => $NomAction, 'IdUserPropr'=>$IdUser, 'datetime' => $dateMongo]],
            ['multi' => false, 'upsert' => false]
        );

        // Exécution de la requête de mise à jour
        $resultat = $connexion->executeBulkWrite("$base_de_donnees.tasks", $mise_a_jour_requete);

        header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$IdUser);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        // En cas d'erreur, affichage du message d'erreur
        echo "Erreur de mise à jour : " . $e->getMessage();
    }
}
// Fermer la connexion à MongoDB si nécessaire
// $connexion = null;

?>




<?php
// require 'vendor/autoload.php';
// $serveur = "localhost"; // Adresse du serveur MongoDB
// $port = 27017; // Port MongoDB par défaut
// $base_de_donnees = "todolist"; // Nom de votre base de données

// // Connexion à MongoDB\Client
// $connexion = new MongoDB\Client("mongodb://localhost:27017");
// // $database = $connexion->selectDatabase("todolist");
// // $collection = $database->selectCollection("tasks");

// // Mise à jour
// if (isset($_POST['save'])) {
//     // Récupération des données du formulaire
//     $id = $_POST['id'];
//     $planina = $_POST['Description'];
//     $StatusNumbrer = $_POST['Status'];
//     $NomAction = $_POST['Nom'];

//     try {
//         // Sélection de la collection "liste" dans la base de données
//         $collection = $connexion->$base_de_donnees->selectCollection("tasks");

//         // Création d'une requête de mise à jour MongoDB
//         $mise_a_jour_requete = [
//             '_id' => new MongoDB\BSON\ObjectId($id),
//         ];

//         $update_data = [
//             '$set' => ['Description' => $planina, 'Status' => $StatusNumbrer, 'Nom' => $NomAction],
//         ];

//         // Exécution de la requête de mise à jour
//         $resultat = $collection->updateOne($mise_a_jour_requete, $update_data);

//         // Redirection vers la page d'ajout après la mise à jour
//         header('location:ToDo_ajout.php');
//     } catch (MongoDB\Driver\Exception\Exception $e) {
//         // En cas d'erreur, affichage du message d'erreur
//         echo "Erreur de mise à jour : " . $e->getMessage();
//     }
// }

// Fermer la connexion à MongoDB si nécessaire
// $connexion = null;

?>