<?php
try{
        $db = new PDO('mysql:host=localhost;dbname=projet_final_php;charset=utf8','root' , '');

        //TODO: à virer avant de mettre en ligne !
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e){
        die('problème avec la base de donnée : '. $e->getMessage());
    }
?>