<?php

/// Envoi de la réponse au Client
function isValidUser($linkpdo, $user, $password) {

    $reqObtenirInfo = $linkpdo->prepare('SELECT login, mdp FROM user_auth_v1 WHERE login = :login');

    if ($reqObtenirInfo == false) {
        echo "Erreur dans la préparation de la requête d'affichage.";
    } else {

        $reqObtenirInfo->bindParam(':login', $user, PDO::PARAM_STR); 

        $reqObtenirInfo->execute();

        if ($reqObtenirInfo == false) {
            echo "Erreur dans l'execution de la requête d'affichage.";
        } else {
            // On récupère toutes les phrases
            $data = $reqObtenirInfo->fetch();

            //Documentation : password_verify — Verifies that a password matches a hash 
           if( $user == $data['login'] && $password == $data['mdp']) {
                return true;
           } else {
            return false;
           }
        }
    }
}

/// Envoi de la réponse au Client
function deliver_response($status_code, $status_message, $data=null) {

    /// Paramétrage de l'entête HTTP
    http_response_code($status_code); //Utilise un message standardisé en fonction du code HTTP

    // Ajout de l'entête Access-Control-Allow-Origin pour autoriser toutes les origines
    header("Access-Control-Allow-Origin: *");

    //header("HTTP/1.1 $status_code $status_message"); //Permet de personnaliser le message associé au code HTTP
    header("Content-Type:application/json; charset=utf-8");//Indique au client le format de la réponse

    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if($json_response===false)
    die('json encode ERROR : '.json_last_error_msg());

    /// Affichage de la réponse (Retourné au client)
    echo $json_response;
}
?>