<?php

if (isset($_POST['CommenterBtn'])) {
    // Récupérez les valeurs du formulaire
    $IdUserCommenter = $_POST['IdUserComs'];
    $IdTacheCommenter = $_POST['IdTacheComs'];
    $commentaireUser = $_POST['commentaireUser'];

    if (!empty($IdUserCommenter) && !empty($IdTacheCommenter) && !empty($commentaireUser)) {

        // Initialiser la connexion à MongoDB
        require 'vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $base_de_donnees = "todolist";  

        // Sélectionner la base de données et la collection
        $database = $client->selectDatabase($base_de_donnees);
        $collection = $database->selectCollection('commentaire');

        // Créer le document à insérer
        $document = [
            'IdUserCommenter' => $IdUserCommenter,
            'IdTacheCommenter' => $IdTacheCommenter,
            'commentaireUser' => $commentaireUser,
            'dateCommentaire' => new MongoDB\BSON\UTCDateTime(),
        ];

        // Insérer le document dans la collection
        $result = $collection->insertOne($document);

        // Vérifier le succès de l'opération
        if ($result->getInsertedCount() > 0) {
            echo "Commentaire ajouté avec succès.";
            header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$IdUserCommenter);
        } else {
            echo "Erreur lors de l'ajout du commentaire.";
        }
    } else {
        echo "Veuillez remplir tous les champs du formulaire.";
    }
}

