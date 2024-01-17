<?php
require 'vendor/autoload.php';

// Connexion à MongoDB
$connection = new MongoDB\Client("mongodb://localhost:27017");

// Sélectionner la base de données et la collection
$database = $connection->todolist; // Remplacez par le nom de votre base de données
$tasksCollection = $database->tasks;
$usersCollection = $database->user;

// Récupérer les données du formulaire
$taskId = $_GET['taskId'];
$userId = $_GET['userId'];

// Mettre à jour la tâche avec l'ID utilisateur
$updateResult = $tasksCollection->updateOne(
    ['_id' => new MongoDB\BSON\ObjectID($taskId)],
    ['$addToSet' => ['listUser' => $userId]]
);

// Si la mise à jour réussit, vous pouvez également mettre à jour la liste d'utilisateurs dans la collection 'user'
if ($updateResult->getModifiedCount() > 0) {
    $usersCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectID($userId)],
        ['$addToSet' => ['listTache' => $taskId]]
    );

    echo "Utilisateur ajouté avec succès à la tâche.";
    header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$_GET['IdUserInCompte']);
} else {
    echo "Erreur lors de l'ajout de l'utilisateur à la tâche. Car ce personne est déjà assigné dans cette tâche <hr>";
    echo '<a  href="http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$_GET['IdUserInCompte'].'">RETOUR</a>';
}
?>
