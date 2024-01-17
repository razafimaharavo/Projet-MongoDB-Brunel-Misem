<?php 

require 'vendor/autoload.php';
// Remplacez ces valeurs par les informations de votre base de données MongoDB
$serveur = "localhost"; // Adresse du serveur MongoDB
$port = 27017; // Port MongoDB par défaut
$base_de_donnees = "todolist"; // Nom de votre base de données

// Connexion à MongoDB
$connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Fonction pour chercher un document par ID
function chercherParId($id, $connexion)
{
    try {
        $query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $resultat = $connexion->executeQuery("todolist.tasks", $query);

        foreach ($resultat as $document) {
            return (array)$document;
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "Erreur de recherche : " . $e->getMessage();
    }

    return null;
}

function getUserById($connexion, $base_de_donnees, $userId)
{
    $filter = ['_id' => new MongoDB\BSON\ObjectID($userId)];
    $query = new MongoDB\Driver\Query($filter);
    $user = $connexion->executeQuery("$base_de_donnees.user", $query)->toArray();

    return reset($user); // Récupérer le premier élément du tableau
}

// Initialisez la connexion à MongoDB
require 'vendor/autoload.php';
$client = new MongoDB\Client("mongodb://localhost:27017");
$base_de_donnees = "todolist";  // Remplacez par le nom de votre base de données

// Sélectionnez la base de données et les collections
$database = $client->selectDatabase($base_de_donnees);
$tachesCollection = $database->selectCollection('tasks');
$usersCollection = $database->selectCollection('user');
$commentairesCollection = $database->selectCollection('commentaire');

// Fonction pour rechercher les tâches et les commentaires
function rechercher($tachesCollection, $usersCollection, $commentairesCollection, $Status, $recherche = '') {
    // Initialisation de la variable pour stocker le HTML
    $html2 = '';

    // Recherchez les tâches avec le nom, le nom d'utilisateur ou le commentaire correspondant
    $taches = $tachesCollection->find([
        '$and' => [
            [
                '$or' => [
                    ['Nom' => new MongoDB\BSON\Regex($recherche, 'i')],
                    ['Description' => new MongoDB\BSON\Regex($recherche, 'i')],
                ]
            ],
            ['Status' => $Status], // Condition pour Status=1
        ]
    ]);

    // Parcourez les tâches résultantes
    foreach ($taches as $tache) {
        $html2 .= "<tr>";
        // Affichez les détails de la tâche
        $html2 .= "<td>".$tache->Nom."</td>";
        $html2 .= "<td>".$tache->Description."</td>";
        if($Status==0)
        {
            $html2 .= "<td>Pas terminer</td>";
        }
        else
        {
            $html2 .= "<td>Terminer</td>";
        }
        
       
         $html2 .=  '<td>';
         $html2 .=  '<button onclick="myFunction(\'Demo1_' . $tache->_id . '\')" class="w3-button w3-block w3-bar-item w3-button w3-light-green w3-round-large">';
         $html2 .=  '<i class="fa fa-users"></i>Liste user</button>';
         $html2 .=  '<div id="Demo1_' . $tache->_id . '" class="w3-hide w3-container">';
 
        
         if (property_exists($tache, 'listUser')) {
            foreach ($tache->listUser as $userId) {
                $utilisateur = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
                if ($utilisateur) {
                    // $nomsUtilisateurs[] = $utilisateur->Name_clt;
                    $html2 .= "<div>";
                      $imagePath = property_exists($utilisateur, 'image') ? 'http://localhost/Today_list/'.$utilisateur->image : 'http://localhost/Today_list/Sary/user_100px.png';
                    // // Affichage de l'image avec la classe w3-round et w3-margin-right
                      $html2 .= "<img class=\"w3-round-xlarge w3-margin-right\" src=\"$imagePath\" style=\"width:15%; height:30px;\">";
                    // Affichez le nom de l'utilisateur
                    $html2 .= "<span>{$utilisateur->Name_clt}</span>";
                    $html2 .= "</div>";
                }
            }
        }

        
       
         // Affichez les noms des utilisateurs
        //  if (!empty($nomsUtilisateurs)) {
        //     echo "<div>";
        //      $html2 .= "<p>" . implode('<hr>', $nomsUtilisateurs) . "</p>";
        //      echo "</div>";
        //  }
          
         $html2 .= '</div>';
         $html2 .= '</td>';

        $html2 .=  '<td>';
        $html2 .=  '<button onclick="myFunction(\'Demo2_' . $tache->_id . '\')" class="w3-button w3-block w3-bar-item w3-button w3-blue w3-round-large">';
        $html2 .=  '<i class="fa fa-users"></i>Commentaire</button>';
        $html2 .=  '<div id="Demo2_' . $tache->_id . '" class="w3-hide w3-container">';

         // Recherchez les commentaires pour cette tâche
         $commentaires = $commentairesCollection->find(['IdTacheCommenter' => (string) $tache->_id]);

         foreach ($commentaires as $commentaire) {
             // Recherchez l'utilisateur correspondant
             $utilisateur = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($commentaire->IdUserCommenter)]);
            
             // Vérifiez si l'utilisateur existe
             if ($utilisateur) {
                 $html2 .= "<strong>{$utilisateur->Name_clt}</strong> {$commentaire->commentaireUser}<hr>";
             } else {
                 $html2 .= "{$commentaire->commentaireUser}<hr>";
             }

         }
         

        $html2 .=  '</div>';
        $html2 .=  '</td>';

        $html2 .= "<tr>";
    }

    // Affichez le HTML
    echo $html2;
}


?>

<!DOCTYPE html>
<html>
<title>Page public</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://localhost/TODAY_LIST/Razma/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster">
<link rel="stylesheet" href="http://localhost/TODAY_LIST/Razma/w3-theme-blue-grey.css">
<link rel="stylesheet" href="http://localhost/TODAY_LIST/css/bootstrap.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
</style>
<body class="w3-light-grey w3-content" style="max-width:1600px">

<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left w3-black" style="z-index:3;width:280px; text-align:center" id="mySidebar"><br>
  <div class="w3-container">
    <a href="#" onclick="w3_close()" class="w3-hide-large w3-right w3-jumbo w3-padding w3-hover-grey" title="close menu">
      <i class="fa fa-remove"></i>
    </a>
    <img  onmouseover="rotateYDIV()" id="rotate3D" src="Sary/work_authorisation_100px.png" style="width:65%;margin-left:3%; transform: rotateY(360deg);" alt="">
    <br><br>
    <h4><b>GESTION DES TACHES</b></h4>
    <p class="w3-text-grey">To Do List</p>
  </div>
  <br>
  <div class="w3-bar-block" style="text-align:center;">
    <a href="#Liste" onclick="w3_close()" class="w3-bar-item w3-button w3-padding w3-text-teal"><i class="fa fa-th-large fa-fw w3-margin-right"></i>LISTE DES TACHES</a> 
    <a href="http://localhost/Today_list/Accueil.php" onclick="w3_close()" class="w3-bar-item w3-button w3-padding w3-text-purple"><i class="fa fa-th-large fa-fw w3-margin-right"></i>RETOUR A L'ACCUEIL</a>  
  </div>
  <div class="w3-panel w3-large">
    <i class="fa fa-facebook-official w3-hover-opacity"></i>
    <i class="fa fa-instagram w3-hover-opacity"></i>
    <i class="fa fa-snapchat w3-hover-opacity"></i>
    <i class="fa fa-pinterest-p w3-hover-opacity"></i>
    <i class="fa fa-twitter w3-hover-opacity"></i>
    <i class="fa fa-linkedin w3-hover-opacity"></i>
  </div>
</nav>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px">

  <!-- Header -->
  <header id="Liste">
    <a href="#"><img src="http://localhost/TODAY_LIST/Sary/Profile.png" style="width:65px;" class="w3-circle w3-right w3-margin w3-hide-large w3-hover-opacity"></a>
    <span class="w3-button w3-hide-large w3-xxlarge w3-hover-text-grey" onclick="w3_open()"><i class="fa fa-bars"></i></span>
    <div class="w3-container">
    <h1 style="font-family:Lobster;text-align:center; font-size:55px; color:blue;"><b>Listes des tâches</b></h1>
    <div class="w3-section w3-bottombar w3-padding-16">
      <span class="w3-margin-right">Filter:</span> 
      <button class="w3-button w3-black">ALL</button>
      <a href="#PasTerminer" class="w3-button w3-white"><i class="fa fa-diamond w3-margin-right"></i>Pas terminer</a>
      <a href="#Terminer"  class="w3-button w3-white w3-hide-small"><i class="fa fa-photo w3-margin-right"></i>Terminer</a>
    </div>
    </div>
  </header>

                <div id="PasTerminer"></div>
                <h2 style="font-family:cursive;">Liste des évenement qui ne sont pas accomplis</h2>
                <br>
                <form action="http://localhost/TODAY_LIST/Principale_page.php#" method="POST">
                    <div class="input-group mb-3" style="width:35%">
                        <input type="text" name="textRecherche" class="form-control" placeholder="rechercher...">
                        <button class="btn btn-primary" type="submit">Rechercher</button> 
                    </div>
                </form>
                <br>
                <table class="table table-striped table-inverse table-responsive">
                    <thead class="thead-inverse">
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                  
                    <tbody>
    <?php
    // Vérifiez si le formulaire de recherche est soumis
    if (isset($_POST['textRecherche'])) {
        $marque = $_POST['textRecherche'];
        rechercher($tachesCollection, $usersCollection, $commentairesCollection, 0, $marque);
    } else {
        rechercher($tachesCollection, $usersCollection, $commentairesCollection, 0);
    }

    ?>
</tbody>
        </table>
                <br>
                <div id="Terminer"></div>
                <h2 style="font-family:cursive;">Liste des évenement qui sont accomplis</h2>
                <br>
                <form action="http://localhost/TODAY_LIST/Principale_page.php#Terminer" method="POST">
                    <div class="input-group mb-3" style="width:35%">
                        <input type="text" name="textRechercheAccomplie" class="form-control" placeholder="rechercher...">
                        <button class="btn btn-primary" type="submit">Rechercher</button> 
                    </div>
                </form>
                <br>
                <table class="table table-striped table-inverse table-responsive">
                    <thead class="thead-inverse">
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                            // Vérifiez si le formulaire de recherche est soumis
                            if (isset($_POST['textRechercheAccomplie'])) {
                                $marque = $_POST['textRechercheAccomplie'];
                                rechercher($tachesCollection, $usersCollection, $commentairesCollection, 1, $marque);
                            } else {
                                rechercher($tachesCollection, $usersCollection, $commentairesCollection, 1);
                            }

                        ?>

                        <style>
                            .highlight {
                                background-color: Yellow;
                            }
                        </style>
                    </tbody>
                </table>



 
  
  <!-- Contact Section -->
  <hr>
 
  <div class="w3-black w3-center w3-padding-24 w3-round-xlarge w3-text-indigo">Mahay ny zavatra rehetra aho amin'ilay mampahery ahy</a></div>

</div>

<script>
// Script to open and close sidebar
function w3_open() {
    document.getElementById("mySidebar").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
 
function w3_close() {
    document.getElementById("mySidebar").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

// Accordion
function myFunction(id) {
  var x = document.getElementById(id);
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show"; 
    x.previousElementSibling.className += " w3-red";
  } else { 
    x.className = x.className.replace(" w3-show", "");
    x.previousElementSibling.className = 
    x.previousElementSibling.className.replace(" w3-red", "");
  }
}

var x, y, n = 0, ny = 0, rotINT, rotYINT;

// Appeler la fonction automatiquement au chargement de la page
window.onload = function () {
    rotateYDIV();
};

function rotateYDIV() {
    y = document.getElementById("rotate3D");
    clearInterval(rotYINT);
    rotYINT = setInterval("startYRotate()", 10);
}

function startYRotate() {
    ny = ny + 1;
    y.style.transform = "rotateY(" + ny + "deg)";
    y.style.webkitTransform = "rotateY(" + ny + "deg)";
    y.style.OTransform = "rotateY(" + ny + "deg)";
    y.style.MozTransform = "rotateY(" + ny + "deg)";
    if (ny == 180 || ny >= 360) {
        clearInterval(rotYINT);
        if (ny >= 360) {
            ny = 0;
        }
    }
}
</script>

</body>

<!-- Mirrored from www.w3schools.com/w3css/tryit.asp?filename=tryw3css_templates_portfolio&stacked=h by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 27 Jan 2020 01:36:24 GMT -->
</html>
