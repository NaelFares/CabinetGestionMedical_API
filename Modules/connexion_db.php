<?php

$server = "localhost";
$db = "cabinetmedicalapi_bd_app";
$login = "root";


// Connexion au serveur MySQL always data
try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


//Pour la version en ligne sur alwaysdata

/*
   $server = "mysql-cabinetmedicalapi.alwaysdata.net";
   $db = "cabinetmedicalapi_bd_app";
   $login = "350794";
   $mdp = '$iutinfo';

   // Connexion au serveur MySQL always data
   try {
       $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
   } catch (Exception $e) {
       die('Erreur : ' . $e->getMessage());
   }
   */
?>