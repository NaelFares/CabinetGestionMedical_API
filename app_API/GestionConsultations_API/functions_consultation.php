<?php
	/*Ce fichier contiendra toutes les définitions des fonctions de manipulations des données en SQL.*/

    function getAllConsultations($linkpdo){

        $response = array(); // Initialisation du tableau de la réponse

        $reqAllConsultations = $linkpdo->prepare('SELECT idC, date_consultation, heure_debut, duree, idM, idP FROM consultation ORDER BY date_consultation ASC');

        if($reqAllConsultations == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            $reqAllConsultations->execute();

            if($reqAllConsultations == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
            } else {
                // On récupère toutes les consultations
                $data = $reqAllConsultations->fetchAll(PDO::FETCH_ASSOC);
                $response['statusCode'] = 200;
                $response['statusMessage'] = "La requête à réussi";
                $response['data'] = $data;
            }
        }
        return $response;
    }


    function getConsultationById($linkpdo, $id) {
        $response = array(); // Initialisation du tableau de la réponse

        // Préparation de la requête de test de présence
        $reqExiste = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE idC = :idC');

        //Test de la requete de présence
        if($reqExiste == false) {
            die("Erreur de préparation de la requête de test de présence d'une consultation.");
        } else {

            $reqExiste->bindParam(":idC", $id, PDO::PARAM_STR);
            // Exécution de la requête
            $reqExiste->execute();

            //Vérification de la bonne exécution de la requete
            //Si oui on arrete et on affiche une erreur
            //Si non on execute la requete
            if($reqExiste == false) {
                die("Erreur dans l'exécution de la requête de test de présence");
            } else {

                // Récupération du résultat
                $nbConsultationId = $reqExiste->fetchColumn();

                // Vérification de présence
                if ($nbConsultationId == 0) {
                    $response['statusCode'] = 404;
                    $response['statusMessage'] = "Erreur, la ressource demandée n'existe pas";
                    $response['data'] = null; 
                } else {

                    $reqConsultationParId = $linkpdo->prepare('SELECT idC, idM, date_consultation, heure_debut, duree, idP FROM consultation WHERE idC = :idC');

                    if($reqConsultationParId == false){
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    } else {
                        $reqConsultationParId->bindParam(":idC", $id, PDO::PARAM_STR);

                        $reqConsultationParId->execute();

                        if($reqConsultationParId == false){
                            $response['statusCode'] = 400;
                            $response['statusMessage'] = "Syntaxe de la requête non conforme";
                        } else { 
                            $data = $reqConsultationParId->fetchAll(PDO::FETCH_ASSOC);
                            $response['statusCode'] = 200;
                            $response['statusMessage'] = "La requête a réussi";
                            $response['data'] = $data;
                        }
                    }
                }
            }
        }
        return $response;
    }

    function createConsultation($linkpdo, $idM, $idP, $date_consultation_param, $heure_debut_param, $duree_param){

        // Convertir la date reçue dans le format de la base de données
        $date_consultation = DateTime::createFromFormat('d/m/y', $date_consultation_param)->format('Y-m-d');
            
        // Convertir l'heure reçue dans le format de la base de données
        $heure_debut = DateTime::createFromFormat('H:i', $heure_debut_param)->format('H:i:s');
        
        // Convertir la durée reçue dans le format de la base de données
        $duree = DateTime::createFromFormat('i', $duree_param)->format('H:i:s');

        $response = array(); // Initialisation du tableau de la réponse

        //Vérifier si la consultation n'existe pas déja
        $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree');

        if($reqExisteDeja == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";  
        } else {

            $reqExisteDeja->bindParam(':date_consultation',$date_consultation, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':heure_debut',$heure_debut, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':duree',$duree, PDO::PARAM_STR);

            $reqExisteDeja->execute();

            if ($reqExisteDeja == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
            } else {

                $nbConsultations = $reqExisteDeja->fetchColumn();

                if ($nbConsultations > 0){
                    $msgErreur = "Cette consultation est déjà enregistrée.";
                    $response['statusCode'] = 409;
                    $response['statusMessage'] = "Erreur, la consultation est déjà enregistrée";
                    $response['data'] = null; 
                } else {

                     // Préparation de la requête de test de chevauchement de consultation pour un medecin
                    $reqChevauchementMedecin = $linkpdo->prepare('SELECT COUNT(*)
                    FROM consultation
                    WHERE idM = :idM
                    AND date_consultation = :date_consultation
                    AND (
                        (:heure_debut BETWEEN heure_debut AND ADDTIME(heure_debut, duree))
                        OR (ADDTIME(:heure_debut, :duree) BETWEEN heure_debut AND ADDTIME(heure_debut, duree))
                        OR (heure_debut BETWEEN :heure_debut AND ADDTIME(:heure_debut, :duree))
                        OR (ADDTIME(heure_debut, duree )BETWEEN :heure_debut AND ADDTIME(:heure_debut, :duree))
                    )');

                        // Préparation de la requête de test de chevauchement de consultation pour un patient
                        $reqChevauchementPatient= $linkpdo->prepare('SELECT COUNT(*)
                        FROM consultation
                        WHERE idP = :idP
                        AND date_consultation = :date_consultation
                        AND (
                            (:heure_debut BETWEEN heure_debut AND ADDTIME(heure_debut, duree))
                            OR (ADDTIME(:heure_debut, :duree) BETWEEN heure_debut AND ADDTIME(heure_debut, duree))
                            OR (heure_debut BETWEEN :heure_debut AND ADDTIME(:heure_debut, :duree))
                            OR (ADDTIME(heure_debut, duree )BETWEEN :heure_debut AND ADDTIME(:heure_debut, :duree))
                        )');

                    

                    if($reqChevauchementMedecin == false || $reqChevauchementPatient == false) {
                        die("Erreur de préparation de la requête de test de chevauchement d'une consultation.");
                    } else {

                         // Liaison des paramètres
                            //PDO::PARAM_STR : C'est le type de données que vous spécifiez pour le paramètre. 
                            //Ici, on indique que :nom doit être traité comme une chaîne de caractères (string). 
                            //Cela permet à PDO de s'assurer que la valeur est correctement échappée et protégée contre les injections SQL
                            $reqChevauchementMedecin->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
                            $reqChevauchementMedecin->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                            $reqChevauchementMedecin->bindParam(':duree', $duree, PDO::PARAM_STR);
                            $reqChevauchementMedecin->bindParam(':idM', $idM, PDO::PARAM_STR);

                            // Liaison des paramètres pour la requête chevauchement patient
                            $reqChevauchementPatient->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
                            $reqChevauchementPatient->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                            $reqChevauchementPatient->bindParam(':duree', $duree, PDO::PARAM_STR);
                            $reqChevauchementPatient->bindParam(':idP', $idP, PDO::PARAM_STR);

                            // Exécution de la requête
                            $reqChevauchementMedecin->execute();

                            // Exécution de la requête
                            $reqChevauchementPatient->execute();


                             //Vérification de la bonne exécution de la requete de test de chevauchement
                            //Si non on arrete et on affiche une erreure
                            //Si oui on execute la requete
                            if($reqChevauchementMedecin == false || $reqChevauchementPatient == false) {
                                die("Erreur dans l'exécution de la requête de test de chevauchement de consultation.");
                            } else {

                                // Récupération du résultat pour un medecin
                                $nbConsultationChevauchementMedecin = $reqChevauchementMedecin->fetchColumn();

                                // Récupération du résultat pour un patient
                                $nbConsultationChevauchementPatient = $reqChevauchementPatient->fetchColumn();

                                // Vérification de chevauchement de la consultation
                                if (($nbConsultationChevauchementMedecin > 0)  || ($nbConsultationChevauchementPatient > 0)) {
                                    $msgErreur = "Créneau déjà réservé";
                                    $response['statusCode'] = 400;
                                    $response['statusMessage'] = "Erreur, le créneau est déjà réservé";
                                } else {

                                    $reqCreateConsultation = $linkpdo->prepare('INSERT INTO consultation(date_consultation, heure_debut, duree, idP, idM) VALUES(:date_consultation, :heure_debut, :duree, :idP, :idM)');

                                    if($reqCreateConsultation == false){
                                        $response['statusCode'] = 400;
                                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                                    } else {

                                        $reqCreateConsultation->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
                                        $reqCreateConsultation->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                                        $reqCreateConsultation->bindParam(':duree', $duree, PDO::PARAM_STR);
                                        $reqCreateConsultation->bindParam(':idP', $idP, PDO::PARAM_STR);
                                        $reqCreateConsultation->bindParam(':idM', $idM, PDO::PARAM_STR);

                                        $reqCreateConsultation->execute();

                                        if ($reqCreateConsultation == false){
                                            $msgErreur = "Erreur dans l'execution de la requête de création";
                                            $response['statusCode'] = 400;
                                            $response['statusMessage'] = "Syntaxe de la requête non conforme";
                                            $response['data'] = null; // Stockage du message dans le tableau de réponse
                                        } else {
                                            $msgErreur = "La consultation a été crée avec succès";
                                            $response['statusCode'] = 201; 
                                            $response['statusMessage'] = "La requête a réussi et une ressource a été créée";
                                            $response['data'] = null; // Stockage du message dans le tableau de réponse
                                        }
                                    }
                                }
                            }
                    }
                }
        }
    }
    return $response;
}


    function patchConsultation($linkpdo, $id, $idM=null, $idP=null, $date_consultation=null, $heure_debut=null, $duree=null) {
        $response = array();

        $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree AND idP = :idP AND idM = :idM');

         //Test de la requete de présence d'une consultation => die si erreur
        if($reqExisteDeja == false) {
            die("Erreur de préparation de la requête de test de présence d'une consultation.");
        } else {
            
            // Liaison des paramètres
            $reqExisteDeja->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':duree', $duree, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':idP', $idP, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':idM', $idM, PDO::PARAM_STR);

            // Exécution de la requête
            $reqExisteDeja->execute();

            //Vérification de la bonne exécution de la requete ExisteDéja
                    //Si non on arrete et on affiche une erreur
                    //Si oui on execute la requete
                    if($reqExisteDeja == false) {
                        die("Erreur dans l'exécution de la requête de test de présence d'une consultation.");
                    } else {

                        // Récupération du résultat
                        $nbConsultations = $reqExisteDeja->fetchColumn();

                        // Vérification si la consultation existe déjà
                        if ($nbConsultations > 0) {
                            $msgErreur = "Cette consultation est déjà enregistrée.";
                            $response['statusCode'] = 409;
                            $response['statusMessage'] = "Erreur, cette consultation est déjà enregistrée";
                            $response['data'] = null; 
                        } else {
                                $reqRecupConsultation = $linkpdo->prepare('SELECT * FROM consultation where idC = :idC');

                                if ($reqRecupConsultation == false) {
                                    $response['statusCode'] = 400;
                                    $response['statusMessage'] = "Syntaxe de la requête non conforme";
                                } else { 
                                    $reqRecupConsultation->bindParam(':idC', $id, PDO::PARAM_STR); 
                                    $reqRecupConsultation->execute();
                                }
                            
                                $valeurObjetCourant = $reqRecupConsultation->fetch();
                            
                                $reqPatchUneConsultation = $linkpdo->prepare('UPDATE consultation SET idM = :idM, idP = :idP, date_consultation = :date_consultation, heure_debut = :heure_debut, duree = :duree WHERE idC = :idC');
                            
                                if ($reqPatchUneConsultation == false) {
                                    $response['statusCode'] = 400;
                                    $response['statusMessage'] = "Syntaxe de la requête non conforme";
                                    return $response;
                                } else {

                                    if($idM == null) {
                                        $reqPatchUneConsultation->bindParam(':idM', $valeurObjetCourant['idM'], PDO::PARAM_STR); 
                                    } else {
                                        $reqPatchUneConsultation->bindParam(':idM', $idM, PDO::PARAM_STR); 
                                    }

                                    if($idP == null) {
                                        $reqPatchUneConsultation->bindParam(':idP', $valeurObjetCourant['idP'], PDO::PARAM_STR); 
                                    } else {
                                        $reqPatchUneConsultation->bindParam(':idP', $idP, PDO::PARAM_STR); 
                                    }

                                    if($date_consultation == null) {
                                        $reqPatchUneConsultation->bindParam(':date_consultation', $valeurObjetCourant['date_consultation'], PDO::PARAM_STR); 
                                    } else {
                                        $reqPatchUneConsultation->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR); 
                                    }
                            
                                    if($heure_debut == null) {
                                        $reqPatchUneConsultation->bindParam(':heure_debut', $valeurObjetCourant['heure_debut'], PDO::PARAM_STR); 
                                    } else {
                                        $reqPatchUneConsultation->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR); 
                                    }
                                    
                                    if($duree == null) {
                                        $reqPatchUneConsultation->bindParam(':duree', $valeurObjetCourant['duree'], PDO::PARAM_STR); 
                                    } else {
                                        $reqPatchUneConsultation->bindParam(':duree', $duree, PDO::PARAM_STR); 
                                    }
                            
                                    $reqPatchUneConsultation->bindParam(':idC', $id, PDO::PARAM_STR); 
                            
                                    $reqPatchUneConsultation->execute();
                            
                                    $errorInfo = $reqPatchUneConsultation->errorInfo();
                            
                                    if ($errorInfo[0] != '00000') {
                                        $response['statusCode'] = 400;
                                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                                    } else {
                                        $msgErreur = "La consultation a bien été modifiée"; // Stockage du message dans le tableau de réponse
                                        $response['statusCode'] = 200; // Status code
                                        $response['statusMessage'] = "La requête a réussi, la consultation à bien été modifiée";
                                        $response['data'] = null; // Stockage du message dans le tableau de réponse
                                    }
                            
                                }
                        }
                    }
        }
            return $response; // Retour du tableau de réponse
    }


    function deleteConsultation($linkpdo, $id){
        $response = array();

        $reqDeleteConsultation = $linkpdo->prepare('DELETE FROM consultation WHERE idC = :idC');

        if ($reqDeleteConsultation == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";

        } else{

            $reqDeleteConsultation->bindParam(':idC', $id, PDO::PARAM_STR);

            $reqDeleteConsultation->execute();

            if($reqDeleteConsultation == false){
                $msgErreur = "Erreur dans l'exécution de la requête de suppression : ";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
                $response['data'] = null; // Stockage du message dans le tableau de réponse
            } else {
                
                $data = $reqDeleteConsultation->fetchAll(PDO::FETCH_ASSOC);

                $msgErreur = "La consultation a été supprimée avec succès !";
                $response['statusCode'] = 200; 
                $response['statusMessage'] = "La requête a réussi, consultation supprimée avec succès ";
                $response['data'] = null; // Stockage du message dans le tableau de réponse

            }
        }
        return $response;
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
