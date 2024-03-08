<?php
   $server = "localhost";
   $db = "gestion_cabinet_medical";
   $login = "root";

   // Connexion au serveur MySQL
   try {
       $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login);
   } catch (Exception $e) {
       die('Erreur : ' . $e->getMessage());
   }


   //
   /* 
   $server = "localhost";
   $db = "id21566564_gestion_cabinet_medical";
   $login = "id21566564_frn";
   $password = "MedicoGest@31";

   // Connexion au serveur MySQL
   try {
       $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $password);
   } catch (Exception $e) {
       die('Erreur : ' . $e->getMessage());
   }
   */

?>