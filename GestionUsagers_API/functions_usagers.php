<?php

//Récupérer les usagers
function recupPatients($linkpdo) {

    $response = array(); // Initialisation du tableau de réponse

    $reqRecupPatient = $linkpdo->prepare('SELECT idP, civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM FROM patient');

        if ($reqRecupPatient == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqRecupPatient->execute();

        if ($reqRecupPatient == false) {
            echo "Erreur dans l'execution de la requête d'affichage.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqRecupPatient->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi";
            $response['data'] = $data; // Stockage des données dans le tableau de réponse
        }
    }

    return $response; // Retour du tableau de réponse
}

function readChuckFactId($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    $reqLireUnePhrase = $linkpdo->prepare('SELECT id, phrase, vote, date_ajout, date_modif, faute, signalement FROM chuckn_facts WHERE id = :id');

    if ($reqLireUnePhrase == false) {
        echo "Erreur dans la préparation de la requête d'affichage d'une seule phrase.";
    } else {

        $reqLireUnePhrase->bindParam(':id', $id, PDO::PARAM_STR); 

        $reqLireUnePhrase->execute();

        if ($reqLireUnePhrase == false) {
            echo "Erreur dans l'execution de la requête d'affichage d'une seule phrase par un Id.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqLireUnePhrase->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi";
            $response['data'] = $data; // Stockage des données dans le tableau de réponse
        }
    }

    return $response; // Retour du tableau de réponse
}

/// Ajouter une phrase
function createChuckFact($linkpdo, $phrase) {

    $response = array(); // Initialisation du tableau de réponse

    $reqCreateUnePhrase = $linkpdo->prepare('INSERT INTO chuckn_facts (phrase) VALUES (:phrase)');

    if ($reqCreateUnePhrase == false) {
        echo "Erreur dans la préparation de la requête de création d'une phrase.";
    } else {

        $reqCreateUnePhrase->bindParam(':phrase', $phrase, PDO::PARAM_STR); 

        $reqCreateUnePhrase->execute();

        if ($reqCreateUnePhrase == false) {
            echo "Erreur dans l'execution de la requête de création d'une phrase.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi";
            $response['data'] = $phrase; // Stockage de la phrase dans le tableau de réponse
        }
    }

    return $response; // Retour du tableau de réponse
}

// Modifier partiellement un objet 
function patchChuckFact($linkpdo, $id, $phrase=null, $vote=null, $faute=null , $signalement=null ) {

    $response = array(); // Initialisation du tableau de réponse

    $reqRecupInfo = $linkpdo->prepare('SELECT * FROM chuckn_facts where id = :id');

    if ($reqRecupInfo == false) {
        echo "Erreur dans la préparation de la requête de récuperation d'info";
    } else { 

        $reqRecupInfo->bindParam(':id', $id, PDO::PARAM_STR); 
        $reqRecupInfo->execute();

    }
    $valeurObjetCourant = $reqRecupInfo->fetch();

    $reqPatchUnePhrase = $linkpdo->prepare('UPDATE chuckn_facts SET phrase = :phrase, vote = :vote, date_modif = :date_modif, faute = :faute, signalement = :signalement WHERE id = :id');

    if ($reqPatchUnePhrase == false) {
        echo "Erreur dans la préparation de la requête de modification partielle d'une phrase.";
    } else {

        if($phrase == null) {
            $reqPatchUnePhrase->bindParam(':phrase', $valeurObjetCourant['phrase'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnePhrase->bindParam(':phrase', $phrase, PDO::PARAM_STR); 
        }

        if($vote == null) {
            $reqPatchUnePhrase->bindParam(':vote', $valeurObjetCourant['vote'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnePhrase->bindParam(':vote', $vote, PDO::PARAM_STR); 
        }
        
        //Pour mettre la date actuelle lors de la modification
        $date_courante = date("Y-m-d H:i:s" );
    
        $reqPatchUnePhrase->bindParam(':date_modif', $date_courante, PDO::PARAM_STR); 
        
        if($faute == null) {
            $reqPatchUnePhrase->bindParam(':faute', $valeurObjetCourant['faute'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnePhrase->bindParam(':faute', $faute, PDO::PARAM_STR); 
        }

        if($signalement == null) {
            $reqPatchUnePhrase->bindParam(':signalement', $valeurObjetCourant['signalement'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnePhrase->bindParam(':signalement', $signalement, PDO::PARAM_STR); 
        }

        $reqPatchUnePhrase->bindParam(':id', $id, PDO::PARAM_STR); 

        $reqPatchUnePhrase->execute();

        if ($reqPatchUnePhrase == false) {
            echo "Erreur dans l'execution de la requête de création d'une phrase.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi, modification partielle effectuée";
        }
    }

    return $response; // Retour du tableau de réponse
}


// Modifier completement un objet 
function putChuckFact($linkpdo, $id, $phrase, $vote, $faute , $signalement ) {

    $response = array(); // Initialisation du tableau de réponse

    $reqPatchUnePhrase = $linkpdo->prepare('UPDATE chuckn_facts SET phrase = :phrase, vote = :vote, date_modif = :date_modif, faute = :faute, signalement = :signalement WHERE id = :id');

    if ($reqPatchUnePhrase == false) {
        echo "Erreur dans la préparation de la requête de modification complète d'une phrase.";
    } else {

        $reqPatchUnePhrase->bindParam(':phrase', $phrase, PDO::PARAM_STR); 
        $reqPatchUnePhrase->bindParam(':vote', $vote, PDO::PARAM_STR); 
        $reqPatchUnePhrase->bindParam(':date_modif', date("Y-m-d H:i:s" ), PDO::PARAM_STR); 
        $reqPatchUnePhrase->bindParam(':faute', $faute, PDO::PARAM_STR); 
        $reqPatchUnePhrase->bindParam(':signalement', $signalement, PDO::PARAM_STR); 
        $reqPatchUnePhrase->bindParam(':id', $id, PDO::PARAM_STR); 

        $reqPatchUnePhrase->execute();

        if ($reqPatchUnePhrase == false) {
            echo "Erreur dans l'execution de la requête de création d'une phrase.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi, modification complète effectuée";
        }
    }

    return $response; // Retour du tableau de réponse
}

/// Ajouter une phrase
function deleteChuckFact($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    $reqDeleteUnePhrase = $linkpdo->prepare('DELETE FROM chuckn_facts WHERE id = :id');

    if ($reqDeleteUnePhrase == false) {
        echo "Erreur dans la préparation de la requête de suppression d'une phrase.";
    } else {

        $reqDeleteUnePhrase->bindParam(':id', $id, PDO::PARAM_STR); 

        $reqDeleteUnePhrase->execute();

        if ($reqDeleteUnePhrase == false) {
            echo "Erreur dans l'execution de la requête de création d'une phrase.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqDeleteUnePhrase->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi, suppression effectuée";
        }
    }

    return $response; // Retour du tableau de réponse
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