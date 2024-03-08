<?php
   $server = "localhost";
   $db = "r401_api";
   $login = "root";

   // Connexion au serveur MySQL
   try {
       $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login);
   } catch (Exception $e) {
       die('Erreur : ' . $e->getMessage());
   }
?>