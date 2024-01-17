<?php
// Inclure le code d'initialisation de MongoDB (autoload, création de la connexion, etc.)
require 'vendor/autoload.php';
require 'GestionUser.php';

// Initialiser la connexion à MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");

// Votre base de données MongoDB
$database = $client->todolist;

// Votre collection MongoDB
$collection = $database->user;

$htmlImage='';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Vérifier si un fichier a été sélectionné
    if (!empty($_FILES['image']['name'])) {
        // Récupérer l'ID de l'utilisateur depuis le formulaire
        $userId = isset($_POST['userId']) ? $_POST['userId'] : '';

        // Chemin où l'image sera sauvegardée sur le serveur
        $imagePath = "ImageUser/" . basename($_FILES["image"]["name"]);

        // Télécharger l'image
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            // Afficher l'image téléchargée dans l'aperçu
            $htmlImage.="<img id='uploadedImage' src='$imagePath' alt='Aperçu de l'image'>";

            // Insérer le chemin de l'image dans la collection MongoDB du client avec l'ID spécifié
            $updateResult = $collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($userId)],
                ['$set' => ['image' => $imagePath]]
            );

            if ($updateResult->getModifiedCount() > 0) {
                echo "L'image a été enregistrée avec succès pour le client avec l'ID : $userId";
                header('location: http://localhost/Today_list/PageUserGestion.php/?IdUser=' .  $userId . '');
            } else {
                echo "Erreur lors de l'enregistrement de l'image.";
            }
        } else {
            echo "Erreur lors du téléchargement de l'image.";
        }
    } else {
        echo "Aucun fichier sélectionné.";
    }
}

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

function getUserDetails($userId) {
    // Initialiser la connexion à MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");

    // Votre base de données MongoDB
    $database = $client->todolist;

    // Votre collection MongoDB
    $collection = $database->user;

    // Récupérer les détails de l'utilisateur à partir de la collection MongoDB
    $userDetails = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

    // Récupérer le chemin de l'image de l'utilisateur (ajuster le champ selon votre structure)
    $userImagePath = isset($userDetails['image']) ? $userDetails['image'] : '';

    // Récupérer le nom d'utilisateur (ajuster le champ selon votre structure)
    $userFirstname = isset($userDetails['Name_clt']) ? $userDetails['Name_clt'] : 'Utilisateur inconnu';
    $UserName = isset($userDetails['Prenom_clt']) ? $userDetails['Prenom_clt'] : 'Utilisateur inconnu';
    $userGender = isset($userDetails['Genre']) ? $userDetails['Genre'] : 'Utilisateur inconnu';
    $userPhone = isset($userDetails['Phone_nbr_clt']) ? $userDetails['Phone_nbr_clt'] : 'Utilisateur inconnu';
    $userLieu = isset($userDetails['Habita']) ? $userDetails['Habita'] : 'Utilisateur inconnu';

    // Vérifier si l'image existe
    if (file_exists($userImagePath)) {
        $userImageSrc ='http://localhost/Today_list/'.$userImagePath;
    } else {
        // Chemin d'une image de remplacement si l'image de l'utilisateur n'existe pas
        $userImageSrc = 'http://localhost/Today_list/Sary/user_100px.png';
    }

    // Retourner un tableau avec les détails de l'utilisateur
    return [
        'userFirstname' => $userFirstname,
        'userImageSrc' => $userImageSrc,
        'userName'=> $UserName, 
        'userGenre'=> $userGender, 
        'userPhone'=> $userPhone, 
        'userLieu'=> $userLieu, 
    ];
}



// Vérifier si l'ID de l'utilisateur est défini dans l'URL
$idClt = isset($_GET['IdUser']) ? $_GET['IdUser'] : '';

// Appeler la fonction pour récupérer les détails de l'utilisateur
$userDetails = getUserDetails($idClt);

// Extraire les valeurs du tableau retourné
$userFirstname = $userDetails['userFirstname'];
$userImageSrc = $userDetails['userImageSrc'];
$userName = $userDetails['userName'];
$userGenre = $userDetails['userGenre'];
$userPhone = $userDetails['userPhone'];
$userLieu = $userDetails['userLieu'];

include 'Class_accueil.php';

$AuthentHandler = new Accueil();
$ListeClients = $AuthentHandler->getAllClients();

$html = '';

foreach ($ListeClients as $client) {
    $IdUser=$client->_id;
    $nom = $client->Name_clt;
    $prenom = $client->Prenom_clt;
    $genre = $client->Genre;
    $Habita = $client->Habita;
    $EmailName = $client->Email_clt;

     // Vérifier si la clé "image" existe dans l'objet $client
     $imagePath = property_exists($client, 'image') ? 'http://localhost/Today_list/'.$client->image : 'http://localhost/Today_list/Sary/user_100px.png';

    //  echo "<br><br><br><br>Image Path for $nom $prenom: $imagePath <br>";

    // Ouverture de la balise <a> avec une fonction JavaScript onclick
    $html .= "<a href=\"javascript:void(0)\" class=\"w3-bar-item w3-button w3-border-bottom test w3-hover-light-grey\" onclick=\"openMail('$nom');w3_close();\" id=\"$nom\">";
    
    $html .= "<div class=\"w3-container\">";

    // Affichage de l'image avec la classe w3-round et w3-margin-right
    $html .= "<img class=\"w3-round w3-margin-right\" src=\"$imagePath\" style=\"width:15%; height:42px;\">";

    // Affichage du nom et prénom avec la classe w3-opacity w3-large
    $html .= "<span class=\"w3-opacity w3-large\">$nom $prenom</span>";
    if(isset($_GET['idTache']))
    {
      $html .= ' <a class="btn btn-warning" href="http://localhost/TODAY_LIST/Ajout_userInTache.php/?taskId=' . $_GET['idTache'] . '&IdUserInCompte='.$_GET['IdUser'].'&userId=' . $IdUser . '">Ajouter</a>';
    }
    

    // Le reste des informations (ici le genre) peut être affiché dans une balise <h6>
    $html .= "<h6>Genre: $genre</h6>";

    // Fermeture de la balise <div> et <a>
    $html .= "</div>";
    $html .= "</a>";
}

function afficherTachesParIdUser($client, $base_de_donnees, $idUser)
{
    try {
        // Sélectionner la base de données et la collection
        $database = $client->selectDatabase($base_de_donnees);
        $collection = $database->selectCollection('tasks');
        $usersCollection = $database->selectCollection('user');
        $commentairesCollection = $database->selectCollection('commentaire');

        // Requête pour récupérer les tâches correspondant à l'IdUser dans listUser
        $filter = ['listUser' => $idUser, 'Status'=>0];
        $options = [];

        // Exécuter la requête
        $result = $collection->find($filter, $options);

        // Variable pour stocker le HTML
        $html1 = '';

        // Afficher les résultats
        foreach ($result as $document) {
            $html1 .= "<tr>";
            $html1 .= "<td>" . $document->Nom . " ; </td>";
            $html1 .= "<td>" . $document->Description . " ; </td>";
            $html1 .= "<td>" . "Pas encore terminé" . " ; </td>";
            if (property_exists($document, 'datetime') && $document->datetime instanceof MongoDB\BSON\UTCDateTime) {
                $dateObj = $document->datetime->toDateTime();
                $html1 .= "<td>" . $dateObj->format('Y-m-d H:i') . " </td>";
            } else {
                $html1 .= "<td>" . "Pas de date" . "</td>";
            }

            $html1 .=  '<button onclick="myFunction(\'Demo2_' . $document->_id . '\')" class="w3-button w3-block w3-bar-item w3-button w3-blue w3-round-large">';
            $html1 .=  '<i class="fa fa-users"></i>Commentaire</button>';
            $html1 .=  '<div id="Demo2_' . $document->_id . '" class="w3-hide w3-container">';
             //Recherchez les commentaires pour cette tâche
            $commentaires = $commentairesCollection->find(['IdTacheCommenter' => (string) $document->_id]);

            foreach ($commentaires as $commentaire) {
                // Recherchez l'utilisateur correspondant
                $utilisateur = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($commentaire->IdUserCommenter)]);
                
                // Vérifiez si l'utilisateur existe
                if ($utilisateur) {
                    $html1 .= "<strong>{$utilisateur->Name_clt}</strong> {$commentaire->commentaireUser}<hr>";
                } else {
                    $html1 .= "{$commentaire->commentaireUser}<hr>";
                }

            }
            $html1 .=  '</div>';


            $html1 .= ' <a class="btn btn-success w3-margin-top w3-margin-left" href="http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$_GET['IdUser'].'&IdTacheComs='.$document->_id.'"><i class="fa fa-paper-plane w3-margin-right"></i>Commenter</a>';
            $html1 .="<hr>";
            // Ajoutez d'autres colonnes à afficher si nécessaire
            $html1 .= "</tr>";
        }

        // Retournez le HTML
        return $html1;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "Erreur lors de la recherche : " . $e->getMessage();
        // Gérez les erreurs de recherche ici
    }
}

if (isset($_GET['IdTacheComs'])) {
    echo '<script>
          document.addEventListener("DOMContentLoaded", function() {
              var CommenterElement = document.getElementById("Commentaire");
              if (CommenterElement) {
                CommenterElement.style.display = "block";
              }
          });
      </script>';
  }

?>


<!DOCTYPE html>
<html>
<title>Compte user</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://localhost/TODAY_LIST/Razma/w3-theme-blue-grey.css">
<link rel="stylesheet" href="http://localhost/TODAY_LIST/Razma/w3.css">
<link rel="stylesheet" href="http://localhost/TODAY_LIST/css/bootstrap.min.css">
<style>
html, body, h1, h2, h3, h4, h5 {font-family: "Open Sans", sans-serif}
.table-inverse
{
    color: cadetblue;
}

.thead-inverse
{
    color: rgb(0, 60, 255);
}
</style>
<body class="w3-theme-l5">

<!-- Navbar -->
<div class="w3-top">
 <div class="w3-bar w3-theme-d2 w3-left-align w3-large">
  <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-theme-d2" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>
  <a href="#" class="w3-bar-item w3-button w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i><img  src="http://localhost/TODAY_LIST/Sary/work_authorisation_100px.png" style="width:35px; margin-left:15px;" alt=""></a>
  <a href="#" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="News"><i class="fa fa-globe"></i></a>
  <a href="#" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Account Settings"><i class="fa fa-user"></i></a>
  <a href="#" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Messages"><i class="fa fa-envelope"></i></a>
  <a href="#" class="w3-bar-item w3-button w3-hide-small w3-right w3-padding-large w3-hover-white" title="My Account">
    <img onclick="w3_open()" src=" <?php echo $userImageSrc; ?>" class="w3-circle" style="height:28px;width:28px" alt="Avatar">
</a>
 </div>
</div>

 <!-- Commentaire Modal -->
 <div id="Commentaire" class="w3-modal">
    <div class="w3-modal-content w3-animate-zoom w3-round-xlarge" style="width: 750px; text-align:center">
        <div class="w3-container w3-black w3-display-container" style="background: linear-gradient(to bottom right, #000000 0%, #66ccff 100%);">
            <a href="http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser=<?php echo $_GET['IdUser']?>"  onclick="document.getElementById('Commentaire').style.display='none'" class="w3-button w3-display-topright w3-large">x</a>
            <h1>Votre commentaire sur <?php if (isset($_GET['IdTacheComs'])) echo chercherParId($_GET['IdTacheComs'], $connexion)['Nom']; ?></h1><hr>
        </div>
        <div>
        <p>Veuillez inserer votre commentaire s'il vous plaît: <hr> </p>
        <form action="<?php echo "http://localhost/TODAY_LIST/InsererComsUser.php" ?>" method="POST">
        <input type="hidden" name="IdTacheComs" value="<?php if (isset($_GET['IdTacheComs'])) echo $_GET['IdTacheComs']; ?>">
        <input type="hidden" name="IdUserComs" value="<?php if (isset($_GET['IdUser'])) echo $_GET['IdUser']; ?>">
            <p><textarea class="w3-input w3-padding-16 " type="text" placeholder="Commentaire .." required name="commentaireUser"></textarea></p>
            <p><button class="btn btn-primary " name="CommenterBtn" type="submit"><i class="fa fa-paper-plane w3-margin-right"></i>Envoyer</button></p>
        </form>
        </div>
    </div>
    </div>


<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">    
  <!-- The Grid -->
  <div class="w3-row">
    <!-- Left Column -->
    <div class="w3-col m2">
      <!-- Profile -->
      <div class="w3-card w3-round w3-white">
        <div class="w3-container">
         <h4 class="w3-center">Mon Profile</h4>
         <p class="w3-center"><img src= "<?php echo $userImageSrc; ?>" class="w3-circle" style="height:106px;width:106px" alt="Avatar"></p>
         <p class="w3-center"><?php echo $userFirstname; ?></p>
         

         <hr>
         <p><i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i><?php echo $userName; ?></p>
         <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i><?php echo $userLieu; ?> </p>
         <p><i class="fa fa-phone fa-fw w3-margin-right w3-text-theme"></i><?php echo $userPhone; ?></p>
        </div>
      </div>
      <br>
      
      <!-- Accordion -->
      <div class="w3-card w3-round">
        <div class="w3-white">
          <button onclick="myFunction('Demonstr1')" class="w3-button w3-block w3-theme-l1 w3-left-align"><i class="fa fa-user fa-fw w3-margin-right"></i>Changer mon Profile</button>
          <div id="Demonstr1" class="w3-hide w3-container">
            <form action="http://localhost/Today_list/PageUserGestion.php" method="post" enctype="multipart/form-data">
                <label style="font-family:cursive;" for="image"> <strong>Sélectionnez une image :</strong></label>
                <input type="file" name="image" id="image" accept="image/*" required>
                <br>
                <input type="hidden" name="userId" value="<?php echo isset($_GET['IdUser']) ? $_GET['IdUser'] : ''; ?>"> <!-- Remplacez par l'ID réel du client -->
                <input type="submit" class="btn btn-primary" name="submit" value="Sauvegarder">
            </form>
          </div>
          <button onclick="myFunction('Demo3')" class="w3-button w3-block w3-theme-l1 w3-left-align"><i class="fa fa-tasks fa-fw w3-margin-right"></i> Liste de mes tâches <span class='spinner-border spinner-border-sm '></span></button>
          <div id="Demo3" class="w3-hide w3-container">
         <div class="w3-row-padding">
         <br>
         <?php
            $idUser = $_GET['IdUser']; // Assurez-vous que cette valeur est sécurisée contre les injections
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $html1 = afficherTachesParIdUser($client, 'todolist', $idUser);
            echo $html1;
         ?>

         </div>
          </div>
        </div>      
      </div>
      <br>
      
      <!-- Interests --> 
      <div class="w3-card w3-round w3-white w3-hide-small">
        <div class="w3-container">
          <p>Interests</p>
          <p>
            <span class="w3-tag w3-small w3-theme-d5">News</span>
            <span class="w3-tag w3-small w3-theme-d2">Games</span>
            <span class="w3-tag w3-small w3-theme-d1">Friends</span>
            <span class="w3-tag w3-small w3-theme">Games</span>
            <span class="w3-tag w3-small w3-theme-l1">Friends</span>
            <span class="w3-tag w3-small w3-theme-l2">Food</span>
            <span class="w3-tag w3-small w3-theme-l3">Design</span>
            <span class="w3-tag w3-small w3-theme-l4">Art</span>
            <span class="w3-tag w3-small w3-theme-l5">Photos</span>
          </p>
        </div>
      </div>
      <br>
      
      <!-- Alert Box -->
      <div class="w3-container w3-display-container w3-round w3-theme-l4 w3-border w3-theme-border w3-margin-bottom w3-hide-small">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-theme-l3 w3-display-topright">
          <i class="fa fa-remove"></i>
        </span>
        <p><strong>Hey!</strong></p>
        <p>Veuillez verifie bien votre profile et votre tâche s'il vous plaît</p>
      </div>
    
    <!-- End Left Column -->
    </div>
    
    <!-- Middle Column -->
    <div class="w3-col m7">
    
      <div class="w3-row-padding">
        <div class="w3-col m12">
          <div class="w3-card w3-round w3-white">
            <div class="w3-container w3-padding">
                    <form action="<?php if (isset($_GET['id'])) echo "http://localhost/TODAY_LIST/Edit.php";
                                else echo "http://localhost/TODAY_LIST/inserer.php" ?>" method="POST">
                    
                    <input type="hidden" name="id" value="<?php if (isset($_GET['id'])) echo $_GET['id']; ?>">
                    <input type="hidden" name="idUserProp" value="<?php if (isset($_GET['IdUser'])) echo $_GET['IdUser']; ?>">
                    <div class="mb-3">
                        <label for="plan"><div class="rounded p-1 mb-1 bg-primary text-white" style="--bs-bg-opacity: 0.4;">Votre objectif que vous voulez organiser</div></label>
                        <textarea name="Description" id="Description" class="form-control" placeholder="Votre plan .." aria-describedby="helpId" required><?php if (isset($_GET['id'])) echo chercherParId($_GET['id'], $connexion)['Description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="Heure"><div class="rounded p-1 mb-1 bg-primary text-white" style="--bs-bg-opacity: 0.4;">Status</div></label>
                        <input type="number" name="Status" id="Status" max=1 min=0 class="form-control" placeholder="Status .. 0: Pas terminer;  1: Terminer" aria-describedby="helpId" value="<?php if (isset($_GET['id'])) echo chercherParId($_GET['id'], $connexion)['Status']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="Heure"><div class="rounded p-1 mb-1 bg-primary text-white" style="--bs-bg-opacity: 0.4;">Heure de sortant</div></label>
                        <input type="text" name="Nom" id="Nom" class="form-control" placeholder="Nom de tâche à faire .." aria-describedby="helpId" value="<?php if (isset($_GET['id'])) echo chercherParId($_GET['id'], $connexion)['Nom']; ?>" required>
                    </div>
                    <?php
            // Récupération de la date et de l'heure depuis la base de données
            $data = null;
            if (isset($_GET['id'])) {
                $data = chercherParId($_GET['id'], $connexion);
            }
            
            // Si des données sont récupérées, on affiche la date et l'heure séparément
            if ($data !== null && isset($data['datetime'])) {
                $utcDateTime = $data['datetime'];
                $dateTime = $utcDateTime->toDateTime(); // Convertir UTCDateTime en objet DateTime
                $date = $dateTime->format('Y-m-d'); // Extraction de la date
                $time = $dateTime->format('H:i'); // Extraction de l'heure
            } else {
                // Si aucune donnée n'est disponible, initialisez les valeurs par défaut
                $date = date('Y-m-d');
                $time = date('H:i');
            }
                    ?>
                    <div class="form-group">
                        <label for="Heure"><div class="rounded p-1 mb-1 bg-primary text-white" style="--bs-bg-opacity: 0.4;">Date finale :</div></label>
                        <input type="date" name="date" id="date" class="form-control" value="<?php echo $date; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="Heure"><div class="rounded p-1 mb-1 bg-primary text-white" style="--bs-bg-opacity: 0.4;">Heure de fin :</div></label>
                        <input type="time" name="time" id="time" class="form-control" value="<?php echo $time; ?>" required>
                    </div>
                    <br>
                    <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
                    <a href="#PasTerminer" class="btn btn-danger">Voir l'enregistrement</a>
                </form>
            </div>
          </div>
        </div>
      </div>
      
      <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
      <div id="PasTerminer"></div>
                <h2 style="font-family:cursive;">Liste des tâches que vous avez créer et qui ne sont pas accomplis</h2>
                <br>
                <form action="" method="POST">
                    <div class="input-group mb-3" style="width:55%">
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
                            <th>Utilisateur</th>
                            <th>Date fin</th>
                            <th>Assigné</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                  
                    <tbody>
    <?php
    $serveur = "localhost"; // Adresse du serveur MongoDB
    $port = 27017; // Port MongoDB par défaut
    $base_de_donnees = "todolist";
    $idUserPropr = $_GET['IdUser'];

    // Connexion à MongoDB
    $connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    function recherche($collection, $connexion, $base_de_donnees,  $idUserPropr, $search = "")
    {
        try {
            $filter = [
                '$and' => [
                    [
                        '$or' => [
                            ['_id' => ['$regex' => $search, '$options' => 'i']],
                            ['Description' => ['$regex' => $search, '$options' => 'i']],
                            ['Nom' => ['$regex' => $search, '$options' => 'i']],
                        ],
                    ],
                    ['Status' => 0], // Condition pour Status=0
                    ['IdUserPropr' => $idUserPropr], // Condition pour filtrer par IdUserPropr
                ]
            ];

            // Préparer la requête
            $query = new MongoDB\Driver\Query($filter);

            // Exécuter la requête
            $result = $connexion->executeQuery("$base_de_donnees.$collection", $query);

            // Afficher les résultats
            foreach ($result as $document) {
                echo "<tr>";
                echo "<td>" . $document->Nom . "</td>";
                echo "<td>" . $document->Description . "</td>";
                echo "<td>" . "Pas encore términer" . "</td>";
          
                echo '<td>';
                echo '<button onclick="myFunction(\'Demo2_' . $document->_id . '\')" class="w3-button w3-block w3-theme-l1 w3-left-align">';
                echo '<i class="fa fa-users"></i>Liste des utilisateurs</button>';
                echo '<div id="Demo2_' . $document->_id . '" class="w3-hide w3-container">';

                if (property_exists($document, 'listUser') && is_array($document->listUser)) {
                    foreach ($document->listUser as $userId) {
                        $user = getUserById($connexion, $base_de_donnees, $userId);
                        if ($user) {
                            echo  $user->Name_clt ." ". $user->Prenom_clt . '<hr>';
                            // Affichez d'autres informations d'utilisateur si nécessaire
                        }
                    }
                }

                echo '</div>';
                echo '</td>';
                if (property_exists($document, 'datetime') && $document->datetime instanceof MongoDB\BSON\UTCDateTime) {
                    $dateObj = $document->datetime->toDateTime();
                    echo "<td>" . $dateObj->format('Y-m-d H:i') . "</td>";
                } else {
                    echo "<td>" . "Pas de date" . "</td>";
                }
                echo '<td><a class="btn btn-primary btn-block" onclick="return confirm(\'Êtes vous sur que vous allez ajouter ? \')" href="http://localhost/TODAY_LIST/PageUserGestion.php/?idTache=' . $document->_id . '&IdUser='.$_GET['IdUser'].'">Ajouter</a>';
                echo '<td><a class="btn btn-primary btn-block" onclick="return confirm(\'Êtes vous sur que vous avez accomplir ? \')" href="http://localhost/TODAY_LIST/Affectation.php/?indice=' . $document->_id . '&IdUser='.$_GET['IdUser'].'">REUISSIT</a>';
                echo ' <a class="btn btn-danger btn-block" onclick="return confirm(\'Vous voulez supprimer vraiment le tâche '.$document->Nom.' ?\')" href="http://localhost/TODAY_LIST/supprimeList.php/?idList=' . $document->_id . '&IdUser='.$_GET['IdUser'].'">SUPPR</a>';
                echo ' <a class="btn btn-warning" href="http://localhost/TODAY_LIST/PageUserGestion.php/?id=' . $document->_id . '&IdUser='.$_GET['IdUser'].'#contact">Editer</a></td>';

                echo "</tr>";
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la recherche : " . $e->getMessage();
            // Gérez les erreurs de recherche ici
        }
    }

    function getUserById($connexion, $base_de_donnees, $userId)
    {
        $filter = ['_id' => new MongoDB\BSON\ObjectID($userId)];
        $query = new MongoDB\Driver\Query($filter);
        $user = $connexion->executeQuery("$base_de_donnees.user", $query)->toArray();

        return reset($user); // Récupérer le premier élément du tableau
    }

    if (isset($_POST['textRecherche'])) {
        $marque = $_POST['textRecherche'];
        recherche("tasks", $connexion, $base_de_donnees, $idUserPropr, $marque);
    } else {
        recherche("tasks", $connexion, $base_de_donnees, $idUserPropr, "");
    }
    ?>
</tbody>
        </table>
                <br>
                <div id="Terminer"></div>
                <h2 style="font-family:cursive;">Liste des tâches que vous avez créer et qui sont accomplis</h2>
                <br>
                <form action="" method="POST">
                    <div class="input-group mb-3" style="width:55%">
                        <input type="text" name="textRechercheAccomp" class="form-control" placeholder="rechercher...">
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
                            <th>Date fin</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $base_de_donnees = "todolist"; 
                        // Connexion à MongoDB
                        $connexion = new MongoDB\Driver\Manager("mongodb://localhost:27017");
                        function rechercheAcco($collection, $connexion, $base_de_donnees, $search = "")
                        {
                            try {

                                $filter = [
                                    '$and' => [
                                        [
                                            '$or' => [
                                                ['_id' => ['$regex' => $search, '$options' => 'i']],
                                                ['Description' => ['$regex' => $search, '$options' => 'i']],
                                                ['Nom' => ['$regex' => $search, '$options' => 'i']],
                                            ],
                                        ],
                                        ['Status' => 1], // Condition pour Status=1
                                        ['IdUserPropr' => $_GET['IdUser']], 
                                    ]
                                ];

                                // Préparer la requête
                                $query = new MongoDB\Driver\Query($filter);

                                // Exécuter la requête
                                $result = $connexion->executeQuery("$base_de_donnees.$collection", $query);

                                // Afficher les résultats
                                foreach ($result as $document) {
                                    echo "<tr>";
                                    echo "<td>" . $document->Nom . "</td>";
                                    echo "<td>" . $document->Description . "</td>";
                                    echo "<td>" . "Términer" . "</td>";

                                    echo '<td>';
                                    echo '<button onclick="myFunction(\'Demo4_' . $document->_id . '\')" class="w3-button w3-block w3-theme-l1 w3-left-align">';
                                    echo '<i class="fa fa-users"></i>Liste des utilisateurs</button>';
                                    echo '<div id="Demo4_' . $document->_id . '" class="w3-hide w3-container">';

                                    if (property_exists($document, 'listUser') && is_array($document->listUser)) {
                                        foreach ($document->listUser as $userId) {
                                            $user = getUserById($connexion, $base_de_donnees, $userId);
                                            if ($user) {
                                                echo  $user->Name_clt ." ". $user->Prenom_clt . '<hr>';
                                                // Affichez d'autres informations d'utilisateur si nécessaire
                                            }
                                        }
                                    }

                                    echo '</div>';
                                    echo '</td>';

                                     // Afficher la date et l'heure si le champ 'MaDate' est présent dans le document
                                    if (property_exists($document, 'datetime') && $document->datetime instanceof MongoDB\BSON\UTCDateTime) {
                                        $dateObj = $document->datetime->toDateTime();
                                        echo "<td>" . $dateObj->format('Y-m-d H:i') . "</td>";
                                    } else {
                                        echo "<td>" . "Pas de date" . "</td>";
                                    }
                                
                                    echo '<td><a class="btn btn-primary btn-block" onclick="return confirm(\'Êtes vous sur de aller refaire cette tâche ? \')" href="http://localhost/TODAY_LIST/AffectationInverse.php/?indice=' . $document->_id . '&IdUser='.$_GET['IdUser'].'">REFAIRE</a>';
                                    echo ' <a class="btn btn-danger btn-block" onclick="return confirm(\'Vous voulez supprimer vraiment le tâche '.$document->Nom.' ?\')" href="http://localhost/TODAY_LIST/supprimeList.php/?idList=' . $document->_id . '&IdUser='.$_GET['IdUser'].'">SUPPR</a>';
                                    echo ' <a class="btn btn-warning" href="http://localhost/TODAY_LIST/PageUserGestion.php/?id=' . $document->_id . '&IdUser='.$_GET['IdUser'].'#contact">Editer</a></td>';
                                    echo "</tr>";
                                }
                            } catch (MongoDB\Driver\Exception\Exception $e) {
                                echo "Erreur lors de la recherche : " . $e->getMessage();
                                // Gérez les erreurs de recherche ici
                            }
                        }

                        if (isset($_POST['textRechercheAccomp'])) {
                            $marque = $_POST['textRechercheAccomp'];
                            rechercheAcco("tasks", $connexion, $base_de_donnees, $marque);
                        } else {
                            rechercheAcco("tasks", $connexion, $base_de_donnees, "");
                        }
                        ?>

                        <style>
                            .highlight {
                                background-color: Yellow;
                            }
                        </style>
                    </tbody>
                </table>
    </div>

      
    <!-- End Middle Column -->
    </div>
    
    <!-- Right Column -->
    <div class="w3-col m3">
      <!-- Side Navigation -->
        <nav class="w3-sidebar w3-bar-block w3-collapse w3-white w3-animate-right w3-card" style="z-index:1;width:340px;" id="mySidebar">
        <a href="javascript:void(0)" onclick="w3_close()" title="Close Sidemenu" 
        class="w3-bar-item w3-button w3-hide-large w3-large">Close <i class="fa fa-remove"></i></a>
        <a href="javascript:void(0)" class="w3-bar-item w3-button w3-dark-grey w3-button w3-hover-black w3-left-align" onclick="document.getElementById('id01').style.display='block'">Ajout d'utilisateur et gestion de compte</a>
        <a id="myBtn" onclick="myFonct('Demo1')" href="javascript:void(0)" class="w3-bar-item w3-button"><i class="fa fa-inbox w3-margin-right"></i>Liste des utilisateurs existant<i class="fa fa-caret-down w3-margin-left"></i></a>
        <div id="Demo1" class="w3-hide w3-animate-left">
            <?php
                echo $html;
            ?>
            
        </div>
        <a id="myBtn" onclick="myFonct('Demo5')" href="javascript:void(0)" class="w3-button w3-block w3-bar-item w3-button w3-blue"><i class="fa fa-pencil w3-margin-right"></i>Editer mon renseigement<i class="fa fa-caret-down w3-margin-left"></i><?php if(isset($_GET['idUserEdit'])){ echo "<span class='spinner-grow text-warning'></span>";}?></a>
        <div id="Demo5" class="w3-hide w3-animate-left">
        <?php
        // Appel de la fonction pour afficher les listes d'utilisateurs
        afficherListesUtilisateurs($_GET['IdUser']);

    if (isset($_GET['idUserEdit'])) {
        $userId = $_GET['idUserEdit'];

        // Récupérer les données de l'utilisateur à éditer
        $user = chercherParIdUser($userId);

        // Vérifier si l'utilisateur existe
        if ($user) {
            ?>
            
            <form action="http://localhost/Today_list/editUserProcess.php" method="post">
                <input type="hidden" name="userIdEdit" value="<?php echo $user->_id; ?>">

                <label for="name">Nom :</label>
                <input class="form-control" type="text" name="name" value="<?php echo $user->Name_clt; ?>" required>

                <label for="prenom">Prénom :</label>
                <input type="text" class="form-control" name="prenom" value="<?php echo $user->Prenom_clt; ?>" required>

                <label for="name">Genre :</label>
                <select class="w3-select" name="Genre" required>
                <option value="<?php echo $user->Genre; ?>" selected><?php echo $user->Genre; ?></option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
                </select>

                <label for="prenom">Numéro téléphone :</label>
                <input type="text" class="form-control" name="NumPhone" value="<?php echo $user->Phone_nbr_clt; ?>" required>

                <label for="name">Habita :</label>
                <input class="form-control" type="text" name="Habita" value="<?php echo $user->Habita; ?>" required>

                <label for="prenom">Adresse Email :</label>
                <input type="text" class="form-control" name="EmailAdresse" value="<?php echo $user->Email_clt; ?>" required>

                <label for="name">Mot de passe :</label>
                <input class="form-control" type="text" name="PassWord" value="<?php echo $user->PassWords_clt; ?>" required>


                <button class="btn btn-primary" type="submit">Enregistrer les modifications</button>
            </form>
            <?php
        } else {
            echo "Utilisateur non trouvé.";
        }
    } else {
        echo "ID de l'utilisateur non fourni.";
    }
    
    ?>
            
        </div>
        <div>
            <a href="http://localhost/Today_list/Accueil.php" class="w3-bar-item w3-button w3-teal"></span>Retour</a>
            <a href="http://localhost/Today_list/Principale_page.php" class="w3-bar-item w3-button w3-indigo">Page Publique</a>
            <a href="#" class="w3-bar-item w3-button w3-deep-purple">Powered by Brunel</a>
        </div>
       
        </nav>
      
    <!-- End Right Column -->
    </div>
    
  <!-- End Grid -->
  </div>
  
<!-- End Page Container -->
</div>
<script src="http://localhost/Today_list/js/all.min.js"></script>
<script>

    
var openInbox = document.getElementById("myBtn");
openInbox.click();

function w3_open() {
  document.getElementById("mySidebar").style.display = "block";
  document.getElementById("myOverlay").style.display = "block";
}

function w3_close() {
  document.getElementById("mySidebar").style.display = "none";
  document.getElementById("myOverlay").style.display = "none";
}

function myFonct(id) {
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

openMail("Borge")
function openMail(personName) {
  var i;
  var x = document.getElementsByClassName("person");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  x = document.getElementsByClassName("test");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" w3-light-grey", "");
  }
  document.getElementById(personName).style.display = "block";
  event.currentTarget.className += " w3-light-grey";
}



// Accordion
function myFunction(id) {
  var x = document.getElementById(id);
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
    x.previousElementSibling.className += " w3-theme-d1";
  } else { 
    x.className = x.className.replace("w3-show", "");
    x.previousElementSibling.className = 
    x.previousElementSibling.className.replace(" w3-theme-d1", "");
  }
}


</script>

</body>
</html> 
