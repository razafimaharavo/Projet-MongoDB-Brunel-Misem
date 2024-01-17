<?php
require 'GestionUser.php';

// Récupérer les données du formulaire
$userId = isset($_POST['userIdEdit']) ? $_POST['userIdEdit'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$prenom = isset($_POST['prenom']) ? $_POST['prenom'] : null;
$Genre = isset($_POST['Genre']) ? $_POST['Genre'] : null;
$NumPhone = isset($_POST['NumPhone']) ? $_POST['NumPhone'] : null;
$Habita = isset($_POST['Habita']) ? $_POST['Habita'] : null;
$EmailAdresse = isset($_POST['EmailAdresse']) ? $_POST['EmailAdresse'] : null;
$PassWord = isset($_POST['PassWord']) ? $_POST['PassWord'] : null;

// Vérifier si l'ID de l'utilisateur est fourni
if ($userId) {
    // Mettre à jour les données de l'utilisateur
    $result = mettreAJourUtilisateur($userId, $name, $prenom, $Genre, $NumPhone, $Habita, $EmailAdresse, $PassWord);
    
    if ($result) {
        header('location:http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$userId);
        exit();
    } else {
        echo "Erreur lors de la mise à jour de l'utilisateur car vous n'avez rien changer sur les données de ".$name ." ".$prenom."<hr> Vous devez faire une modification si vous voulez sauvegarder <hr>";
        echo '<a href="http://localhost/TODAY_LIST/PageUserGestion.php/?IdUser='.$userId.'">Retour</a>';
    }
} else {
    echo "ID de l'utilisateur non fourni.";
}
?>
