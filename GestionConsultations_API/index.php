<?php 

    require('Modules/connexion_db.php');
    require('functions_consultations');
    require('auth_API/jwt_utils.php');

    $http_method = $_SERVER['REQUEST_METHOD'];

    switch ($http_method){
        case 'GET' :
            if (is_null($idConsultation)){
                $resultat = getAllConsultations($linkpdo);
                deliver_response('200', 'Affichage de toutes les consultations', $resultat);
            }




            break;
        
        case 'POST' :
            break;

        case 'PATCH' : 
            break;

        case 'DEL' :
            break;

        case default : 
            break;

    }