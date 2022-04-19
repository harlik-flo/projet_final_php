<?php

session_start();

//Appel des variables
if(
    isset($_POST["email"]) &&
    isset($_POST["password"])
){

    if(
        !filter_var($_POST['email'],
        FILTER_VALIDATE_EMAIL)
    ){

        $errors[] = "Email invalide !";

    }

    if (
        !preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/u',$_POST['password'])
    ){

        $errors[] = "Le mot de passe doit comprendre au moins 8 caractères dont 1 lettre minuscule, 1 majuscule, un chiffre et un caractère spécial.";

    }

    if(!isset($errors)){

        require "include/db.php";

        $queryUserLog = $db->prepare( "SELECT * FROM users WHERE email = :email");
    
        $queryUserLog->execute(array(
            "email" => $_POST["email"],
        ));
    
        $user = $queryUserLog->fetch(PDO::FETCH_ASSOC);
    
        if( !empty($user) ){
    
            
            if( password_verify($_POST['password'], $user['password'])){
                $_SESSION["user"] = $user;
                
                $successMsg = 'Vous êtes bien connecté !';

            } else {
                $errors[] = 'Mauvais mot de passe !';
            }

    
        } else {
            $errors[] = 'Ce compte n\'existe pas !';
        }

    }

    echo "<pre>";
        print_r($_POST);
    echo "</pre>";

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
<link rel="stylesheet" href="css/styles.css">    <title>Connexion - Wikifruit</title>
</head>
<body>

<!-- Inclure le menu  -->
<?php
if(isset($successMsg)){

    include "include/menu_user.php";

}else {
    include "include/menu.php";
}

?>

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 col-md-8 offset-md-2 py-5">
                    <!-- <p class="text-center"><a class="text-decoration-none" href="login2.php">Voir la version "bonus" avancée de la page de connexion</a></p> -->
                    <h1 class="pb-4 text-center">Connexion</h1>
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


                    <div class="col-12 col-md-6 offset-md-3">

                            <form action="login.php" method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="text" name="email" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input id="password" type="password" name="password" class="form-control">
                                </div>
                                <div>
                                    <input value="Connexion" type="submit" class="btn btn-primary col-12">
                                </div>
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