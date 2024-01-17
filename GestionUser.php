<style>
 

.lohany
{
    color: chartreuse;
}
</style>
<?php
require 'vendor/autoload.php'; // Inclure l'autoloader de MongoDB

function afficherListesUtilisateurs($IdUser)
{
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $base_de_donnees = $client->selectDatabase("todolist");
    $collection = $base_de_donnees->selectCollection("user");

      // Requête pour récupérer les tâches correspondant à l'IdUser dans listUser
      $filter = ['_id' => new MongoDB\BSON\ObjectId($IdUser)];
      $options = [];
      
    $cursor = $collection->find($filter, $options);

    echo "<table border='1'>";
    echo "<tr class='lohany'><th>Nom</th><th>Prénom</th><th>Téléphone</th><th>Action</th></tr>";

    foreach ($cursor as $document) {
        echo "<tr>";
        echo "<td>" . $document['Name_clt'] . "</td>";
        echo "<td>" . $document['Prenom_clt'] . "</td>";
        echo "<td>" . $document['Phone_nbr_clt'] . "</td>";
        echo '<td> <a class="btn btn-warning" href="http://localhost/TODAY_LIST/PageUserGestion.php/?idUserEdit=' . $document->_id . '&IdUser='.$_GET['IdUser'].'#contact">Editer</a></td>';
        echo "</tr>";
    }

    echo "</table>";
}

function chercherParIdUser($userId)
{
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $base_de_donnees = $client->selectDatabase("todolist");
    $collection = $base_de_donnees->selectCollection("user");

    // Convertir l'ID en objet MongoDB\BSON\ObjectId
    $userId = new MongoDB\BSON\ObjectId($userId);

    // Chercher l'utilisateur par ID
    $user = $collection->findOne(['_id' => $userId]);

    return $user;
}

function mettreAJourUtilisateur($userId, $name, $prenom, $genre, $phone, $habita, $email, $password)
{
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $base_de_donnees = $client->selectDatabase("todolist");
    $collection = $base_de_donnees->selectCollection("user");

    // Convertir l'ID en objet MongoDB\BSON\ObjectId
    $userId = new MongoDB\BSON\ObjectId($userId);

    // Mettre à jour l'utilisateur
    $result = $collection->updateOne(
        ['_id' => $userId],
        [
            '$set' => [
                'Name_clt' => $name,
                'Prenom_clt' => $prenom,
                'Genre' => $genre,
                'Phone_nbr_clt' => $phone,
                'Habita' => $habita,
                'Email_clt' => $email,
                'PassWords_clt' => $password
            ]
        ]
    );

    return $result->getModifiedCount();
}

?>
