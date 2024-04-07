<?php 
//Récupère et retourne les données de statistiques//


////// Usagers

    // Usagers ayant moins de 25 ans par genre
    function getStatisticsUsagersMoins25ParGenre($linkpdo){
        $reqNbHMoins25 = $linkpdo->prepare("SELECT COUNT(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 25");  
        $reqNbFMoins25 = $linkpdo->prepare("SELECT COUNT(*) FROM patient WHERE civilite = 'Mme' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 25");

        if($reqNbHMoins25 == false || $reqNbFMoins25 == false){
            die ("Erreur dans la préparation des requêtes.");
        } 
        
        $reqNbHMoins25->execute();
        $reqNbFMoins25->execute();

        if($reqNbHMoins25 == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (H,-25 ans) non conforme";
        } else if($reqNbFMoins25 == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (F,-25 ans) non conforme";
        } else {
            $nbHommesMoins25 = $reqNbHMoins25->fetchColumn();
            $nbFemmesMoins25 = $reqNbFMoins25->fetchColumn();
        }
        
        $stats_NbUsagersMoins25 = array(
            'homme' => $nbHommesMoins25,
            'femme' => $nbFemmesMoins25
        );
        
        return $stats_NbUsagersMoins25;
    }


    // Usagers ayant entre 25 et 50 ans par genre
    function getStatisticsUsagersEntre25et50ParGenre($linkpdo){
        $reqNbH25_50 = $linkpdo->prepare("SELECT COUNT(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 25 AND 50");  
        $reqNbF25_50 = $linkpdo->prepare("SELECT COUNT(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 25 AND 50");  
        
        if($reqNbF25_50 == false || $reqNbH25_50 == false){
            die ("Erreur dans la préparation des requêtes.");
        } 

        $reqNbH25_50->execute();
        $reqNbF25_50->execute();

        if($reqNbH25_50 == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (H, 25-50 ans) non conforme";
        } else if ($reqNbF25_50 == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (F, 25-50 ans) non conforme";
        } else {
            $nbHommes25_50 = $reqNbH25_50->fetchColumn();
            $nbFemmes25_50 = $reqNbF25_50->fetchColumn();
        }
        
        $stats_NbUsagers25_50 = array(
            'homme' => $nbHommes25_50,
            'femme' => $nbFemmes25_50
        );
        
        return $stats_NbUsagers25_50;
    }


    // Usagers ayant plus de 50 ans par genre
    function getStatisticsUsagersPlus50ParGenre($linkpdo){
        $reqNbHplus50 = $linkpdo->prepare("SELECT COUNT(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 50");  
        $reqNbFplus50 = $linkpdo->prepare("SELECT COUNT(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 50");  

        if($reqNbHplus50 == false || $reqNbFplus50 == false){
            die ("Erreur dans la préparation des requêtes.");
        } 

        $reqNbHplus50->execute();
        $reqNbFplus50->execute();

        if($reqNbHplus50 == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (H, +50 ans) non conforme";
        } else if($reqNbFplus50 == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (F, +50 ans) non conforme";
        } else {
            $nbHplus50 = $reqNbHplus50->fetchColumn();
            $nbFplus50 = $reqNbFplus50->fetchColumn();
        }
        
        $stats_NbUsagersPlus50 = array(
            'homme' => $nbHplus50,
            'femme' => $nbFplus50
        );
        
        return $stats_NbUsagersPlus50;
    }



////// Durée totale des consultations effectuées par chaque médecin (nbHeures)
    function getStatisticsNbHeuresConsultParMedecin($linkpdo){
        $reqNbHeuresConsultParMedecin = $linkpdo->prepare('
                        SELECT
                            c.idM,
                            m.civilite,
                            m.nom,
                            m.prenom,
                            SEC_TO_TIME(SUM(TIME_TO_SEC(c.duree))) AS total_heures
                        FROM
                            medecin m
                            LEFT JOIN consultation c ON m.idM = c.idM
                        GROUP BY
                            m.idM
                    ');

        if($reqNbHeuresConsultParMedecin == false){
            die ("Erreur dans la préparation des requêtes.");
        }

        $reqNbHeuresConsultParMedecin->execute();
        if($reqNbHeuresConsultParMedecin == false){
            $response['statusCode'] = 400;
            $response['statusMessage'] = "Syntaxe de la requête (stat nbHeures/medecin) non conforme";
        } else {
            $resultArray = $reqNbHeuresConsultParMedecin->fetchAll(PDO::FETCH_ASSOC);
        
            $stats_NbHeuresConsultParMedecin = array();
            foreach ($resultArray as $row){
                $idMedecin = $row['idM'];
                $nomMedecin = $row['nom'];
                $prenomMedecin = $row ['prenom'];
                $heure = $row['total_heures'];

                // Vérifier si la valeur de la durée est NULL
                if ($heure !== null) {
                    $heureFormatee = DateTime::createFromFormat('H:i:s', $heure)->format('H\hi');
                } else {
                    $heureFormatee = 'N/A'; // Ou toute autre valeur par défaut que vous souhaitez
                }
                //$heureFormatee = DateTime::createFromFormat('H:i:s', $heure)->format('H\hi');
                $stats_NbHeuresConsultParMedecin[$idMedecin] = array(
                    'nom' => $nomMedecin,
                    'prenom' => $prenomMedecin,
                    'total_heures' => $heureFormatee
                );
            }
        }
    return $stats_NbHeuresConsultParMedecin;
}


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