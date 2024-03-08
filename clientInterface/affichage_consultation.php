<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Les Consultations</title>
    <link href="style/tableau.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
    <script src="js/selectionDeLigne.js"></script>
    <link rel="icon" href="image/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php
     require('module/header.php');
     require('module/bd_connexion.php');
     ?>

    <!--Espace vide pour permettre de placer le header en haut de page-->
    <div class="vide-haut-page"> </div>

    <?php

    if (isset($_POST['filtrer_tableau']) && $_POST['idM'] != "Tous") {

    ?>
            <div class="button-sur-tableau">
                <form method="post" action="">
                    <select class="select-filtre" name="idM" id="medecin-select" required>
                        <option value="Tous"> Tous </option>
                        <?php
                        $reqMedecins = $linkpdo->prepare('SELECT idM, civilite, nom, prenom FROM medecin');
                        if ($reqMedecins == false) {
                            echo "Erreur dans la préparation de la requête d'affichage.";
                        } else {
                            $reqMedecins->execute();
                            if ($reqMedecins == false) {
                                echo "Erreur dans l'exécution de la requête d'affichage.";
                            } else {
                                while ($medecin = $reqMedecins->fetch(PDO::FETCH_ASSOC)) {
                                    $idMedecin = $medecin['idM'];
                                    $civiliteMedecin = $medecin['civilite'];
                                    $nomMedecin = $medecin['nom'];
                                    $prenomMedecin = $medecin['prenom'];

                                    echo "<option value=\"$idMedecin\">$civiliteMedecin $nomMedecin $prenomMedecin</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                    <input type="submit" name="filtrer_tableau" value="Filtrer par médecin" class="input-filtrer">
                </form> 
            </div>

            <div class="containerExterieur">
            <div class="containerTab">
            <table class="tableau">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure de début</th>
                    <th>Durée</th>
                    <th>Heure de fin</th>
                    <th>Patient</th>
                    <th>Medecin</th>
                    <!-- Colonnes invisibles pour recuperer l'id du patient et du medecin -->
                    <th style=display:none >IdPatientRef</th>
                    <th style=display:none >IdMedecinRef</th>
                    <th style=display:none >Date_BD</th>
                </tr>
                </thead>
                <tbody>
                        <?php
                        // Préparation de la requête de recherche des patients
                        $reqAffichage = $linkpdo->prepare('SELECT idM, date_consultation, heure_debut, duree, idP FROM consultation WHERE idM = :idM ORDER BY date_consultation DESC');

                        if ($reqAffichage == false) {
                            echo "Erreur dans la préparation de la requête d'affichage.";
                        } else {
                            $reqAffichage->bindParam(':idM', $_POST['idM']);
                            // Exécution de la requête
                            $reqAffichage->execute();

                            if ($reqAffichage == false) {
                                echo "Erreur dans l'exécution de la requête d'affichage.";
                            } else {
                                // Récupération des résultats et affichage dans le tableau
                                while ($consultation = $reqAffichage->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr onclick=\"selectionnerLigne(this)\">"; // Appel de la fonction js de selection de ligne
                                    $dateConsultation = new DateTime($consultation['date_consultation']);
                                    echo "<td>{$dateConsultation->format('d/m/Y')}</td>"; // Format jj/mm/aaaa

                                    echo "<td>{$consultation['heure_debut']}</td>";
                                    echo "<td>{$consultation['duree']}</td>";

                                    // Calcul de l'heure de fin
                                    $heureDebut = new DateTime($consultation['heure_debut']);

                                    // Méthode pour créer une intervalle de temps pour obtenir la duree, pcq il y a un problème avec DateInterval
                                    $duree_datetime = new DateTime($consultation['duree']);
                                    // Création de l'intervalle, donc la durée, avec un temps null 00:00:00 pour obtenir la valeur de la durée mais en type Interval 
                                        // On a : A ->diff(B); cela renvoit B - A
                                    $duree = (new DateTime('00:00:00'))->diff($duree_datetime);
                                    $heureFin = $heureDebut->add($duree);

                                    // Affichage de l'heure de fin
                                    echo "<td>{$heureFin->format('H:i:s')}</td>";

                                        // Récupération du nom du patient
                                    $reqPatient = $linkpdo->prepare('SELECT nom, prenom FROM patient WHERE idP = :idP');
                                    if ($reqPatient == false) {
                                        echo "Erreur dans la préparation de la requête du patient.";
                                    } else {
                                        $reqPatient->bindParam(':idP', $consultation['idP']);
                                        $reqPatient->execute();
                                        if ($reqPatient == false) {
                                            echo "Erreur dans l'exécution de la requête du patient.";
                                        } else {
                                            $patient = $reqPatient->fetch(PDO::FETCH_ASSOC);

                                            // Récupération du nom du médecin
                                            $reqMedecin = $linkpdo->prepare('SELECT nom, prenom FROM medecin WHERE idM = :idM');
                                            if ($reqMedecin == false) {
                                                echo "Erreur dans la préparation de la requête du medecin.";
                                            } else {
                                            $reqMedecin->bindParam(':idM', $consultation['idM']);
                                            $reqMedecin->execute();
                                            if ($reqMedecin == false) {
                                                echo "Erreur dans l'exécution de la requête du medecin.";
                                            } else {
                                                $medecin = $reqMedecin->fetch(PDO::FETCH_ASSOC);

                                                echo "<td>{$patient['nom']} {$patient['prenom']}</td>";
                                                echo "<td>{$medecin['nom']} {$medecin['prenom']}</td>";
                                                // Colonnes invisibles pour récuperer l'id du patient, l'id du medecin et la date dans le format de la BD
                                                echo "<td style=display:none >{$consultation['idP']}</td>";
                                                echo "<td style=display:none >{$consultation['idM']}</td>";
                                                echo "<td style=display:none >{$consultation['date_consultation']}</td>";

                                                echo "</tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }         
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php

    //Fin de la condition du dessus 
    } else if ( !(isset($_POST['filtrer_tableau'])) || $_POST['idM'] == "Tous") {
    ?>

    <div class="button-sur-tableau">
        <form method="post" action="">
            <select class="select-filtre" name="idM" id="medecin-select" required>
                <option value="Tous"> Tous </option>
                <?php
                $reqMedecins = $linkpdo->prepare('SELECT idM, civilite, nom, prenom FROM medecin');
                if ($reqMedecins == false) {
                    echo "Erreur dans la préparation de la requête d'affichage.";
                } else {
                    $reqMedecins->execute();
                    if ($reqMedecins == false) {
                        echo "Erreur dans l'exécution de la requête d'affichage.";
                    } else {
                        while ($medecin = $reqMedecins->fetch(PDO::FETCH_ASSOC)) {
                            $idMedecin = $medecin['idM'];
                            $civiliteMedecin = $medecin['civilite'];
                            $nomMedecin = $medecin['nom'];
                            $prenomMedecin = $medecin['prenom'];

                            echo "<option value=\"$idMedecin\">$civiliteMedecin $nomMedecin $prenomMedecin</option>";
                        }
                    }
                }
                ?>
            </select>
            <input type="submit" name="filtrer_tableau" value="Filtrer par médecin" class="input-filtrer">
        </form> 
    </div>

    <div class="containerExterieur">
    <div class="containerTab">
    <table class="tableau">
        <thead>
        <tr>
            <th>Date</th>
            <th>Heure de début</th>
            <th>Durée</th>
            <th>Heure de fin</th>
            <th>Patient</th>
            <th>Medecin</th>
            <!-- Colonnes invisibles pour recuperer l'id du patient et du medecin -->
            <th style=display:none >IdPatientRef</th>
            <th style=display:none >IdMedecinRef</th>
            <th style=display:none >Date_BD</th>


        </tr>
        </thead>
        <tbody>
             <?php
                // Préparation de la requête de recherche des patients
                $reqAffichage = $linkpdo->prepare('SELECT idM, date_consultation, heure_debut, duree, idP FROM consultation ORDER BY date_consultation DESC');

                if ($reqAffichage == false) {
                    echo "Erreur dans la préparation de la requête d'affichage.";
                } else {
                    // Exécution de la requête
                    $reqAffichage->execute();

                    if ($reqAffichage == false) {
                        echo "Erreur dans l'exécution de la requête d'affichage.";
                    } else {
                        // Récupération des résultats et affichage dans le tableau
                        while ($consultation = $reqAffichage->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr onclick=\"selectionnerLigne(this)\">"; // Appel de la fonction js de selection de ligne
                            $dateConsultation = new DateTime($consultation['date_consultation']);
                            echo "<td>{$dateConsultation->format('d/m/Y')}</td>"; // Format jj/mm/aaaa
                            
                            echo "<td>{$consultation['heure_debut']}</td>";
                            echo "<td>{$consultation['duree']}</td>";

                            // Calcul de l'heure de fin
                            $heureDebut = new DateTime($consultation['heure_debut']);

                            // Méthode pour créer une intervalle de temps pour obtenir la duree, pcq il y a un problème avec DateInterval
                            $duree_datetime = new DateTime($consultation['duree']);
                            // Création de l'intervalle, donc la durée, avec un temps null 00:00:00 pour obtenir la valeur de la durée mais en type Interval 
                             // On a : A ->diff(B); cela renvoit B - A
                            $duree = (new DateTime('00:00:00'))->diff($duree_datetime);
                            $heureFin = $heureDebut->add($duree);

                            // Affichage de l'heure de fin
                            echo "<td>{$heureFin->format('H:i:s')}</td>";

                             // Récupération du nom du patient
                            $reqPatient = $linkpdo->prepare('SELECT nom, prenom FROM patient WHERE idP = :idP');
                            if ($reqPatient == false) {
                                echo "Erreur dans la préparation de la requête du patient.";
                            } else {
                                $reqPatient->bindParam(':idP', $consultation['idP']);
                                $reqPatient->execute();
                                if ($reqPatient == false) {
                                    echo "Erreur dans l'exécution de la requête du patient.";
                                } else {
                                    $patient = $reqPatient->fetch(PDO::FETCH_ASSOC);

                                    // Récupération du nom du médecin
                                    $reqMedecin = $linkpdo->prepare('SELECT nom, prenom FROM medecin WHERE idM = :idM');
                                    if ($reqMedecin == false) {
                                        echo "Erreur dans la préparation de la requête du medecin.";
                                    } else {
                                    $reqMedecin->bindParam(':idM', $consultation['idM']);
                                    $reqMedecin->execute();
                                    if ($reqMedecin == false) {
                                        echo "Erreur dans l'exécution de la requête du medecin.";
                                    } else {
                                        $medecin = $reqMedecin->fetch(PDO::FETCH_ASSOC);

                                        echo "<td>{$patient['nom']} {$patient['prenom']}</td>";
                                        echo "<td>{$medecin['nom']} {$medecin['prenom']}</td>";
                                        // Colonnes invisibles pour récuperer l'id du patient et l'id du medecin
                                        echo "<td style=display:none >{$consultation['idP']}</td>";
                                        echo "<td style=display:none >{$consultation['idM']}</td>";
                                        echo "<td style=display:none >{$consultation['date_consultation']}</td>";

                                        echo "</tr>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }         
            ?>
         </tbody>
      </table>
    </div>
    </div>

    <?php
    //Fin de la condition du dessus 
    } 
    ?>

    <div class="button-sous-tableau">
            <a href="ajout_consultation.php"><button class="button-ajout">Ajouter une consultation</button></a>
            <a class="lien-modif-supp" id="boutonModification" onclick="envoyerVersPageModificationConsultation()" disabled >Modifier une consultation</a>
            <a class="lien-modif-supp" id="boutonSuppression" onclick="envoyerVersPageSuppressionConsultation()" disabled >Supprimer une consultation</a>
            
    </div>
</body>
</html>

<style>
.tableau {
    border-collapse: collapse;
    background-color: white;
    width: 1100px /* Pour ne pas avoir la scrollbar en bas*/
}
</style>