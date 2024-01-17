<?php
session_start();

include 'Class_accueil.php';

$AuthentHandler = new Accueil();

$html0='';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailToSearch = $_POST['MailName'];
    $password = $_POST['PassWord'];

    // Vérifier si l'e-mail existe
    $existeEmail = $AuthentHandler->ChercherParPassWord($emailToSearch, $password);

    if ($existeEmail) {
        $idClt = $AuthentHandler->ChercherId($emailToSearch, $password);
        $_SESSION['mety'] = 1;
        header("Location: http://localhost/Today_list/PageUserGestion.php/?IdUser=" . $idClt);
        exit();
    } else {
        $_SESSION['mety'] = 0;
        $html0 .= "<hr>";
        $html0 .= '<a class="btn btn-danger" href="http://localhost/Today_list/Accueil.php">Mots de passe incorrect</a>';
    }
}

$ListeClients ='';

// Si le formulaire n'est pas soumis, affichez tous les cours
$ListeClients  = $AuthentHandler->getAllClients();

    // Initialisation de variables pour stocker les sections actuelles et les noms de cours
    $currentSection = null;
    $courseNames = [];

    $html = '';

    // Parcourir les résultats
    foreach ($ListeClients as $client) {
       
        $nom = $client->Name_clt;
        $prenom = $client->Prenom_clt;
        $genre = $client->Genre;
        $Habita = $client->Habita;
        $EmailName=$client->Email_clt;
          // Vérifier si la clé "image" existe dans l'objet $client
        $imagePath = property_exists($client, 'image') ? 'http://localhost/Today_list/'.$client->image : 'http://localhost/Today_list/Sary/user_100px.png';

        // Vérifier si la section courante est différente de la section précédente
        if ($nom !== $currentSection) {

            $html .= "<div class=\"w3-container w3-black\" style=\"background: linear-gradient(to top right, #6600cc 0%, #66ffff 100%);\">";
             // Affichage de l'image avec la classe w3-round et w3-margin-right
            $html .= "<img class=\"w3-round-xlarge w3-margin-right\" src=\"$imagePath\" style=\"width:50px; height:50px;\">";
            $html .= "<h2 class=\"w3-myfont\">$nom $prenom</h2>"; // Ouvrir une nouvelle section
            $html .= "</div>";

            // Mettre à jour la section actuelle
            $currentSection = $nom;
        }

        // Afficher les détails de l'étudiant dans la section actuelle
        $html .= "<div class=\"w3-container\">";
        $html .= "<h5> <b> Genre:</b> $genre <b> Lieu:</b> $Habita <b> Email:</b> $EmailName </h5>";
        $html .= "</div>";

        // Stocker les noms de cours pour utilisation future si nécessaire
        $courseNames[] = $nom;
    }

    // Fermer la dernière section
    $html .= "</div>";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="http://localhost/Today_list/Razma/w3.css">
    <link rel="stylesheet" href="http://localhost/Today_list/css/bootstrap.min.css">
</head>
<style>
body,h1,h5 {font-family: "Raleway", sans-serif}
body, html {height: 100%}
.bgimg {
  background-image: url('Sary/nothing-phone.jpg');
  min-height: 100%;
  background-position: center;
  background-size: cover;
}
.w3-myfont {
  font-family: "Comic Sans MS", cursive, sans-serif;
}
.table-inverse
{
    color: cadetblue;
}

.thead-inverse
{
    color: chartreuse;
    background: linear-gradient(to right, #6600cc 0%, #66ffff 100%);
}
.BtnClient
{
    position : absolute;
    top : 35pc;
    left: 54pc;
    color: white;
    width: 500px;
    text-align: center;
    border-radius: 10px;
    background-color: rgba(26, 64, 122, 0.4);

    animation-name: SecAnime;
    animation-duration: 4s;
    
}
@keyframes SecAnime {
    0%   {background-color: rgba(255, 54, 54, 0.3);}
    25%  {width: 300px;}
    50%  {color: rgba(255, 238, 0, 0.9);}
    100% {height: 200;}
}
</style>
<body>
<script>
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

    <div class="bgimg w3-display-container w3-text-white">
    <div class="w3-display-middle w3-jumbo">
        <img  onmouseover="rotateYDIV()" id="rotate3D" src="Sary/work_authorisation_100px.png" style="width:150px; margin-left:15px; transform: rotateY(360deg);" alt="">
        <h6 style="color:Brown; font-size:20px;"><strong>Gestion des tâches</strong></h6>
        <?php
            echo $html0;
        ?>
    </div>
    
    <div class="w3-display-topleft w3-container w3-large">
        <br>
        <p><button onclick="document.getElementById('menu').style.display='block'" class="w3-button w3-round-xlarge">Liste des utilisateurs</button></p>
        <p><button onclick="document.getElementById('contact').style.display='block'" class="w3-button w3-round-xlarge w3-black">Authentification</button></p>
        <a class="w3-button w3-round-xlarge" href="http://localhost/Today_list/Principale_page.php">Page publique</a>
    </div>
    <div class="w3-display-bottomleft w3-container">
        <p class="w3-xlarge">Dimanche à Vendredi | 8h à 18h</p>
        <p class="w3-large">RN7 UAZ, Sambaina</p>
    </div>
    </div>

    <div class="BtnClient">
        <h6>Pour faire les tâches, N'hesité pas de s'incrire</h6>
        <a class="btn btn-primary"  href="http://localhost/Today_list/CreateUser.php/?">S'inscrire</a>
    </div>

    <!-- Menu Modal -->
    <div id="menu" class="w3-modal">
    <div class="w3-modal-content w3-animate-zoom w3-round-xlarge" style="width: 750px; text-align:center">
        <div class="w3-container w3-black w3-display-container" style="background: linear-gradient(to bottom right, #000000 0%, #66ccff 100%);">
            <span onclick="document.getElementById('menu').style.display='none'"  class="w3-button w3-display-topright w3-large">x</span>
            <h1>Liste des utilisateurs dans la base</h1><hr>
        </div>
        <div>
            <?php
                echo $html;
            ?>
        </div>
    </div>
    </div>

    <!-- Contact Modal -->
    <div id="contact" class="w3-modal">
    <div class="w3-modal-content w3-animate-right w3-round-xlarge" style="width: 600px;">
        <div class="w3-container w3-black" style="background: linear-gradient(to right, #000000 0%, #66ccff 100%);">
        <span onclick="document.getElementById('contact').style.display='none'" class="w3-button w3-display-topright w3-large">x</span>
        <h2>Authentification</h2>
        </div>
        <div class="w3-container">
        <p>Veuillez inserer votre information s'il vous plaît: <hr> </p>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <p><input class="w3-input w3-padding-16 " type="text" placeholder="Votre adresse email .." required autocomplete="off" name="MailName"></p>
            <p><input class="w3-input w3-padding-16 " type="password" placeholder="Votre mots de passe .." required name="PassWord"></p>
            <p><button class="btn btn-primary " type="submit">Se connecter</button></p>
        </form>
        </div>
    </div>
    </div>
</body>
</html>