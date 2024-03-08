<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Ajout d'une consultation</title>
        <link href="style/style.css" rel="stylesheet">
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="js/miseAJourComboBox.js"></script>
        <script src="js/erreurSamediDimanche.js"></script>
        <link rel="icon" href="image/favicon.ico" type="image/x-icon">
    </head>
    <body>

        <?php 
            require('module/header.php');
            require('module/bd_connexion.php');
        ?>

        <?php

        $msgErreur = ""; // Déclaration de la variable de message d'erreur

        if (isset($_POST['ajouter_consultation'])) {

            // Préparation de la requête de test de présence de la même consultation 
            $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree AND idP = :idP AND idM = :idM');

            //Test de la requete de présence d'une consultation => die si erreur
            if($reqExisteDeja == false) {
                die("Erreur de préparation de la requête de test de présence d'une consultation.");
            } else {
                
                // Liaison des paramètres
                //PDO::PARAM_STR : C'est le type de données que vous spécifiez pour le paramètre. 
                //Ici, on indique que :nom doit être traité comme une chaîne de caractères (string). 
                //Cela permet à PDO de s'assurer que la valeur est correctement échappée et protégée contre les injections SQL
                $reqExisteDeja->bindParam(':date_consultation', $_POST['date_consultation'], PDO::PARAM_STR);
                $reqExisteDeja->bindParam(':heure_debut', $_POST['heure_debut'], PDO::PARAM_STR);
                $reqExisteDeja->bindParam(':duree', $_POST['duree'], PDO::PARAM_STR);
                $reqExisteDeja->bindParam(':idP', $_POST['idP'], PDO::PARAM_STR);
                $reqExisteDeja->bindParam(':idM', $_POST['idM'], PDO::PARAM_STR);

                // Exécution de la requête
                $reqExisteDeja->execute();

                //Vérification de la bonne exécution de la requete ExisteDéja
                //Si non on arrete et on affiche une erreur
                //Si oui on execute la requete
                if($reqExisteDeja == false) {
                    die("Erreur dans l'exécution de la requête de test de présence d'une consultation.");
                } else {

                    // Récupération du résultat
                    $nbConsultations = $reqExisteDeja->fetchColumn();

                    // Vérification si le patient existe déjà
                    if ($nbConsultations > 0) {
                        $msgErreur = "Cette consultation est déjà enregistrée.";
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

                        //Test de la requete de présence d'une consultation => die si erreur
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
                                if (($nbConsultationChevauchementMedecin > 0) || ($nbConsultationChevauchementPatient > 0)) {
                                    $msgErreur = "Créneau déjà réservé.";
                                } else {

                                    // Préparation de la requête d'insertion
                                    $req = $linkpdo->prepare('INSERT INTO consultation(date_consultation, heure_debut, duree, idP, idM) VALUES(:date_consultation, :heure_debut, :duree, :idP, :idM)');

                                    // Vérification du fonctionnement de la requete d'insertion
                                    if($req == false) {
                                        die('Probleme de la préparation de la requete d\'insertion');
                                    }

                                    if (empty($_POST['date_consultation']) || empty($_POST['heure_debut']) || empty($_POST['duree']) || empty($_POST['idP']) || empty($_POST['idM'])) {
                                        $msgErreur =  "Champs manquants.";
                                    } else {

                                            // Exécution de la requête d'insertion
                                            $req->bindParam(':date_consultation', $_POST['date_consultation'], PDO::PARAM_STR);
                                            $req->bindParam(':heure_debut', $_POST['heure_debut'], PDO::PARAM_STR);
                                            $req->bindParam(':duree', $_POST['duree'], PDO::PARAM_STR);
                                            $req->bindParam(':idP', $_POST['idP'], PDO::PARAM_STR);
                                            $req->bindParam(':idM', $_POST['idM'], PDO::PARAM_STR);
                                            $req->execute();

                                            //Permet de voir comment les requetes SQL agisse sur phpMyAdmin
                                            //$req->debugDumpParams();

                                            $msgErreur =  "La consultation a été ajoutée avec succès !";
                                        }
                                    }
                                }
                            }
                        }   
                    } 
                }      
             }
         ?>

        <!--Espace vide pour permettre de placer le header en haut de page-->
        <div class="vide-haut-page"> </div>

        <div>
            <!--Debut du formulaire-->
            <div class="row justify-content-center">
                <div class=" col-lg-7 col-md-8">
                    <div class="card p-9">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <h2 class="heading text-center">Ajouter une consultation</h2>
                                <div class="errormessage text-center">
                                    <p><?php echo $msgErreur; ?></p>
                                     <!-- Section message erreur pour la selection du samedi ou du dimanche, le test de selection est fais en javascript -->
                                    <p id="message-erreur-date"></p>
                                </div>
                            </div>
                        </div>
                        <form class="form-card" method="post" action="">

                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <label>Date</label>
                                        <div class="input-group-date"> 
                                            <input type="date" name="date_consultation" id="date_consultation" required min="<?php echo date('Y-m-d'); ?>">  
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <label> Choisissez un creneau </label>
                                        <div class="input-group">
                                            <select name="heure_debut" required>
                                                <?php
                                                    $interval = new DateInterval('PT30M');
                                                    $start_time = new DateTime('08:00:00');
                                                    $end_time = new DateTime('17:00:00');

                                                    while ($start_time <= $end_time) {
                                                        $formatted_time = $start_time->format('H:i:s');

                                                        // Exclure les créneaux entre 12h30 et 14h00
                                                        if ($formatted_time !== '12:00:00' && $formatted_time !== '12:30:00' && $formatted_time !== '13:00:00' && $formatted_time !== '13:30:00') {
                                                            echo "<option value=\"$formatted_time\">$formatted_time</option>";
                                                        }

                                                        $start_time->add($interval);
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                <label> Choisissez une durée</label>
                                    <div class="input-group">
                                        <select name="duree" required>
                                            <option value="00:30:00"> 30 minutes </option>
                                            <option value="01:00:00"> 1 heure</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <label> Choisissez un patient</label>
                                    <div class="input-group">
                                        <!-- Ajout de la classe 'patient-select' à l'élément select -->
                                        <select name="idP" class="patient-select" required>
                                            <option> </option>
                                            <?php
                                            $reqPatients = $linkpdo->prepare('SELECT idP, civilite, nom, prenom FROM patient');
                                            if ($reqPatients == false) {
                                                echo "Erreur dans la préparation de la requête d'affichage.";
                                            } else {
                                                $reqPatients->execute();
                                                if ($reqPatients == false) {
                                                    echo "Erreur dans l'exécution de la requête d'affichage.";
                                                } else {
                                                    while ($patient = $reqPatients->fetch(PDO::FETCH_ASSOC)) {
                                                        $idPatient = $patient['idP'];
                                                        $civilitePatient = $patient['civilite'];
                                                        $nomPatient = $patient['nom'];
                                                        $prenomPatient = $patient['prenom'];
                                                        echo "<option value=\"$idPatient\">$civilitePatient $nomPatient $prenomPatient</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <label> Choisissez un medecin</label>
                                    <div class="input-group">
                                        <!-- Ajout de l'ID 'medecin-select' à l'élément select -->
                                        <select name="idM" id="medecin-select" required>
                                            <option id="MedecinRef" value=""> </option>
                                            <option> </option>
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
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="col-12">
                                        <input type="submit" name="ajouter_consultation" value="Ajouter" class="btn">
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <!--Fin du formulaire-->

        </div>
                
</body>

</html>