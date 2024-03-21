<?php

/// Envoi de la réponse au Client
function getAllMedecins($linkpdo) {

    $response = array(); // Initialisation du tableau de réponse

    $reqAllMedecin = $linkpdo->prepare('SELECT idM, civilite, prenom, nom FROM medecin');

    if ($reqAllMedecin == false) {
        $response['statusCode'] = 400;
        $response['statusMessage'] = "Erreur dans la préparation de la requête d'affichage.";    
    } else {
        $reqAllMedecin->execute();

        if ($reqAllMedecin == false) {
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans l'execution de la requête d'affichage.";
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

function getMedecinById($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    $reqMedecinParId = $linkpdo->prepare('SELECT idM, civilite, prenom, nom FROM medecin WHERE idM = :idM');

    if ($reqMedecinParId == false) {
        $response['statusCode'] = 400;
        $response['statusMessage'] = "Erreur dans la préparation de la requête d'affichage d'un seul medecin.";
    } else {

        $reqMedecinParId->bindParam(':idM', $id, PDO::PARAM_STR); 

        $reqMedecinParId->execute();

        if ($reqMedecinParId == false) {
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans l'execution de la requête d'affichage d'un seul medecin par un Id.";
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
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans la préparation de la requête de pré création d'un medecin.";        
        } else {

            $reqExisteDeja->bindParam(':nom', $nom, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':prenom', $prenom, PDO::PARAM_STR);

            // Exécution de la requête
            $reqExisteDeja->execute();

            //Vérification de la bonne exécution de la requete ExisteDéja
            if($reqExisteDeja == false) {
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Erreur dans l'execution de la requête de création d'un medecin.";                    
            } else {

                // Récupération du résultat
                $nbMedecins = $reqExisteDeja->fetchColumn();

                // Vérification si le patient existe déjà
                if ($nbMedecins > 0) {
                    $msgErreur = "Ce medecin est déjà enregistré.";
                    $response['statusCode'] = 200;
                    $response['statusMessage'] = "La requête a réussie";
                    $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
                } else {

                    $reqCreateMedecin = $linkpdo->prepare('INSERT INTO medecin (civilite, nom, prenom) VALUES (:civilite, :nom, :prenom)');

                    if ($reqCreateMedecin == false) {
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Erreur dans la préparation de la requête de création d'un medecin.";
                    } else {

                        $reqCreateMedecin->bindParam(':civilite', $civilite, PDO::PARAM_STR); 
                        $reqCreateMedecin->bindParam(':nom', $nom, PDO::PARAM_STR); 
                        $reqCreateMedecin->bindParam(':prenom', $prenom, PDO::PARAM_STR); 

                        $reqCreateMedecin->execute();

                        if ($reqCreateMedecin == false) {
                            $msgErreur = "Erreur d'exécution de la requête";
                            $response['statusCode'] = 400;
                            $response['statusMessage'] = "Syntaxe de la requête non conforme";
                            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse    
                        } else {
                            $msgErreur = "Le medecin a été ajouté avec succès !";
                            $response['statusCode'] = 200; // Status code
                            $response['statusMessage'] = "La requête a réussie";
                            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
                        }
                    }
                }
            }
            return $response; // Retour du tableau de réponse
        }
    }

// Modifier partiellement un objet 
function patchMedecin($linkpdo, $id, $civilite=null, $nom=null, $prenom=null) {

    $response = array(); // Initialisation du tableau de réponse

    $msgErreur = ""; // Déclaration de la variable de message d'erreur

    $reqRecupMedecin = $linkpdo->prepare('SELECT * FROM medecin where idM = :idM');

    if ($reqRecupMedecin == false) {
        $response['statusCode'] = 500;
        $response['statusMessage'] = "Erreur dans la préparation de la requête de récuperation du medecin: ";
        return $response;
    } else { 
        $reqRecupMedecin->bindParam(':idM', $id, PDO::PARAM_STR); 
        $reqRecupMedecin->execute();
    }

    $valeurObjetCourant = $reqRecupMedecin->fetch();

    $reqPatchUnMedecin = $linkpdo->prepare('UPDATE medecin SET civilite = :civilite, nom = :nom, prenom = :prenom WHERE idM = :idM');

    if ($reqPatchUnMedecin == false) {
        $response['statusCode'] = 500;
        $response['statusMessage'] = "Erreur dans la préparation de la requête de modification partielle d'un medecin: ";
        return $response;
    } else {

        if($civilite == null) {
            $reqPatchUnMedecin->bindParam(':civilite', $valeurObjetCourant['civilite'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnMedecin->bindParam(':civilite', $civilite, PDO::PARAM_STR); 
        }

        if($nom == null) {
            $reqPatchUnMedecin->bindParam(':nom', $valeurObjetCourant['nom'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnMedecin->bindParam(':nom', $nom, PDO::PARAM_STR); 
        }
        
        if($prenom == null) {
            $reqPatchUnMedecin->bindParam(':prenom', $valeurObjetCourant['prenom'], PDO::PARAM_STR); 
        } else {
            $reqPatchUnMedecin->bindParam(':prenom', $prenom, PDO::PARAM_STR); 
        }

        $reqPatchUnMedecin->bindParam(':idM', $id, PDO::PARAM_STR); 

        $reqPatchUnMedecin->execute();

        if ($reqPatchUnMedecin == false ) {
            $msgErreur = "Erreur dans l'execution de la requête de modification d'un médecin";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur lors de l'exécution de la requête : ";
            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
        } else {
            $msgErreur = "La requête a réussi, Le médecin a été modifié avec succès !";
            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussie, modification partielle effectuée";
            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
        }
    }
    return $response; // Retour du tableau de réponse
}


/// supprimer un medecin
function deleteMedecin($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    // Préparation de la requête de test de présence d'une consultation pour ce medecin
    $reqMedConsult = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE idM = :idM');

    //Test de la requete de présence d'une consultation => die si erreur
    if($reqMedConsult == false) {
        $response['statusCode'] = 500;
        $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'un medecin (il a des consultations) ";
        return $response;    
    } else {

        $reqMedConsult->bindParam(':idM', $idM , PDO::PARAM_STR);

        // Exécution de la requête
        $reqMedConsult->execute();

        //Vérification de la bonne exécution de la requete ExisteDéja
        //Si oui on arrete et on affiche une erreur
        //Si non on execute la requete
        if($reqMedConsult == false) {
            $response['statusCode'] = 500;
            $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'un medecin (il a des consultations) (2) ";
            return $response;         
        } else {
             // Récupération du résultat
            $nbConsultations = $reqMedConsult->fetchColumn();

             // Vérification si la consultation existe déjà
            if ($nbConsultations > 0) {
                $reqDeleteConsultationDuMedecin = $linkpdo->prepare('DELETE FROM consultation WHERE idM = :idM');

                if($reqDeleteConsultationDuMedecin == false) {
                    $response['statusCode'] = 500;
                    $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'un medecin (il a des consultations) (3) ";
                    return $response; 
                } else {
                    $reqDeleteConsultationDuMedecin->bindParam(':idM', $idM , PDO::PARAM_STR);
    
                    // Exécution de la requête
                    $reqDeleteConsultationDuMedecin->execute();
                }
            }

                 // Préparation de la requête de test de présence d'un medecin referent
                $reqReferentExiste = $linkpdo->prepare('SELECT COUNT(*) FROM patient WHERE idM = :idM');

                //Test de la requete de présence d'un medecin referent => die si erreur
                if($reqReferentExiste == false) {
                    $response['statusCode'] = 500;
                    $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'un medecin (il est réferent) ";
                    return $response;                 
                } else {

                    $reqReferentExiste->bindParam(':idM', $idM , PDO::PARAM_STR);

                    // Exécution de la requête
                    $reqReferentExiste->execute();

                    //Vérification de la bonne exécution de la requete ExisteDéja
                    //Si oui on arrete et on affiche une erreur
                    //Si non on execute la requete
                    if($reqReferentExiste == false) {
                        $response['statusCode'] = 500;
                        $response['statusMessage'] = "Erreur dans l'execution de la requête de suppression d'un medecin (il est réferent) (2) ";
                        return $response;                       
                    } else {
                        // Récupération du résultat
                        $nbReference = $reqReferentExiste->fetchColumn();

                        // Vérification si il ya une reference
                        if ($nbReference > 0) {
                            // Mettre à jour les références à NULL dans la table Patient
                            $reqUpdateReferenceMedecin = $linkpdo->prepare('UPDATE Patient SET idM = NULL WHERE idM = :idM');

                            if($reqUpdateReferenceMedecin == false) {
                                $response['statusCode'] = 500;
                                $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'un medecin (il est réferent) (3)";
                                return $response;                               
                            } else {
                                $reqUpdateReferenceMedecin->bindParam(':idM', $idM , PDO::PARAM_STR);
                
                                // Exécution de la requête
                                $reqUpdateReferenceMedecin->execute();
                            }
                        }

                    $reqDeleteMedecin = $linkpdo->prepare('DELETE FROM medecin WHERE idM = :idM');

                    if ($reqDeleteMedecin == false) {
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'un medecin.";                    } else {

                        $reqDeleteMedecin->bindParam(':idM', $id, PDO::PARAM_STR); 

                        $reqDeleteMedecin->execute();

                        if ($reqDeleteMedecin == false) {
                            $msgErreur = "Erreur dans l'exécution de la requête de suppression : ";
                            $response['statusCode'] = 400;
                            $response['statusMessage'] = "Syntaxe de la requête non conforme";
                            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse

                        } else {
                            // On récupère toutes les phrases
                            $data = $reqDeleteMedecin->fetchAll(PDO::FETCH_ASSOC);

                            $msgErreur = "Le medecin a été supprimé avec succès !";
                            $response['statusCode'] = 200; // Status code
                            $response['statusMessage'] = "La requête a réussi, suppression effectuée";
                            $response['data'] = $msgErreur; // Stockage du message dans le tableau de réponse
                        }
                    }
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