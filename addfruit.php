<?php

session_start();

$countriesList = [
    'fr' => 'france',
    'de' => 'allemagne',
    'es' => 'espagne',
    'jp'=> 'japon',
    'mr'=> 'maroc',
];
$allowedMIMETypes = [
    'jpg' => 'image/jpeg',
    'png' =>'image/png',
];

$maxFileSize = 5242880;
$description = "";
print_r($_POST);
print_r($_FILES);

// Appel des variables
if(
    isset( $_POST["name"] ) &&
    isset( $_FILES["picture"] ) &&
    isset( $_POST["description"] )
){

    if( !preg_match(
        "/^[\p{L}\w]{1,50}$/iu",
        $_POST["name"] )
    ){

        $errors[] = "Le nom doit contenir entre 1 et 50 caractères !";

    }

    if(!empty($_POST['description'])){

        if( !preg_match("/^.{5,20000}$/", $_POST["description"] )){

            $errors[] = "La description doit contenir entre 5 et 20000 caractères !";

        }
    }

    if(!isset($_POST['origin']) || !array_key_exists($_POST['origin'], $countriesList)){
        $errors [] = "Le pays est invalide";
    }

    $fileErrorCode = $_FILES['picture']['error'];

    $fileMIMEType = "";

    if ($fileErrorCode == 1 || $fileErrorCode == 2 || $_FILES['picture']['size'] > $maxFileSize) {
        $errors []= "fichiez trop volumineux !";

    }elseif ($fileErrorCode == 3) {
        $errors []= "Téléchargement incomplet, erreur de connection !";

    }elseif ($fileErrorCode == 6 || $fileErrorCode == 7 || $fileErrorCode == 8) {
        $errors []= "problème serveur !";

    }elseif ($fileErrorCode == 0 ) {
        $fileMIMEType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['picture']['tmp_name']);

        if (!in_array($fileMIMEType, $allowedMIMETypes)) {
            $errors [] = 'Seuls les fichiers jpg et png sont autorisés !';
        }

    }


    if (!isset($errors)) {
        $ext = array_search($fileMIMEType, $allowedMIMETypes);


        do {
            $newFileName = md5 ( random_bytes(50) ). '.' . $ext;
        } while (file_exists('images/uploads/' . $newFileName));{

        move_uploaded_file( $_FILES['picture']['tmp_name'],'images/uploads/'.$newFileName.'');

        require "include/db.php";
        $userId = $_SESSION['user'];
        $insertNewFruit = $db->prepare("INSERT INTO fruits ( name, origin, description, picture_name, user_id ) VALUES (?,?,?,?,?) ");



        $querySuccess = $insertNewFruit -> execute(array(
            $_POST['name'],
            $_POST['origin'],
            $_POST['description'],
            $newFileName,
            $_SESSION['user']['id'],
        ));




    $insertNewFruit->closeCursor();

        $success = "Le fruit a bien été créé !";

        }
    }

}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Accueil de Wikifruit</title>
</head>
<body>

<!-- Menu -->
<?php include "include/menu_user.php"; ?>

<div class="container-fluid">

    <div class="row">

        <div class="col-12 col-md-8 offset-md-2 py-5">

            <h1 class="pb-4 text-center">Ajouter un fruit</h1>

                <div class="col-12 col-md-6 offset-md-3">

<?php

    // Affichage des erreurs ou du message de succès
    if( isset($errors) ){

        foreach($errors as $error){

            echo "<p class='alert alert-danger mb-3 mt-4'>" . $error . "</p>";

        }

    }

    if( isset($success) ){

        echo "<p class='alert alert-success mb-3 mt-4'> " . $success . " </p>";

    } else {

?>

                    <form action="addfruit.php" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">

                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input placeholder="Banane" id="name" type="text" name="name" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label for="origin" class="form-label">Pays d'origine <span class="text-danger">*</span></label>
                            <select id="origin" name="origin" class="form-select">

                                <option selected disabled>Sélectionner un pays</option>
                                <?php

                                foreach($countriesList as $key => $country){
                                    echo '<option value="' . $key . '">' . ucfirst($country) . '</option>';
                                }

                                ?>

                            </select>

                        </div>

                        <input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxFileSize?>">

                        <div class="mb-3">

                            <label for="picture" class="form-label">Photo</label>
                            <input type="file" class="form-control" id="picture" name="picture" accept="image/png, image/jpeg">

                        </div>

                        <div class="mb-3">

                            <label for="description" class="form-label">Description <span class="text-danger"></span></label>
                            <textarea class="form-control" name="description" id="description" cols="30" rows="10" placeholder="Description..."></textarea>

                        </div>

                        <div>

                            <input value="Créer le fruit" type="submit" class="btn btn-primary col-12">

                        </div>

                        <p class="text-danger mt-4">* Champs obligatoires</p>

                    </form>

                </div>
                <?php
                }
                ?>

        </div>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>