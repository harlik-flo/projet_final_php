<?php

echo '<pre>';
print_r($_POST);
echo '</pre>';

// include le CAPTCHA
require 'include/recaptchaValid.php';

if(
    isset($_POST['email']) &&
    isset($_POST['password']) &&
    isset($_POST['confirm-password']) &&
    isset($_POST['pseudonym']) &&
    isset($_POST['g-recaptcha-response'])
){

  // Bloc des vérifs

    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $errors[] = 'Email invalide !';
    }

    if(!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/u', $_POST['password'])){
        $errors[] = 'Votre mot de passe doit contenir au moins 1 min, 1 maj, un chiffre, un caractère spécial et doit avoir au minimum 8 caractères.';
    }

    if($_POST['password'] != $_POST['confirm-password']){
        $errors[] = 'La confirmation ne correspond pas au mot de passe !';
    }

    if(!preg_match('/^[\p{L}\w]{2,25}$/iu', $_POST['pseudonym'])){
        $errors[] = 'Le pseudonym doit contenir uniquement entre 1 et 50 caractères, composés de lettre min/maj, de chiffres, de caractères accentués et de l\'underscore !';
    }

    if(!recaptchaValid($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']) ){
        $errors[] = 'CAPTCHA invalide !';
    }

    /// Si pas d'erreurs, traitement du formulaire
    if(!isset($errors)){

        // Connexion à la BDD
        require "include/db.php";

        // Requête SQL de création du fruit avec les données du formulaire
        // Cette requête est une requête préparée pour éviter de concaténer les variables directement dans la requête, sinon injection SQL
        $insertNewFruit = $db->prepare("INSERT INTO users(email, password, pseudonym, register_date) VALUES(?, ?, ?, NOW())");

        // Execution de la requête préparée en envoyant la valeur de $_POST['email'] au premier "?", $_POST['password'] au deuxième "?" avec le hashage pour le chiffrage, $_POST['pseudonym'] en 3ème et NOW() pour la date du moment.
        $querySuccess = $insertNewFruit->execute([
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_BCRYPT),
            $_POST['pseudonym'],
        ]);

        // Fermeture de la requête
        $insertNewFruit->closeCursor();

        // On vérifie si la requête a fonctionné ($querySuccess contient true si la requête a réussi, sinon false)
        if($querySuccess){

            // Message de succès
            $successMsg = 'Votre inscription a bien été enregistrée !';
        } else {

            $errors[] = 'Problème, veuillez ré-essayer';
        }


    }

}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Éléments du head HTML communs à toutes les pages du site -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="css/styles.css">    <title>Inscription - Wikifruit</title>
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>

<!-- Inclure le menu  -->
<?php

include "include/menu.php";

?>

<div class="container-fluid">

        <div class="row">

            <div class="col-12 col-md-8 offset-md-2 py-5">
                <!-- <p class="text-center"><a class="text-decoration-none" href="register2.php">Voir la version "bonus" avancée de la page d'inscription</a></p> -->
                <h1 class="pb-4 text-center">Créer un compte sur Wikifruit</h1>
                <div class="col-12 col-md-6 mx-auto">
<?php

    // Affichage des erreurs s'il y en a
    if(isset($errors)){

        foreach($errors as $error){
            echo '<p class="alert alert-danger mb-3;">' . htmlspecialchars($error) . '</p>';
        }

    }

    // Affichage de la variable de succès, sinon affichage du formulaire

    if(isset($successMsg)){

        echo '<p class="alert alert-success mb-3;">' . htmlspecialchars($successMsg) . '</p>';

    }else {

        ?>
    
    


                    
                        <form action="register.php" method="POST">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input id="email" type="text" name="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input id="password" type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirmation mot de passe <span class="text-danger">*</span></label>
                                <input id="confirm-password" type="password" name="confirm-password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="pseudonym" class="form-label">Pseudonyme <span class="text-danger">*</span></label>
                                <input id="pseudonym" type="text" name="pseudonym" class="form-control">
                            </div>
                            <div class="mb-3">
                                <p class="mb-2">Captcha <span class="text-danger">*</span></p>
                                <div class="g-recaptcha" data-sitekey="6LfkYncfAAAAAK4-KxbOrQ1hjb7iwlvuDb5P7oXM"></div>
                            </div>
                            <div>
                                <input value="Créer mon compte" type="submit" class="btn btn-success col-12">
                            </div>

                            <p class="text-danger mt-4">* Champs obligatoires</p>

                        </form>
                                        </div>

            </div>

        </div>

    </div>
    
        <?php
    }


?>
    

    <!-- Fichiers JS communs à toutes les pages du site -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script></body>
</html>