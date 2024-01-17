<?php
include 'Database_connect.php';

require 'vendor/autoload.php';

use MongoDB\Client;

class Accueil extends Database {
       
    public function getAllClients() {
        $collection = $this->connection->todolist->user;
        
        // Vous pouvez ajuster la condition de recherche selon vos besoins
        $filter = [
            '$and' => [
                [
                    '$or' => [
                        ['_id' => ['$exists' => true]],
                        ['Name_clt' => ['$exists' => true]],
                        ['Prenom_clt' => ['$exists' => true]],
                        ['Genre' => ['$exists' => true]],
                        ['Phone_nbr_clt' => ['$exists' => true]],
                        ['Habita' => ['$exists' => true]],
                        ['Email_clt' => ['$exists' => true]],
                    ]
                ],
            ]
        ];
        
        $options = [];
        $query = new MongoDB\Driver\Query($filter, $options);

        $cursor = $collection->find($filter, $options);  // Utiliser find() au lieu de executeQuery()

        return $cursor;
    }

    public function ChercherId($email, $password) {
        $collection = $this->connection->todolist->user;
        $result = $collection->findOne(['Email_clt' => $email, 'PassWords_clt' => $password], ['projection' => ['_id' => 1]]);

        if ($result) {
            return $result['_id'];
        }

        return null;
    }

    public function ChercherParPassWord($email, $password) {
        $collection = $this->connection->todolist->user;
        $result = $collection->findOne(['Email_clt' => $email, 'PassWords_clt' => $password]);

        return $result !== null;
    }

}
?>
