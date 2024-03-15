<?php
	/*Ce fichier contiendra toutes les définitions des fonctions de manipulations des données en SQL.*/

    require('auth_API/jwt_utils.php');

    function getAllConsultations($linkpdo){
        $response = array(); // Initialisation du tableau de la réponse

        $reqAllConsulations = $linkpdo->prepare('SELECT idM, date_consultation, heure_debut, duree, idP FROM consultation ORDER BY date_consultation DESC');

        if($reqAllConsulations == false){
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqAllConsulations->execute();

            if($reqAllConsulations == false){
                echo "Erreur dans l'execution de la requête d'affichage.";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
            } else {
                $Consultations = $reqAllConsulations->fetchAll(PDO::FETCH_ASSOC);

                $response['statusCode'] = 200;
                $response['statusMessage'] = "Affichage de toutes les consultations"
                $response['data'] = $data;
            }
        }
        return $response;
    }


    function getConsultation($linkpdo, $idConsultation){
        $response = array(); // Initialisation du tableau de la réponse

        $reqAllConsulations = $linkpdo->prepare('SELECT idM, date_consultation, heure_debut, duree, idP FROM consultation WHERE idC = :idC ORDER BY date_consultation DESC');

        if($reqAllConsulations == false){
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqAllConsulations->execute();

            if($reqAllConsulations == false){
                echo "Erreur dans l'execution de la requête d'affichage.";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
            } else {
                $Consultations = $reqAllConsulations->fetchAll(PDO::FETCH_ASSOC);

                $response['statusCode'] = 200;
                $response['statusMessage'] = "Affichage de la consultation"
                $response['data'] = $data;
            }
        }
        return $response;
    }

    function addConsultation($linkpdo, $date_consultation, $heure_debut, $duree){
        $response = array(); // Initialisation du tableau de la réponse

        //Vérifier si la consultation n'existe pas déja
        $reqConsultExistDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree');
        if($reqConsultExistDeja == false){
            echo "Erreur dans l'execution de la requête.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";  
        } else {
            $reqConsultExistDeja->bindParam(':date_consultation',$date_consultation, PDO::PARAM_STR);
            $reqConsultExistDeja->bindParam(':heure_debut',$heure_debut, PDO::PARAM8STR);
            $reqConsultExistDeja->bindParam(':duree',$duree, PDO::PARAM_STR);

            $reqConsultExistDeja->execute();
            if ($reqConsultExistDeja == false){
                die("Erreur dans l'exécution de la requête de test de présence d'une consultation.");
            } else {
                $nbConsultations = $reqConsultExistDeja->fetchColumn();
                if ($nbConsultations > 0){
                    echo "Erreur dans l'execution de la requête de création d'une consultation.";
                    $response['statusCode'] = 409;
                    $response['statusMessage'] = "Existe déjà";
                } else {
                    $reqAddConsultation = $linkpdo->prepare('INSERT INTO consulation (date_consultation, heure_debut, duree) VALUES (:date_consultation, :heure_debut, :duree');

                    if($reqAddConsultation == false){
                        echo "Erreur dans la préparation de la requête de création d'une consultation.";
                        $response['statusCode'] = 400;
                        $response['statusMessage'] = "Syntaxe de la requête non conforme";
                    } else {
                        $reqAddConsultation->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
                        $reqAddConsultation->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                        $reqAddConsultation->bindParam(':duree', $duree, PDO::PARAM_STR);

                        $reqAddConsultation->execute();
                        if ($reqAddConsultation == false){
                            echo "Erreur dans l'execution de la requête de création d'une consultation.";
                            $response['statusCode'] = 400;
                            $response['statusMessage'] = "Syntaxe de la requête non conforme";
                        } else {
                            $response['statusCode'] = 200; 
                            $response['statusMessage'] = "La requête a réussi";
                            $response['data'] = $data; 
                        }
                    }
                }
            }
            return $response;
        }
    }


    function patchConsultation($linkpdo, $idC, $date_consultation, $heure_debut, $duree){
        $response = array();

        $reqMajConsultation = $linkpdo->prepare('UPDATE consultations SET date_consultation = :date_consultation, heure_debut = :heure_debut, duree = :duree WHERE idC = :idC');
        if($reqMajConsultation == false){
            echo "Erreur dans la préparation de la requête de modification d'une consultation.";
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête non conforme";
        } else {
            $reqMajConsultation->bindParam(':idC', $idC, PDO::PARAM_STR);
            $reqMajConsultation->bindParam(':date_consultation', $date_consultation, PDO::PARAM_STR);
            $reqMajConsultation->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
            $reqMajConsultation->bindParam(':duree', $duree, PDO::PARAM_STR);

            $reqMajConsultation->execute();
            if($reqMajConsultation == false){
                echo "Erreur dans l'execution de la requête de modification d'une consultation.";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
            } else {
                $response['statusCode'] = 200; 
                $response['statusMessage'] = "La requête a réussi";
            }
        }
        return $response;
    }


    function deleteConsultation($linkpdo, $idC){
        $response = array();

        $reqDeleteConsultation = $linkpdo->prepare('DELETE FROM consultation WHERE idC = :idC');
        if ($reqDeleteConsultation == false){
            echo "Erreur dans la préparation de la requête de suppression d'une consultation.";
        } else{
            $reqDeleteConsultation->binParam(':idC', $idC, PDO::PARAM_STR);
            $reqDeleteConsultation->execute();
            if($reqDeleteConsultation == false){
                echo "Erreur dans l'execution de la requête de suppression d'une consultation";
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête non conforme";
            } else {
                $data = $reqDeleteConsultation->fetchAll(PDO::FETCH_ASSOC);

                $response['statusCode'] = 200; 
                $response['statusMessage'] = "La requête a réussie, suppression effectuée";
            }
        }
        return $response;
    }

?>
