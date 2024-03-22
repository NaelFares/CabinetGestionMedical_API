<?php 

    require('../Modules/connexion_db.php');
    require('functions_stats.php');
    
    // Récupérer les statistiques sur les usagers
    echo ($statsUsagersMoins25 = getStatisticsUsagersMoins25ParGenre($linkpdo));
    echo ($statsUsagersEntre25et50 = getStatisticsUsagersEntre25et50ParGenre($linkpdo));
    echo ($statsUsagersPlus50 = getStatisticsUsagersPlus50ParGenre($linkpdo));


    // Récupérer les statistiques sur la durée totale des consultations par médecin
    $statsNbHeuresConsultParMedecin = getStatisticsNbHeuresConsultParMedecin($linkpdo);
?>