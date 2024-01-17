<?php

$serveur = "localhost";
$port = 27017;
$base_de_donnees = "todolist";

// Connexion à MongoDB
$connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Insertion
if (isset($_POST['save'])) {
    $planina = $_POST['Description'];
    $StatusNumber = intval($_POST['Status']);
    $NomAction = $_POST['Nom'];
    $IdUser = $_POST['idUserProp'];

    // Récupération des données depuis le formulaire
$heureInput = $_POST['time'];
$dateInput = $_POST['date'];

// Combinaison de la date et de l'heure en un objet DateTime
$dateTimeStr = $dateInput . ' ' . $heureInput;
$dateObj = new DateTime($dateTimeStr);

//$newFormat = $dateTime->format('Y-m-d H:i');

// Conversion de la date en format BSON
$dateMongo = new MongoDB\BSON\UTCDateTime($dateObj->getTimestamp() * 1000);

    // Insertion dans la collection "tasks"
    $insertion_requete = new MongoDB\Driver\BulkWrite;
    $insertion_requete->insert(['Description' => $planina, 'Status' => $StatusNumber, 'Nom' => $NomAction, 'IdUserPropr'=>$IdUser, 'datetime' => $dateMongo]);

    try {
        $resultat = $connexion->executeBulkWrite("$base_de_donnees.tasks", $insertion_requete);
        header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$IdUser);
    }  catch (MongoDB\Driver\Exception\AuthenticationException $e) {
        echo "Erreur d'authentification : " . $e->getMessage();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "Erreur d'insertioneeee : " . $e->getMessage();
        var_dump($e);
    }
    
}

// Fermer la connexion à MongoDB si nécessaire
// $connexion = null;

?>



