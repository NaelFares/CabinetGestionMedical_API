<?php
	/*Ce fichier contiendra toutes les définitions des fonctions de manipulations des données en SQL.*/

    require('auth_API/jwt_utils.php');

    function getAllConsultations($linkpdo){

        $response = array(); // Initialisation du tableau de la réponse

        $reqAllConsultations = $linkpdo->prepare('SELECT idM, date_consultation, heure_debut, duree, idP FROM consultation ORDER BY date_consultation DESC');

        if($reqAllConsultations == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans l'execution de la requête d'affichage.";        } else {
        } else {
            $reqAllConsulations->execute();

            if($reqAllConsultations == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Erreur dans l'execution de la requête d'affichage.";
            } else {
                $data = $reqAllConsultations->fetchAll(PDO::FETCH_ASSOC);

                $response['statusCode'] = 200;
                $response['statusMessage'] = "Affichage de toutes les consultations"
                $response['data'] = $data;
            }
        }
        return $response;
    }


    function getConsultationById($linkpdo, $id) {
        $response = array(); // Initialisation du tableau de la réponse

        $reqConsultationParId = $linkpdo->prepare('SELECT idM, date_consultation, heure_debut, duree, idP FROM consultation WHERE idC = :idC ORDER BY date_consultation DESC');

        if($reqConsultationParId == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans la préparation de la requête d'affichage d'une seule consultation.";
        } else {
            $reqConsultationParId->bindParam(":idC", $id, PDO::PARAM_STR);

            $reqConsultationParId->execute();

            if($reqConsultationParId == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Erreur dans l'execution de la requête d'affichage";
            } else {
                // On récupère toutes les consultations
                $data = $reqConsultationParId->fetchAll(PDO::FETCH_ASSOC);

                $response['statusCode'] = 200;
                $response['statusMessage'] = "La requête a réussie";
                $response['data'] = $data;
            }
        }
        return $response;
    }

    function createConsultation($linkpdo, $date_consultation, $heure_debut, $duree){
        $response = array(); // Initialisation du tableau de la réponse

        //Vérifier si la consultation n'existe pas déja
        $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree');

        if($reqExisteDeja == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans la préparation de la requête.";  
        } else {

            $reqExisteDeja->bindParam(':date_consultation',$date_consultation, PDO::PARAM_STR);
            $reqExisteDeja->bindParam(':heure_debut',$heure_debut, PDO::PARAM8STR);
            $reqExisteDeja->bindParam(':duree',$duree, PDO::PARAM_STR);

            $reqExisteDeja->execute();

            if ($reqExisteDeja == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Erreur dans l'execution de la requête d'avant-création d'une consultation.";
            } else {

                $nbConsultations = $reqConsultExistDeja->fetchColumn();

                if ($nbConsultations > 0){
                    $response['statusCode'] = 409;
                    $response['statusMessage'] = "Existe déjà";
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
                            $reqChevauchementMedecin->bindParam(':date_consultation', $_POST['date_consultation'], PDO::PARAM_STR);
                            $reqChevauchementMedecin->bindParam(':heure_debut', $_POST['heure_debut'], PDO::PARAM_STR);
                            $reqChevauchementMedecin->bindParam(':duree', $_POST['duree'], PDO::PARAM_STR);
                            $reqChevauchementMedecin->bindParam(':idM', $_POST['idM'], PDO::PARAM_STR);

                            // Liaison des paramètres pour la requête chevauchement patient
                            $reqChevauchementPatient->bindParam(':date_consultation', $_POST['date_consultation'], PDO::PARAM_STR);
                            $reqChevauchementPatient->bindParam(':heure_debut', $_POST['heure_debut'], PDO::PARAM_STR);
                            $reqChevauchementPatient->bindParam(':duree', $_POST['duree'], PDO::PARAM_STR);
                            $reqChevauchementPatient->bindParam(':idP', $_POST['idP'], PDO::PARAM_STR);

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
                                    $msgErreur = "Créneau déjà réservé.";
                                } else {

                                    $reqCreateConsultation = $linkpdo->prepare('INSERT INTO consulation (date_consultation, heure_debut, duree) VALUES (:date_consultation, :heure_debut, :duree');

                                    if($reqCreateConsultation == false){
                                        $response['statusCode'] = 400;
                                        $response['statusMessage'] = "Erreur dans la préparation de la requête de création d'une consultation.";
                                    } else {

                                        $reqCreateConsultation->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
                                        $reqCreateConsultation->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                                        $reqCreateConsultation->bindParam(':duree', $duree, PDO::PARAM_STR);

                                        $reqCreateConsultation->execute();

                                        if ($reqCreateConsultation == false){
                                            $response['statusCode'] = 400;
                                            $response['statusMessage'] = "Erreur dans l'execution de la requête de création d'une consultation.";
                                        } else {

                                            $response['statusCode'] = 200; 
                                            $response['statusMessage'] = "La requête a réussie";
                                            $response['data'] = $date_consultation ." ". $heure_debut ." ". $duree; 
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


    function patchConsultation($linkpdo, $id, $date_consultation=null, $heure_debut=null, $duree=null) {
        $response = array();

        $reqRecupConsultation = $linkpdo->prepare('SELECT * FROM consultation where idC = :idC');

        if ($reqRecupConsultation == false) {
            $response['statusCode'] = 500;
            $response['statusMessage'] = "Erreur dans la préparation de la requête de récuperation d'une consultation: ";
            return $response;
        } else { 
            $reqRecupConsultation->bindParam(':idC', $id, PDO::PARAM_STR); 
            $reqRecupConsultation->execute();
        }
    
        $valeurObjetCourant = $reqRecupConsultation->fetch();
    
        $reqPatchUneConsultation = $linkpdo->prepare('UPDATE consultation SET date_consultation = :date_consultation, heure_debut = :heure_debut, duree = :duree WHERE idC = :idC');
    
        if ($reqPatchUneConsultation == false) {
            $response['statusCode'] = 500;
            $response['statusMessage'] = "Erreur dans la préparation de la requête de modification partielle d'une consultation: ";
            return $response;
        } else {
    
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
                echo "Erreur dans l'execution de la requête de modification d'un medecin.";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Erreur lors de l'exécution de la requête : " . $errorInfo[2];
            } else {
                $response['statusCode'] = 200; // Status code
                $response['statusMessage'] = "La requête a réussie, modification partielle effectuée";
            }
    
        }
    
        return $response; // Retour du tableau de réponse
    }


    function deleteConsultation($linkpdo, $id){
        $response = array();

        $reqDeleteConsultation = $linkpdo->prepare('DELETE FROM consultation WHERE idC = :idC');

        if ($reqDeleteConsultation == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Erreur dans la préparation de la requête de suppression d'une consultation.";

        } else{

            $reqDeleteConsultation->binParam(':idC', $id, PDO::PARAM_STR);

            $reqDeleteConsultation->execute();

            if($reqDeleteConsultation == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Erreur dans l'execution de la requête de suppression d'une consultation";
            } else {
                
                $data = $reqDeleteConsultation->fetchAll(PDO::FETCH_ASSOC);

                $response['statusCode'] = 200; 
                $response['statusMessage'] = "La requête a réussie, suppression effectuée";
            }
        }
        return $response;
    }

?>
