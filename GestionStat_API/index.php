<?php

require('../Modules/connexion_db.php');
require('functions_stats.php');

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method){

    case "GET" :

        //Récupération des données dans l’URL si on veut avec l'id
        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Appel de la fonction de lecture des phrases
            $matchingData=getPatientsById($linkpdo, $idP);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]);

        } else {
            //Appel de la fonction de lecture des phrases
            $matchingData=getAllPatients($linkpdo);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]);
        }
        
    break;

}
?>