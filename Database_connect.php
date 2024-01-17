<?php

// class Database {
//     private $host = "localhost";
//     private $port = 27017;

//     protected $connection;

//     public function __construct() {
//         try {
//             $this->connection = new MongoDB\Driver\Manager("mongodb://{$this->host}:{$this->port}");
//         } catch (Exception $e) {
//             die("La connexion à la base de données a échoué : " . $e->getMessage());
//         }
//     }
// }
require 'vendor/autoload.php';

use MongoDB\Client;

class Database {
    private $host = "localhost";
    private $port = 27017;

    protected $connection;

    public function __construct() {
        try {
            $this->connection = new Client("mongodb://{$this->host}:{$this->port}");
        } catch (Exception $e) {
            die("La connexion à la base de données a échoué : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}

?>
