<?php

/// Envoi de la réponse au Client
function getAllMedecins($linkpdo) {

    $response = array(); // Initialisation du tableau de réponse

    $reqAllMedecin = $linkpdo->prepare('SELECT idM, civilite, prenom, nom FROM medecin');

    if ($reqAllMedecin == false) {
        $response['statusCode'] = 400;
        $response['statusMessage'] = "Syntaxe de la requête non conforme";
    } else {
        $reqAllMedecin->execute();

        if ($reqAllMedecin == false) {
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            // On récupère toutes les phrases
            $data = $reqAllMedecin->fetchAll(PDO::FETCH_ASSOC);

            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussi";
            $response['data'] = $data; // Stockage des données dans le tableau de réponse
        }
    }
    return $response; // Retour du tableau de réponse
}

function getMedecinById($linkpdo, $id) {

    $response = array(); // Initialisation du tableau de réponse

    // Préparation de la requête de test de présence
    $reqExiste = $linkpdo->prepare('SELECT COUNT(*) FROM medecin WHERE idM = :idM');

     //Test de la requete de présence
     if($reqExiste == false) {
         die("Erreur de préparation de la requête de test de présence d'un médecin.");
     } else {

         $reqExiste->bindParam(":idM", $id, PDO::PARAM_STR);
         // Exécution de la requête
         $reqExiste->execute();

         //Vérification de la bonne exécution de la requete
         //Si oui on arrete et on affiche une erreur
         //Si non on execute la requete
         if($reqExiste == false) {
             die("Erreur dans l'exécution de la requête de test de présence");
         } else {

             // Récupération du résultat
             $nbMedecinId = $reqExiste->fetchColumn();

             // Vérification de présence
             if ($nbMedecinId == 0) {
                 $response['statusCode'] = 404;
                 $response['statusMessage'] = "Erreur, la ressource demandée n'existe pas";
                 $response['data'] = null; 
             } else {

                $reqMedecinParId = $linkpdo->prepare('SELECT idM, civilite, prenom, nom FROM medecin WHERE idM = :idM');

                if ($reqMedecinParId == false) {
                    $response['statusCode'] = 400;
                    $response['statusMessage'] = "Syntaxe de la requête non conforme";
                } else {

                    $reqMedecinParId->bindParam(':idM', $id, PDO::PARAM_STR); 

                    $reqMedecinParId->execute();

                    if ($reqMedecinParId == false) {
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    } else {
                        // On récupère tout les medecins
                        $data = $reqMedecinParId->fetchAll(PDO::FETCH_ASSOC);

                        $response['statusCode'] = 200; // Status code
                        $response['statusMessage'] = "La requête a réussi";
                        $response['data'] = $data; // Stockage des données dans le tableau de réponse
                    }
                }
            }
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
            $response['statusMessage'] = "Syntaxe de la requête non conforme";        
        } else {

            $reqExisteDeja->bindParam(':nom', $nom, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':prenom', $prenom, PDO::PARAM_STR);

            // Exécution de la requête
            $reqExisteDeja->execute();

            //Vérification de la bonne exécution de la requete ExisteDéja
            if($reqExisteDeja == false) {
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";                    
            } else {

                // Récupération du résultat
                $nbMedecins = $reqExisteDeja->fetchColumn();

                // Vérification si le medecin existe déjà
                if ($nbMedecins > 0) {
                    $msgErreur = "Ce medecin est déjà enregistré.";
                    $response['statusCode'] = 409;
                    $response['statusMessage'] = "Erreur, Ce medecin est déjà enregistré";
                    $response['data'] = null; // Stockage du message dans le tableau de réponse
                } else {

                    $reqCreateMedecin = $linkpdo->prepare('INSERT INTO medecin (civilite, nom, prenom) VALUES (:civilite, :nom, :prenom)');

                    if ($reqCreateMedecin == false) {
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    } else {

                        $reqCreateMedecin->bindParam(':civilite', $civilite, PDO::PARAM_STR); 
                        $reqCreateMedecin->bindParam(':nom', $nom, PDO::PARAM_STR); 
                        $reqCreateMedecin->bindParam(':prenom', $prenom, PDO::PARAM_STR); 

                        $reqCreateMedecin->execute();
                        $data = $reqCreateMedecin-> fetchAll(PDO::FETCH_ASSOC);

                        if ($reqCreateMedecin == false) {
                            $msgErreur = "Erreur d'exécution de la requête";
                            $response['statusCode'] = 400;
                            $response['statusMessage'] = "Syntaxe de la requête non conforme";
                            $response['data'] = null; // Stockage du message dans le tableau de réponse    
                        } else {
                            $msgErreur = "Le medecin a été ajouté avec succès !";
                            $response['statusCode'] = 201; // Status code
                            $response['statusMessage'] = "La requête a réussi et une nouvelle ressource a été créée";
                            $response['data'] = $data; // Stockage du message dans le tableau de réponse
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
        $response['statusCode'] = 400;
        $response['statusMessage'] = "Syntaxe de la requête non conforme";
    } else { 
        $reqRecupMedecin->bindParam(':idM', $id, PDO::PARAM_STR); 
        $reqRecupMedecin->execute();
    }

    $valeurObjetCourant = $reqRecupMedecin->fetch();

    $reqPatchUnMedecin = $linkpdo->prepare('UPDATE medecin SET civilite = :civilite, nom = :nom, prenom = :prenom WHERE idM = :idM');

    if ($reqPatchUnMedecin == false) {
        $response['statusCode'] = 400;
        $response['statusMessage'] = "Syntaxe de la requête non conforme";
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
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
            $response['data'] = null; // Stockage du message dans le tableau de réponse
        } else {
            $msgErreur = "La requête a réussi, Le médecin a été modifié avec succès !";
            $response['statusCode'] = 200; // Status code
            $response['statusMessage'] = "La requête a réussie, modification partielle effectuée";
            $response['data'] = null; // Stockage du message dans le tableau de réponse
        }
    }
    return $response; // Retour du tableau de réponse
}


/// supprimer un medecin
function deleteMedecin($linkpdo, $id){            
            
    $reqMedConsult = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE idM = :idM');

    //Test de la requete de présence d'une consultation => die si erreur
    if($reqMedConsult == false) {
        $response['statusCode'] = 400;
        $response['statusMessage'] = "Syntaxe de la requête non conforme";
    } else {

        $reqMedConsult->bindParam(':idM', $id , PDO::PARAM_STR);

        // Exécution de la requête
        $reqMedConsult->execute();

        //Vérification de la bonne exécution de la requete ExisteDéja
        if($reqMedConsult == false) {
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";        
        } else {
                // Récupération du résultat
                $nbConsultations = $reqMedConsult->fetchColumn();

                // Vérification si la consultation existe déjà
                if ($nbConsultations > 0) {
                $reqDeleteConsultationDuMedecin = $linkpdo->prepare('DELETE FROM consultation WHERE idM = :idM');

                if($reqDeleteConsultationDuMedecin == false) {
                    $response['statusCode'] = 400;
                    $response['statusMessage'] = "Requête non conform, une consultation est enregistrée avec ce medecin";
                } else {
                    $reqDeleteConsultationDuMedecin->bindParam(':idM', $id , PDO::PARAM_STR);
    
                    // Exécution de la requête
                    $reqDeleteConsultationDuMedecin->execute();
                }
            }

                    ////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //////////////////////////////// Test de présence de médecin référent //////////////////////////////////
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////

                     // Préparation de la requête de test de présence d'un medecin referent
                    $reqReferentExiste = $linkpdo->prepare('SELECT COUNT(*) FROM patient WHERE idM = :idM');

                    //Test de la requete de présence d'un medecin referent => die si erreur
                    if($reqReferentExiste == false) {
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                        return $response;                 
                    } else {

                        $reqReferentExiste->bindParam(':idM', $id , PDO::PARAM_STR);

                        // Exécution de la requête
                        $reqReferentExiste->execute();

                        //Vérification de la bonne exécution de la requete ExisteDéja
                        //Si oui on arrete et on affiche une erreur
                        //Si non on execute la requete
                        if($reqReferentExiste == false) {
                            $response['statusCode'] = 400;
                            $response['statusMessage'] = "Syntaxe de la requête non conforme";    
                        } else {        
                            // Récupération du résultat
                            $nbReference = $reqReferentExiste->fetchColumn();

                            // Vérification si il ya une reference
                            if ($nbReference > 0) {
                                // Mettre à jour les références à NULL dans la table Patient
                                $reqUpdateReferenceMedecin = $linkpdo->prepare('UPDATE Patient SET idM = NULL WHERE idM = :idM');

                                if($reqUpdateReferenceMedecin == false) {
                                    $response['statusCode'] = 400;
                                    $response['statusMessage'] = "Syntaxe de la requête non conforme";    
                                } else {
                                    $reqUpdateReferenceMedecin->bindParam(':idM', $id , PDO::PARAM_STR);
                    
                                    // Exécution de la requête
                                    $reqUpdateReferenceMedecin->execute();
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////////////////
                            ///////////////////////////////////// suppression du médecin ///////////////////////////////////////////
                            ////////////////////////////////////////////////////////////////////////////////////////////////////////

                            // Préparation de la requête de suppression
                            $reqDeleteMedecin = $linkpdo->prepare('DELETE FROM medecin WHERE idM = :idM');

                            if ($reqDeleteMedecin == false) {
                                $response['statusCode'] = 400;
                                $response['statusMessage'] = "Syntaxe de la requête non conforme";
        
                            } else {
                                // Liaison des paramètres
                                $reqDeleteMedecin->bindParam(':idM', $id, PDO::PARAM_STR);
                                
                                // Exécution de la requête
                                $reqDeleteMedecin->execute();

                                if ($reqDeleteMedecin == false) {
                                    $msgErreur = "Erreur dans l'exécution de la requête de suppression : ";
                                    $response['statusCode'] = 400;
                                    $response['statusMessage'] = "Syntaxe de la requête non conforme";
                                    $response['data'] = null; // Stockage du message dans le tableau de réponse
        
                                } else {
                                    // On récupère toutes les phrases
                                    $data = $reqDeleteMedecin->fetchAll(PDO::FETCH_ASSOC);
        
                                    $msgErreur = "Le medecin a été supprimé avec succès !";
                                    $response['statusCode'] = 200; // Status code
                                    $response['statusMessage'] = "La requête a réussi, médecin supprimé avec succès";
                                    $response['data'] = null; // Stockage du message dans le tableau de réponse
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