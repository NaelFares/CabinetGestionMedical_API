<?php

<<<<<<< HEAD
require('../Modules/connexion_db.php');
require('functions_consultations.php');
=======



    //////////Changer l'ecriture de deliver response 
    /////////// dans Modules/connexion_db rajouter et changer le( $login = "root", $mdp = "qvk1hXX_THrRfD*Q"; //Protège le serveur MySQL ) ==> pour valentane




    require('../Modules/connexion_db.php');
    require('functions_consultations.php');
>>>>>>> 17eea6551f6e9a1bf43eaef66574c2977407281c

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

<<<<<<< HEAD
switch ($http_method){
=======
    switch ($http_method){
        case 'GET' :
            if (isset($_GET['id'])){
                $id=htmlspecialchars($_GET['id']);

                $resultat = getConsultation($linkpdo, $id);
                deliver_response('200', 'Affichage de la consultations par son id', $resultat);
            } else {
                $resultat = getAllConsultations($linkpdo);
                deliver_response('200', 'Affichage de toutes les consultations', $resultat);
            }
            break;
        
        case 'POST' :
            //Récupération des données dans le corps (bodPostman)
            $posteData = file_get_contents('php://input');
            
            //Reçoit du json et renvoie une adpatation exploitable en pho, le paramètre true met les données dans un tableau.
            $data = json_decode($posteData, true); 

            if (isset($data['date_consultation']) && isset($data['heure_debut']) && isset($data['duree'])) {
                $resultat = addConsultation($linkpdo, $data['date_consultation'], $data['heure_debut'], $data['duree']);
                
                // Le troisième paramètre est null car on ne recupère aucune données
                deliver_response('200', 'Création de la consultation', null);
            } else {
                deliver_response('400', 'Des données requises sont manquantes', null);
            }
            break;
        /*
        case 'PATCH' : 
            if (isset($_GET['id]) == true){

            }


>>>>>>> 17eea6551f6e9a1bf43eaef66574c2977407281c

    case "GET" :

        //Récupération des données dans l’URL si on veut avec l'id
        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Appel de la fonction d'affichage d'une consultation
            $matchingData=getConsultationById($linkpdo, $id);

<<<<<<< HEAD
            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]);

        } else {
            //Appel de la fonction de lecture de toutes les consultations
            $matchingData=getAllConsultations($linkpdo);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], $matchingData["data"]);
        }
        
    break;

    case "POST" :
=======

            break;
>>>>>>> 17eea6551f6e9a1bf43eaef66574c2977407281c

        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
        //Traitement des données
        
        if(isset($data['date_consultation']) && isset($data['heure_debut']) && isset($data['duree'])) {
            // Appel de la fonction de création d’une consultation
            $matchingData = createConsultation($linkpdo, $data['date_consultation'], $data['heure_debut'], $data['duree']);
            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"], null); // Les données de réponse sont stockées dans $matchingData, le troisième paramètre est null car il n'y a pas de données à renvoyer dans ce cas.
        } else {
            // Gestion de l'erreur si des données requises sont manquantes
            deliver_response(400, "Des données requises sont manquantes", null);
        }
        
    break;

<<<<<<< HEAD
    case "PATCH" :

        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Récupération des données dans le corps
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
            //Traitement des données
            
            //Appel de la fonction de modification partielle d’une consultation
            $matchingData=patchConsultation($linkpdo, $id , null, null, null);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"]);
        }

    break;

    case "DELETE" :

        //Récupération des données dans l’URL si on veut avec l'id
        if(isset($_GET['id']) == true) {
            $id=htmlspecialchars($_GET['id']);
            //Traitement des données

            //Appel de la fonction de suppression d'une consultation
            $matchingData=deleteConsultation($linkpdo, $id);

            deliver_response($matchingData["statusCode"], $matchingData["statusMessage"]);
        }
        
    break;

}
=======
        case default : 
            break;
        */
    }
>>>>>>> 17eea6551f6e9a1bf43eaef66574c2977407281c
?>