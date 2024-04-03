<?php

require('../Modules/connexion_db.php');
require('functions_medecin.php');

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method){

    case "GET" :

        //Récupération des données dans l’URL si on veut avec l'id
        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Appel de la fonction de lecture d'une phrase
            $matchingData=getMedecinById($linkpdo, $id);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]);

        } else {
            //Appel de la fonction de lecture des phrases
            $matchingData=getAllMedecins($linkpdo);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]);
        }
        
    break;

    case "POST" :

        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
        //Traitement des données
        
        if(isset($data['civilite']) && isset($data['nom']) && isset($data['prenom'])) {
            // Appel de la fonction de création d’un medecin
            $matchingData = createMedecin($linkpdo, $data['civilite'], $data['nom'], $data['prenom']);
            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]); // Les données de réponse sont stockées dans $matchingData, le troisième paramètre est null car il n'y a pas de données à renvoyer dans ce cas.
        } else {
            // Gestion de l'erreur si des données requises sont manquantes
            deliver_response(400, "Des données requises sont manquantes", null);
        }
        
    break;

    case "PATCH" :

        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Récupération des données dans le corps
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
            //Traitement des données
            
            //Appel de la fonction de modification partielle d’une phrase
            $matchingData=patchMedecin($linkpdo, $id , null, $data['nom'], null);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"]);
        }

    break;

    case "DELETE" :

        //Récupération des données dans l’URL si on veut avec l'id
        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Appel de la fonction de lecture des phrases
            $matchingData=deleteMedecin($linkpdo, $id);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"]);
        }
        
    break;

    case "OPTIONS" :

        // Ajoutez les en-têtes CORS pour indiquer les méthodes HTTP autorisées
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        // Ajoutez les en-têtes CORS pour indiquer les en-têtes autorisés
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        deliver_response("204", "Autorisation de la méthode option et des requêtes CORS");
        
    break;

}
?>