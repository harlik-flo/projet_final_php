<?php
session_start();

$_SESSION["user"] = $user;
// $_SESSION["user"] existe on la suprime
if (isset($user)) {
    unset($user);
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
<link rel="stylesheet" href="css/styles.css">    <title>Déconnexion - Wikifruit</title>
</head>
<body>
    <!-- Menu HTML/Bootstrap qui sera en haut de chaque page du site -->
    <?php

include "include/menu.php";

?>
    <div class="container-fluid">

        <div class="row">

            <div class="col-12 col-md-8 offset-md-2 py-5">
                <h1 class="pb-4 text-center">Déconnexion</h1>
                <p class="alert alert-success text-center">Vous avez bien été déconnecté !</p>
            </div>

        </div>

    </div>

    <!-- Fichiers JS communs à toutes les pages du site -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script></body>
</html>