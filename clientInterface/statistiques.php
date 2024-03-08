<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Les Statistiques</title>
    <link href="style/statistiques.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
    <link rel="icon" href="image/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php
     require('module/header.php');
     require('module/bd_connexion.php');
     ?>

    <?php
        // Traitement pour les hommes ayant moins de 25 ans
        $reqNbHommeMoins25 = $linkpdo->prepare("SELECT count(*) FROM patient WHERE civilite = 'M.' AND TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 25 ;");
        if ($reqNbHommeMoins25 == false) {
            echo "Erreur dans la préparation de la requête d'affichage.";
        } else {
            $reqNbHommeMoins25->execute();                   
            if ($reqNbHommeMoins25 == false) {
                echo "Erreur dans l'exécution de la requête d'affichage.";
            } else {
                $nbHommeMoins25 = $reqNbHommeMoins25->fetchColumn();
            }
        }
            
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
        
    ?>

    <!--Espace vide pour permettre de placer le header en haut de page-->
    <div class="vide-haut-page"> </div>
    <div class="containerExterieur">
        <div class="containerTab">
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Tranche d'âge</th>
                        <th>Nombre d'Hommes</th>
                        <th>Nombre de Femmes</th>                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="titre-stats"> Moins de 25 ans</th>
                        <td> <?php echo $nbHommeMoins25;?> </td>
                        <td> <?php echo $nbFemmeMoins25;?> </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <th class="titre-stats">Entre 25 et 50 ans</th>
                        <td> <?php echo $NbHommeEntre25_50; ?> </td>
                        <td> <?php echo $NbFemmeEntre25_50; ?> </td>                            
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <th class="titre-stats">Plus de 50 ans</th>
                        <td> <?php echo $nbHommePlus50; ?> </td>
                        <td> <?php echo $nbFemmePlus50; ?> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="containerExterieur">
        <div class="containerTab">
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Medecin</th>
                        <th>Durée totale des consultations (en heures)</th>                    
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Préparation de la requête de recherche des médecins avec la durée totale des consultations
                    $reqAffichage = $linkpdo->prepare('
                        SELECT
                            m.civilite,
                            m.nom,
                            m.prenom,
                            SEC_TO_TIME(SUM(TIME_TO_SEC(c.duree))) AS duree_totale
                        FROM
                            medecin m
                            LEFT JOIN consultation c ON m.idM = c.idM
                        GROUP BY
                            m.idM
                    ');

                    if ($reqAffichage == false) {
                        echo "Erreur dans la préparation de la requête de recherche.";
                    } else {
                        // Exécution de la requête
                        $reqAffichage->execute();

                        if ($reqAffichage == false) {
                            echo "Erreur dans l'exécution de la requête d'affichage.";
                        } else {
                            // Récupération des résultats et affichage dans le tableau
                            while ($medecin = $reqAffichage->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>"; // Appel de la fonction js de sélection de ligne
                                $infoMedecin = $medecin['civilite'] . " " . $medecin['nom'] . " " . $medecin['prenom'];
                                echo "<td>{$infoMedecin}</td>";
                                if ($medecin['duree_totale'] == null){
                                    echo "<td>Aucune consultation</td>";
                                } else {
                                $heure = $medecin['duree_totale'];
                                $heureFormatee = DateTime::createFromFormat('H:i:s', $heure)->format('H\hi');

                                echo "<td>{$heureFormatee}</td>";
                                }
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>        
        </div>
    </div>
        
    </body>
</html>

<style>
.tableau {
    border-collapse: collapse;
    background-color: white;
    width: 900px /* Pour ne pas avoir la scrollbar en bas*/
}
</style>

    
</body>
</html>
