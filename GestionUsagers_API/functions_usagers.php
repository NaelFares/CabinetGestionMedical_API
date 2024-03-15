<?php

//Récupérer les usagers
function getAllPatients($linkpdo) {

    $response = array(); // Initialisation du tableau de réponse

    $reqGetAllPatients = $linkpdo->prepare('SELECT idP, civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM FROM patient');

        if ($reqGetAllPatients == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqGetAllPatients->execute();

        if ($reqGetAllPatients == false) {
            echo "Erreur dans l'execution de la requête d'affichage.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqGetAllPatients->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi";
            $response['data'] = $data; // Stockage des données dans le tableau de réponse
        }
    }
    return $response; // Retour du tableau de réponse
}

function getPatientsById($linkpdo, $idP) {

    $response = array(); // Initialisation du tableau de réponse

    $reqgetPatientsById = $linkpdo->prepare('SELECT idP, civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM FROM patient WHERE idP = :idP');

    if ($reqgetPatientsById == false) {
        echo "Erreur dans la préparation de la requête d'affichage d'une seule phrase.";
    } else {

        $reqgetPatientsById->bindParam(':idP', $idP, PDO::PARAM_STR); 

        $reqgetPatientsById->execute();

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
function createPatient($linkpdo, $civilite, $nom, $prenom, $adresse, $ville, $cp, $date_naissance, $lieu_naissance, $num_secu_sociale, $idM) {

    $response = array(); // Initialisation du tableau de réponse

    $msgErreur = ""; // Déclaration de la variable de message d'erreur

    // Préparation de la requête de test de présence d'un contact
    $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM patient WHERE nom = :nom AND prenom = :prenom');

    //Test de la requete de présence d'un contact => die si erreur
    if($reqExisteDeja == false) {
        die("Erreur de préparation de la requête de test de présence d'un patient.");
    } else {

        // Liaison des paramètres
        //PDO::PARAM_STR : C'est le type de données que vous spécifiez pour le paramètre. 
        //Ici, on indique que :nom doit être traité comme une chaîne de caractères (string). 
        //Cela permet à PDO de s'assurer que la valeur est correctement échappée et protégée contre les injections SQL
        $reqExisteDeja->bindParam(':nom', $nom, PDO::PARAM_STR);
        $reqExisteDeja->bindParam(':prenom', $prenom, PDO::PARAM_STR);

        // Exécution de la requête
        $reqExisteDeja->execute();

        //Vérification de la bonne exécution de la requete ExisteDéja
        //Si oui on arrete et on affiche une erreur
        //Si non on execute la requete
        if($reqExisteDeja == false) {
            die("Erreur dans l'exécution de la requête de test de présence d'un patient.");
        } else {

            // Récupération du résultat
            $nbPatients = $reqExisteDeja->fetchColumn();

            // Vérification si le patient existe déjà
            if ($nbPatients > 0) {
                $msgErreur = "Ce patient existe déjà dans la base de données";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
                $response['data'] = $msgErreur; // Stockage de la phrase dans le tableau de réponse
            } else {
                // Préparation de la requête d'insertion
                $req = $linkpdo->prepare('INSERT INTO patient(civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM) VALUES(:civilite, :nom, :prenom, :adresse, :ville, :cp, :date_naissance, :lieu_naissance, :num_secu_sociale, :idM)');

                // Vérification du fonctionnement de la requete d'insertion
                if($req == false) {
                    die('Probleme de la préparation de la requete d\'insertion');
                }

                // Attribution des paramètres
                $req->bindParam(':civilite', $civilite, PDO::PARAM_STR);
                $req->bindParam(':nom', $nom, PDO::PARAM_STR);
                $req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $req->bindParam(':adresse', $adresse, PDO::PARAM_STR);
                $req->bindParam(':ville', $ville, PDO::PARAM_STR);
                $req->bindParam(':cp', $cp, PDO::PARAM_STR);
                $req->bindParam(':date_naissance', $date_naissance, PDO::PARAM_STR);
                $req->bindParam(':lieu_naissance', $lieu_naissance, PDO::PARAM_STR);
                $req->bindParam(':num_secu_sociale', $num_secu_sociale, PDO::PARAM_STR);

                    // Vérification si un médecin référent a été choisi et que la valeur n'est pas vide
                    if ($idM != null) {
                    $idMParamReq = $idM;      
                } else {
                    // Exécuter la requête avec NULL
                    $idMParamReq = null; 
                }

                $req->bindParam(':idM', $idMParamReq, PDO::PARAM_INT);
                
                /// Exécution de la requête d'insertion
                $req->execute();

                    if ($req == false) {
                        $msgErreur = "Erreur d'exécution de la requête";
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    } else {
                        $msgErreur = "Le patient a été ajouté avec succès !";
                        $response['statusCode'] = 200; // Status code
                        $response['statusMessage'] = "La requête a réussi";
                        $response['data'] = $msgErreur; // Stockage de la phrase dans le tableau de réponse
                    }
                
                }   
            } 
        }   

    return $response; // Retour du tableau de réponse
}

// Modifier partiellement un objet 
function patchChuckFact($linkpdo, $id, $phrase=null, $vote=null, $faute=null , $signalement=null,  $civilite, $nom, $prenom, $adresse, $ville, $cp, $date_naissance, $lieu_naissance, $num_secu_sociale, $idM ) {

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