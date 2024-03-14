<?php
   $server = "mysql-authentificationapi.alwaysdata.net";
   $db = "authentificationapi_bd_auth";
   $login = "350793";
   $mdp = '$iutinfo';

   // Connexion au serveur MySQL
   try {
       $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
   } catch (Exception $e) {
       die('Erreur : ' . $e->getMessage());
   }
?>