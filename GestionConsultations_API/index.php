<?php 




    //////////Changer l'ecriture de deliver response 
    /////////// dans Modules/connexion_db rajouter et changer le( $login = "root", $mdp = "qvk1hXX_THrRfD*Q"; //Protège le serveur MySQL ) ==> pour valentane




    require('../Modules/connexion_db.php');
    require('functions_consultations.php');

    $http_method = $_SERVER['REQUEST_METHOD'];

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







            break;

        case 'DEL' :
            break;

        case default : 
            break;
        */
    }
?>