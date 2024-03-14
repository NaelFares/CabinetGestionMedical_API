<?php
require("jwt_utils.php");
require('connexion_db.php');
require('authapi_functions.php');

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method){

    //Création du jeton
    case "POST" :

        // get posted data
        $data = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(isValidUser($linkpdo, $data['login'], $data['mdp'])){
            $login = $data['login'];
            $headers = array('alg'=>'HS256','typ'=>'JWT');
            $playload = array('login'=>$login, 'exp'=>(time() + 60));

            $jwt = generate_jwt($headers, $playload);

            deliver_response("200", "Succés, création du jeton",  $jwt);
        } else {
            deliver_response("401", "Identifiant ou mot de passe invalide");
        }
    
    break;
}
?>