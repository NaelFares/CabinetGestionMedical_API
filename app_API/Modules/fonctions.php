<?php

// Fonction pour effectuer une demande de validation à l'API d'authentification
function demande_validation() {
    $res = false;
    $url = 'http://localhost/CabinetGestionMedical_API/auth'; // URL de l'endpoint de validation
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    //Ajout du token dans l'entête
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . get_bearer_token()
    ));
    $response = curl_exec($ch);
    // Vérifier si la réponse est un JSON valide
    $json_output = json_decode($response);
    curl_close($ch);

    // Traitement de la réponse
    // Si le jeton est valide -> code = 200
    if ($json_output->status_code == 200) {
        $res = true;
    } else {
        // Logique si la validation échoue
        $res = false;
        //Renvoyer l'erreur générée dans la requête à l'URL à partir du json reçu 
        deliver_response($json_output->status_code, $json_output->status_message);
    }

    return $res;
}

//Utilisé dans get_bearer_token
function get_authorization_header(){
	$headers = null;

	if (isset($_SERVER['Authorization'])) {
		$headers = trim($_SERVER["Authorization"]);
	} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
		$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
	} else if (function_exists('apache_request_headers')) {
		$requestHeaders = apache_request_headers();
		// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
		$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
		//print_r($requestHeaders);
		if (isset($requestHeaders['Authorization'])) {
			$headers = trim($requestHeaders['Authorization']);
		}
	}

	return $headers;
}

function get_bearer_token() {
    $headers = get_authorization_header();
    
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            if($matches[1]=='null') //$matches[1] est de type string et peut contenir 'null'
                return null;
            else
                return $matches[1];
        }
    }
    return null;
}

?>