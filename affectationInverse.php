<?php

$serveur = "localhost";
$port = 27017;
$base_de_donnees = "todolist";
$collection = "tasks";

// Connexion à MongoDB
$connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");

function chercherParId($id, $connexion)
{
    $query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectId($id)]);
    $resultat = $connexion->executeQuery("todolist.tasks", $query);

    foreach ($resultat as $document) {
        return [
            $document->_id->__toString(),
            $document->Description,
            $document->Status,
            $document->Nom
        ];
    }

    return [];
}

// SUPPRESSION
if (isset($_GET['indice'])) {
    $id = chercherParId($_GET['indice'], $connexion)[0];
    

    try {
        // Création d'une requête de mise à jour MongoDB
        $mise_a_jour_requete = new MongoDB\Driver\BulkWrite;
        
        // Spécification de la mise à jour du document avec l'ID correspondant
        $mise_a_jour_requete->update(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => ['Status' => 0]],
            ['multi' => false, 'upsert' => false]
        );

        // Exécution de la requête de mise à jour
        $resultat = $connexion->executeBulkWrite("$base_de_donnees.tasks", $mise_a_jour_requete);

        // Redirection vers la page d'ajout après la mise à jour
        header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$_GET['IdUser']);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        // En cas d'erreur, affichage du message d'erreur
        echo "Erreur de mise à jour : " . $e->getMessage();
    }
}

header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$_GET['IdUser']);

