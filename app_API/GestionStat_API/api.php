<?php 

require('../Modules/connexion_db.php');
require('../Modules/fonctions.php');
require('functions_stats.php');

if(demande_validation()) {
    
    $http_method = $_SERVER['REQUEST_METHOD'];
    if ($http_method !== "GET"){
        deliver_response(405, "La méthode HTTP n'est pas autorisée pour cette ressource.", true);
    }

    $stat = $_GET["stat"];
    if(empty($stat)) {
        deliver_response(400, "Il faut préciser la statistique voulue.", true);
    }

    switch ($stat) {
        case "medecins" : 
            // Récupérer les statistiques sur la durée totale des consultations par médecin
            $statsNbHeuresConsultParMedecin = getStatisticsNbHeuresConsultParMedecin($linkpdo);

            if($statsNbHeuresConsultParMedecin !== false){
                deliver_response(200, "Statistiques sur la durée totale des consultations par médecin récupérées avec succès", $statsNbHeuresConsultParMedecin);
            } else {
                deliver_response(500, "Erreur lors de la récupération des statistiques médecins", null);
            }
            break;

        case "usagers" : 
            // Récupérer les statistiques sur les usagers
            $statsUsagersMoins25 = getStatisticsUsagersMoins25ParGenre($linkpdo);
            $statsUsagersEntre25et50 = getStatisticsUsagersEntre25et50ParGenre($linkpdo);
            $statsUsagersPlus50 = getStatisticsUsagersPlus50ParGenre($linkpdo);

            if($statsUsagersMoins25 !== false && $statsUsagersEntre25et50 !== false && $statsUsagersPlus50 !== false){
                $statsUsagers = array(
                    "usagers_moins_25" => $statsUsagersMoins25,
                    "usagers_25_50" => $statsUsagersEntre25et50,
                    "usagers_plus_50" => $statsUsagersPlus50
                );
                deliver_response(200, "Statistiques sur les usagers récupérées avec succès", $statsUsagers);
            } else {
                deliver_response(500, "Erreur lors de la récupération des statistiques usagers", null);
            }
            break;
            
        default:
            deliver_response(400, "La statistique '".$stat."' n'existe pas.", true);
    }    

}

?>