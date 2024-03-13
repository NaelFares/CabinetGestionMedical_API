<?php

/// Envoi de la réponse au Client
function readMedecin($linkpdo) {

    $response = array(); // Initialisation du tableau de réponse

    $reqAllMedecin = $linkpdo->prepare('SELECT idM, civilite, prenom, nom FROM medecin');

    if ($reqAllMedecin == false) {
        echo "Erreur dans la préparation de la requête d'affichage.";
    } else {
        $reqAllMedecin->execute();

        if ($reqAllMedecin == false) {
            echo "Erreur dans l'execution de la requête d'affichage.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqAllMedecin->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussie";
            $response['data'] = $data; // Stockage des données dans le tableau de réponse
        }
    }

    return $response; // Retour du tableau de réponse
}

function readMedecinParId($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    $reqMedecinParId = $linkpdo->prepare('SELECT id, civilite, prenom, nom FROM medecin WHERE id = :id');

    if ($reqMedecinParId == false) {
        echo "Erreur dans la préparation de la requête d'affichage d'un seul medecin.";
    } else {

        $reqMedecinParId->bindParam(':id', $id, PDO::PARAM_STR); 

        $reqMedecinParId->execute();

        if ($reqMedecinParId == false) {
            echo "Erreur dans l'execution de la requête d'affichage d'un seul medecin par un Id.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère tout les medecins
            $data = $reqMedecinParId->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussie";
            $response['data'] = $data; // Stockage des données dans le tableau de réponse
        }
    }

    return $response; // Retour du tableau de réponse
}

/// Ajouter une phrase
function createMedecin($linkpdo, $civilite, $nom, $prenom) {

    $response = array(); // Initialisation du tableau de réponse

        // Préparation de la requête de test de présence d'un medecin
        $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM medecin WHERE nom = :nom AND prenom = :prenom');

        //Test de la requete de présence d'un medecin => die si erreur
        if($reqExisteDeja == false) {
            echo "Erreur dans l'execution de la requête de création d'un medecin.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";        
        } else {

            $reqExisteDeja->bindParam(':nom', $nom, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':prenom', $prenom, PDO::PARAM_STR);

            // Exécution de la requête
            $reqExisteDeja->execute();

            //Vérification de la bonne exécution de la requete ExisteDéja
            if($reqExisteDeja == false) {
                die("Erreur dans l'exécution de la requête de test de présence d'un medecin.");
            } else {

                // Récupération du résultat
                $nbMedecins = $reqExisteDeja->fetchColumn();

                // Vérification si le patient existe déjà
                if ($nbMedecins > 0) {
                    echo "Erreur dans l'execution de la requête de création d'une phrase.";
                    $response['statusCode'] = 409;
                    $response['statusMessage'] = "Existe déjà";
                } else {

                    $reqCreateMedecin = $linkpdo->prepare('INSERT INTO medecin (civilite, nom, prenom) VALUES (:civilite, :nom, :prenom)');

                    if ($reqCreateMedecin == false) {
                        echo "Erreur dans la préparation de la requête de création d'un medecin.";
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    } else {

                        $reqCreateMedecin->bindParam(':civilite', $civilite, PDO::PARAM_STR); 
                        $reqCreateMedecin->bindParam(':nom', $nom, PDO::PARAM_STR); 
                        $reqCreateMedecin->bindParam(':prenom', $prenom, PDO::PARAM_STR); 

                        $reqCreateMedecin->execute();

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
                }
            }
            return $response; // Retour du tableau de réponse
        }
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
function deleteMedecin($linkpdo, $id) {

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