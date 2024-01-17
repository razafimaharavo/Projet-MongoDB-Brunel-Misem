<?php
session_start();

// Initialisation des variables de session s'il s'agit de la première visite de l'utilisateur
if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = 1;
    $_SESSION['data'] = array();
}

// Tableau contenant les étapes et les messages à afficher à chaque étape
$steps = array(
    1 => "Entrer votre Nom",
    2 => "Entrer votre Prénom",
    3 => "Selectionner votre genre",
    4 => "Entrer votre Numéro de téléphone",
    5 => "Entrer le Lieu où vous habitez",
    6 => "Entrer votre Email",
    7 => "Veuillez entrer votre mot de passe"
);

// Options pour le champ de sélection du genre
$genreOptions = array(
    'Homme' => 'Homme',
    'Femme' => 'Femme'
);

// Connexion à la base de données MongoDB
$mongoClient = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Traitement des données du formulaire à chaque soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $step = $_SESSION['step'];

    // Récupération de la valeur saisie par l'utilisateur
    $userInput = $_POST['user_input'];

    // Ajout de la valeur saisie dans le tableau de données
    $_SESSION['data'][$step] = $userInput;

    // Passage à l'étape suivante
    $_SESSION['step']++;

    // Redirection vers la page actuelle pour afficher l'étape suivante ou insérer dans la base de données si toutes les étapes sont terminées
    if ($_SESSION['step'] <= count($steps)) {
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        // Insérer les données dans la collection "user" de la base de données "todoList"
        $bulk = new MongoDB\Driver\BulkWrite;
        $name = $_SESSION['data'][1];
        $prenom = $_SESSION['data'][2];
        $genre = $_SESSION['data'][3];
        $phone_nbr = $_SESSION['data'][4];
        $habita = $_SESSION['data'][5];
        $email = $_SESSION['data'][6];
        $passWords = $_SESSION['data'][7];
        $date_insert = new MongoDB\BSON\UTCDateTime(strtotime("now") * 1000);

        $document = [
            'Name_clt' => $name,
            'Prenom_clt' => $prenom,
            'Genre' => $genre,
            'Phone_nbr_clt' => $phone_nbr,
            'Habita' => $habita,
            'Email_clt' => $email,
            'PassWords_clt' => $passWords,
            'Date_insert' => $date_insert
        ];

        $bulk->insert($document);
        $mongoClient->executeBulkWrite('todolist.user', $bulk);

        // Réinitialisation des variables de session pour une nouvelle saisie
        $_SESSION['step'] = 1;
        $_SESSION['data'] = array();

        // Message de succès
        $successMessage = "Enregistrement réussi !";
    }
}
?>

<!-- Le reste du code reste inchangé -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer utilisateur</title>
    <link rel="stylesheet" href="http://localhost/Today_list/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/Today_list/StyleCreate.css">
    <style>
        .form-group
        {
            width : 30pc;
        }
        .container
        {
            position: fixed;
            top: 15pc;
            left : 28pc;
        }
        .btn
        {
            position: relative;
            left: 10pc;
            bottom: 10px;
            width: 10pc;
        }
        .btn:hover {
            transform: scale(1.1);
        }
        body {
            background-image: url('http://localhost/Today_list/Sary/MainPhone.webp'); /* Mettez ici le chemin vers votre première image */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            transition: background-image 2s ease; /* Ajoutez une transition de 1 seconde */
        }
        
    </style>
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
        // Tableau des chemins d'accès vers les images de fond
        var backgrounds = ['http://localhost/Today_list/Sary/PhotoLivre.jpg', 'http://localhost/Today_list/Sary/restaurant-2-domaine-de-nazere.jpg', 'http://localhost/Today_list/Sary/MainPhone.webp', 'http://localhost/Today_list/Sary/onepage_restaurant.jpg']; // Ajoutez ici les chemins vers vos images

        // Fonction pour changer le fond d'écran toutes les 5 secondes
        function changeBackground() {
            var randomIndex = Math.floor(Math.random() * backgrounds.length);
            var randomBackground = backgrounds[randomIndex];
            document.body.style.backgroundImage = 'url(' + randomBackground + ')';
        }

        // Appeler la fonction changeBackground toutes les 5 secondes
        setInterval(changeBackground, 5000);
    </script>
</head>

<body>
    <img  onmouseover="rotateYDIV()" id="rotate3D" src="http://localhost/Today_list/Sary/Add Male User.png" style="width:90px; margin-top:55px; margin-left:20px; transform: rotateY(360deg);" alt="">
    <?php
    if ($_SESSION['step'] <= count($steps)) {
        $step = $_SESSION['step'];
        //echo "<p>".$steps[$step]."</p>";
        ?>
        <div class="container">
       
    <form method="post" action="">
        <div class="form-group">
            <label for="UserText">
                <div class="rounded p-1 mb-1 bg-primary text-white" style="--bs-bg-opacity: .2;">
                    <?php echo "<p>".$steps[$step]."</p>" ?>
                </div>
            </label>

            <?php if ($step == 3): // Si c'est l'étape "Selectionner votre genre" ?>
                <select name="user_input" class="form-control" required>
                    <?php foreach ($genreOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>

                <?php elseif ($step == 6): // Si c'est l'étape "Entrer votre Email" ?>
                        <input type="email" name="user_input" placeholder="name@gmail.com"  class="form-control" autocomplete="off" required>

            <?php else: // Sinon, afficher un champ de texte normal ?>
                <input type="text" name="user_input" class="form-control" autocomplete="off" required>
            <?php endif; ?>
        </div>
        <br> 
        <input type="submit" class="btn btn-warning" value="Valider">
    </form>
</div>

    <?php
    } else {
        // Toutes les étapes sont terminées, affichage des données saisies
        echo "<h2>Données saisies :</h2>";
        foreach ($_SESSION['data'] as $step => $value) {
            echo "<p>".$steps[$step].": ".$value."</p>";
        }

        // Afficher le message de succès
        if (isset($successMessage)) {
            echo "<p>$successMessage</p>";
        }

        $_SESSION['step'] = 1;
        $_SESSION['data'] = array();
    }
    ?>
</body>

</html>
