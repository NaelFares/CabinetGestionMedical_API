<?php 
//Récupère et retourne les données de statistiques//

    // Usagers ayant moins de 25 ans par genre
    function getStatisticsUsagersMoins25ParGenre(){
        $reqNbHMoins25 = $linkpdo->prepare('SELECT COUNT(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 25;');  
        $reqNbFMoins25 = $linkpdo->prepare('SELECT COUNT(*) FROM patient WHERE civilite = 'Mme' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 25;');

        if($reqNbHMoins25 == false || $reqNbFMoins25 == false){
            die "Erreur dans la préparation des requêtes.";
        } else {
            $reqNbHMoins25->execute();
            $reqNbFMoins25->execute();

            if($reqNbHMoins25 == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête (H,-25 ans) non conforme";
            } else if($reqNbFMoins25 == false){
                $response['statusCode'] = 400;
                $response['statusMessage'] = "Syntaxe de la requête (F,-25 ans) non conforme";
            } else {
                $data = array($homme = $reqNbHMoins25->fetchAll(PDO::FETCH_ASSOC), $femme = $reqNbFMoins25->fetchAll(PDO::FETCH_ASSOC));
            }

            
        }

        $stats_NbUsagersMoins25 = array(
            'homme' => $data[$homme],
            'femme' => $data[$femme]
        );
        
        return $stats_NbUsagersMoins25;
    }

    



    // Fonction pour récupérer les statistiques
    function getStatistics() {
        // Exemple de données de statistiques
        $stats_data = array(
            'total_users' => 1000,
            'active_users' => 800,
            'inactive_users' => 200
        );

        return $stats_data;
    }










?>



        
            
        // Traitement pour les femmes ayant moins de 25 ans
        $reqNbFemmeMoins25 = $linkpdo->prepare("SELECT count(*) FROM patient WHERE civilite = 'Mme' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 25 ;");
        if ($reqNbFemmeMoins25 == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
           $reqNbFemmeMoins25->execute();
            if ($reqNbFemmeMoins25 == false) {
                echo "Erreur dans l'exécution de la requête d'affichage.";
            } else {
                $nbFemmeMoins25 = $reqNbFemmeMoins25->fetchColumn();
            }
        }

        // Traitement pour les hommes ayant entre 25 et 50 ans
        $reqNbHommeEntre25_50 = $linkpdo->prepare("SELECT count(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 25 AND  50 ;");
        if ($reqNbHommeEntre25_50 == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqNbHommeEntre25_50->execute();
            if ($reqNbHommeEntre25_50 == false) {
                echo "Erreur dans l'exécution de la requête d'affichage.";
            } else {
                $NbHommeEntre25_50 = $reqNbHommeEntre25_50->fetchColumn();
            }
        }

        // Traitement pour les femmes ayant entre 25 et 50 ans
        $reqNbFemmeEntre25_50 = $linkpdo->prepare("SELECT count(*) FROM patient WHERE civilite = 'Mme' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 25 AND  50 ;");
        if ($reqNbFemmeEntre25_50 == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
           $reqNbFemmeEntre25_50->execute();    
            if ($reqNbFemmeEntre25_50 == false) {
                echo "Erreur dans l'exécution de la requête d'affichage.";
            } else {
                $NbFemmeEntre25_50 = $reqNbFemmeEntre25_50->fetchColumn();                           
            }
        }

        // Traitement pour les hommes ayant plus de 50 ans
        $reqNbHommeplus50 = $linkpdo->prepare("SELECT count(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 50 ;");
        if ($reqNbHommeplus50 == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqNbHommeplus50->execute(); 
            if ($reqNbHommeplus50 == false) {
                echo "Erreur dans l'exécution de la requête d'affichage.";
            } else {
                $nbHommePlus50 = $reqNbHommeplus50->fetchColumn();                             
            }
        }

        // Traitement pour les hommes ayant plus de 50 ans
        $reqNbFemmePlus50 = $linkpdo->prepare("SELECT count(*) FROM patient WHERE civilite = 'Mme' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 50 ;");
        if ($reqNbFemmePlus50 == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
           $reqNbFemmePlus50->execute(); 
            if ($reqNbFemmePlus50 == false) {
                echo "Erreur dans l'exécution de la requête d'affichage.";
            } else {
                $nbFemmePlus50 = $reqNbFemmePlus50->fetchColumn();
            }
        }








