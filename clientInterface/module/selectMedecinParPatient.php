<?php

require('bd_connexion.php');

    // Récupération de l'ID du patient depuis la requête AJAX
    $patientId = $_POST['patientId'];

    // Préparation de la requête pour récupérer les médecins associés au patient
    $reqMedecinRef = $linkpdo->prepare('SELECT m.idM, m.civilite, m.nom, m.prenom FROM medecin m, patient p WHERE m.idM = p.idM AND p.idP = :idP');

    if ($reqMedecinRef == false) {
        echo "Erreur dans la préparation de la requête d'affichage.";
    } else {
        $reqMedecinRef->bindParam(':idP', $patientId, PDO::PARAM_INT);
        $reqMedecinRef->execute();

        if ($reqMedecinRef == false) {
            echo "Erreur dans l'exécution de la requête d'affichage.";
        } else {
            while ($medecin = $reqMedecinRef->fetch(PDO::FETCH_ASSOC)) {
                $idMedecin = $medecin['idM'];
                $civiliteMedecin = $medecin['civilite'];
                $nomMedecin = $medecin['nom'];
                $prenomMedecin = $medecin['prenom'];

                echo "<option value=\"$idMedecin\">$civiliteMedecin $nomMedecin $prenomMedecin</option>";
            }
        }
    }

?>
