<?php

//Récupérer les usagers
function getAllPatients($linkpdo) {

    $response = array(); // Initialisation du tableau de réponse

    $reqGetAllPatients = $linkpdo->prepare('SELECT idP, civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM FROM patient');

        if ($reqGetAllPatients == false) {
            die "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqGetAllPatients->execute();

        if ($reqGetAllPatients == false) {
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
        die "Erreur dans la préparation de la requête d'affichage d'une seule phrase.";
    } else {

        $reqgetPatientsById->bindParam(':idP', $idP, PDO::PARAM_STR); 

        $reqgetPatientsById->execute();

        if ($reqgetPatientsById == false) {
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqgetPatientsById->fetchAll(PDO::FETCH_ASSOC);

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
                        $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse

                    } else {
                        $msgErreur = "Le patient a été ajouté avec succès !";
                        $response['statusCode'] = 200; // Status code
                        $response['statusMessage'] = "La requête a réussi";
                        $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
                    }
                
                }   
            } 
        }   

    return $response; // Retour du tableau de réponse
}

// Modifier partiellement un objet 
function patchPatient($linkpdo, $id, $civilite=null, $nom=null, $prenom=null, $adresse=null, $ville=null, $cp=null, $date_naissance=null, $lieu_naissance=null, $num_secu_sociale=null, $idM=null ) {

    $response = array(); // Initialisation du tableau de réponse

    $msgErreur = ""; // Déclaration de la variable de message d'erreur

    $reqRecupInfo = $linkpdo->prepare('SELECT * FROM patient where idP = :id');

    if ($reqRecupInfo == false) {
        echo "Erreur dans la préparation de la requête de récuperation d'info";
    } else { 

        $reqRecupInfo->bindParam(':id', $id, PDO::PARAM_STR); 
        $reqRecupInfo->execute();
    }
    $valeurObjetCourant = $reqRecupInfo->fetch();

     // Préparation de la requête d'insertion
    // La prochaine fois utiliser + de paramètres dans le where pour éviter de modifier les infos d'un homonyme 
    $reqModification = $linkpdo->prepare('UPDATE patient SET civilite = :nouvelleCivilite, nom = :nouveauNom, prenom = :nouveauPrenom, adresse = :nouvelleAdresse, ville = :nouvelleVille, cp = :nouveauCp, date_naissance = :nouvelleDate_naissance, lieu_naissance = :nouveauLieu_naissance, num_secu_sociale = :nouveauNum_secu_sociale, idM = :nouveauIdM WHERE idP = :idP');

    if ($reqModification === false) {
        echo "Erreur de préparation de la requête.";
    } else {

        if($civilite == null) {
            $reqModification->bindParam(':nouvelleCivilite', $valeurObjetCourant['civilite'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouvelleCivilite', $civilite, PDO::PARAM_STR); 
        }

        if($nom == null) {
            $reqModification->bindParam(':nouveauNom', $valeurObjetCourant['nom'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouveauNom', $nom, PDO::PARAM_STR); 
        }

        if($prenom == null) {
            $reqModification->bindParam(':nouveauPrenom', $valeurObjetCourant['prenom'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouveauPrenom', $prenom, PDO::PARAM_STR); 
        }

        if($adresse == null) {
            $reqModification->bindParam(':nouvelleAdresse', $valeurObjetCourant['adresse'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouvelleAdresse', $adresse, PDO::PARAM_STR); 
        }

        if($ville == null) {
            $reqModification->bindParam(':nouvelleVille', $valeurObjetCourant['ville'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouvelleVille', $ville, PDO::PARAM_STR); 
        }

        if($cp == null) {
            $reqModification->bindParam(':nouveauCp', $valeurObjetCourant['cp'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouveauCp', $cp, PDO::PARAM_STR); 
        }

        if($date_naissance == null) {
            $reqModification->bindParam(':nouvelleDate_naissance', $valeurObjetCourant['date_naissance'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouvelleDate_naissance', $date_naissance, PDO::PARAM_STR); 
        }

        if($lieu_naissance == null) {
            $reqModification->bindParam(':nouveauLieu_naissance', $valeurObjetCourant['lieu_naissance'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouveauLieu_naissance', $lieu_naissance, PDO::PARAM_STR); 
        }

        if($num_secu_sociale == null) {
            $reqModification->bindParam(':nouveauNum_secu_sociale', $valeurObjetCourant['num_secu_sociale'], PDO::PARAM_STR); 
        } else {
            $reqModification->bindParam(':nouveauNum_secu_sociale', $num_secu_sociale, PDO::PARAM_STR); 
        }

        // Vérification si un médecin référent a été choisi et que la valeur n'est pas aucun
        if($idM == "Aucun" || $idM == null)  {
        // Exécuter la requête avec NULL
        $idMedecin = null; 
        } else {
        $idMedecin = $idM;      
        }

        $reqModification->bindParam(':nouveauIdM', $idMedecin, PDO::PARAM_INT);

        // Paramètres du where
        $reqModification->bindParam(':idP', $id, PDO::PARAM_STR);

        // Exécution de la requête
        $reqModification->execute();

        if ($reqModification == false) {
            $msgErreur = "Erreur dans l'execution de la requête de modification d'un patient";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse

        } else {
            $msgErreur = "La requête a réussi, Le patient a été modifié avec succès !";
            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi, modification partielle effectuée";
            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
        }
    }

    return $response; // Retour du tableau de réponse
}



function deletePatient($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    // Préparation de la requête de test de présence d'une consultation pour ce patient
    $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE idP = :idP');

    //Test de la requete de présence d'une consultation => die si erreur
    if($reqExisteDeja == false) {
        die("Erreur de préparation de la requête de test de présence de consultations.");
    } else {

        $reqExisteDeja->bindParam(':idP', $id , PDO::PARAM_STR);

        // Exécution de la requête
        $reqExisteDeja->execute();

        //Vérification de la bonne exécution de la requete ExisteDéja
        //Si oui on arrete et on affiche une erreur
        //Si non on execute la requete
        if($reqExisteDeja == false) {
            die("Erreur dans l'exécution de la requête de test de présence d'une consultation.");
        } else {
            // Récupération du résultat
            $nbConsultations = $reqExisteDeja->fetchColumn();

            // Vérification si la consultation existe déjà
            if ($nbConsultations > 0) {
                $reqDeleteConsultationDuPatient = $linkpdo->prepare('DELETE FROM consultation WHERE idP = :idP');

                if($reqDeleteConsultationDuPatient == false) {
                    die("Erreur de préparation de la requête de suppression de consultations.");
                } else {
                    $reqDeleteConsultationDuPatient->bindParam(':idP', $id , PDO::PARAM_STR);
    
                    // Exécution de la requête
                    $reqDeleteConsultationDuPatient->execute();
                }
            }

            // Préparation de la requête de suppression
            // La prochaine fois utiliser + de paramètres dans le where pour éviter de supprimer les infos d'un homonyme  
            $reqSuppression = $linkpdo->prepare('DELETE FROM patient WHERE idP = :idP');

            if ($reqSuppression === false) {
                echo "Erreur de préparation de la requête.";
            } else {
                // Liaison des paramètres
                $reqSuppression->bindParam(':idP', $id, PDO::PARAM_STR);
                $reqSuppression->execute();

                if ($reqSuppression == false) {
                    $msgErreur = "Erreur dans l'exécution de la requête de suppression : ";
                    $response['statusCode'] = 400;
                    $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse

                } else {
                    // On récupère toutes les phrases
                    $data = $reqSuppression->fetchAll(PDO::FETCH_ASSOC);
                    
                    $msgErreur = "Le patient a été supprimé avec succès !";
                    $response['statusCode'] = 200; // Status code
                    $response['statusMessage'] = "La requête a réussi, suppression effectuée";
                    $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse

                }
            }
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