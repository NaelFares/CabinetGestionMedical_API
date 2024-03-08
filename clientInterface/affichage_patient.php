<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Les Patients</title>
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

        <div class="containerExterieur">
        <div class="containerTab">
            <table class="tableau">
                <thead>
                    <tr>
                        <th class="col-civilite">Civilité</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Adresse</th>
                        <th>Code Postal</th>
                        <th>Ville</th>
                        <!--On rend cette colonne invisible dans le site, elle sert uniquement à récupérer la date dans le format de la BD-->
                        <th style=display:none>Date BD</th>
                        <th>Date de naissance</th>
                        <th>Lieu de naissance</th>
                        <th>N° sécurité sociale</th>
                        <th>Médecin référent</th>
                        <!--On rend cette colonne invisible dans le site, elle sert uniquement à récupérer l'id du médecin référent dans la fonction js-->
                        <th style=display:none >IdMedecinRef</th>
                        <!--On rend cette colonne invisible dans le site, elle sert uniquement à récupérer l'id du patient dans la fonction js-->
                        <th style=display:none>IdPatient</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reqAffichage = $linkpdo->prepare('SELECT idP, civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM FROM patient');

                    if ($reqAffichage == false) {
                        echo "Erreur dans la préparation de la requête d'affichage.";
                    } else {
                        $reqAffichage->execute();

                        if ($reqAffichage == false) {
                            echo "Erreur dans l'exécution de la requête d'affichage.";
                        } else {
                            while ($patient = $reqAffichage->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr onclick=\"selectionnerLigne(this)\">";
                                echo "<td>{$patient['civilite']}</td>";
                                echo "<td>{$patient['nom']}</td>";
                                echo "<td>{$patient['prenom']}</td>";
                                echo "<td>{$patient['adresse']}</td>";
                                echo "<td>{$patient['cp']}</td>";
                                echo "<td>{$patient['ville']}</td>";
                                // c'est la date dans le format de la BD afin de pouvoir interagir avec
                                echo "<td style=display:none>{$patient['date_naissance']}</td>";

                                // c'est la date pour l'afficher dans le tableau dans le format jj/mm/aaaa
                                $dateAffichage = new DateTime($patient['date_naissance']);
                                echo "<td>{$dateAffichage->format('d/m/Y')}</td>"; // Format jj/mm/aaaa

                                echo "<td>{$patient['lieu_naissance']}</td>";
                                echo "<td>{$patient['num_secu_sociale']}</td>";

                               // Affiche le médecin référent si il existe
                                if (is_null($patient['idM'])) {
                                    echo "<td>Aucun</td>";
                                } else {
                                    $idmedecinRef = $patient['idM'];
                                     // Récupération du nom du médecin
                                     $reqMedecin = $linkpdo->prepare('SELECT nom, prenom FROM medecin WHERE idM = :idM');
                                     if ($reqMedecin == false) {
                                         echo "Erreur dans la préparation de la requête du medecin.";
                                     } else {
                                     $reqMedecin->bindParam(':idM', $idmedecinRef);
                                     $reqMedecin->execute();
                                     if ($reqMedecin == false) {
                                         echo "Erreur dans l'exécution de la requête du medecin.";
                                     } else {
                                         $medecin = $reqMedecin->fetch(PDO::FETCH_ASSOC);
                                         echo "<td>{$medecin['nom']} {$medecin['prenom']}</td>";
                                         }
                                     }
                                }

                                // On rend cette colonne invisible dans le site, elle sert uniquement à récupérer l'id du médecin référent dans la fonction js
                                echo "<td style=display:none >{$patient['idM']}</td>";
                                // On rend cette colonne invisible dans le site, elle sert uniquement à récupérer l'id du patient dans la fonction js
                                echo "<td style=display:none >{$patient['idP']}</td>";

                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        </div>
        
        <div class="button-sous-tableau">
            <a href="ajout_patient.php"><button class="button-ajout">Ajouter un patient</button></a>
            <!--Appel la fonction pour récupérer les infos d'un patient pour le modifier en lui donnant l'id du médecin -->
            <a class="lien-modif-supp" id="boutonModification" onclick="envoyerVersPageModificationPatient()" disabled >Modifier un patient</a>
            <a class="lien-modif-supp" id="boutonSuppression" onclick="envoyerVersPageSuppressionPatient()" disabled >Supprimer un patient</a>
        </div>
    </body>

</html>

