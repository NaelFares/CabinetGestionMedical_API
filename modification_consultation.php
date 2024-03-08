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
        <script src="miseAJourComboBox.js"></script>
        <script src="js/erreurSamediDimanche.js"></script>
        <link rel="icon" href="image/favicon.ico" type="image/x-icon">
    </head>
    <body>

        <?php 
            require('module/header.php');
            require('module/bd_connexion.php');

            if (!empty($_GET['date_BD']) && !empty($_GET['heure_debut']) && !empty($_GET['duree']) && !empty($_GET['idP']) && !empty($_GET['idM'])  && !empty($_GET['npPatient'])  && !empty($_GET['npMedecin'])) {
                // Récupérez les valeurs des paramètres GET
                $heure_debut = $_GET['heure_debut'];
                $duree = $_GET['duree'];
                $idP = $_GET['idP'];
                $idM = $_GET['idM'];
                $date_BD = $_GET['date_BD'];
                $npPatient = $_GET['npPatient'];
                $npMedecin = $_GET['npMedecin'];

            }
        ?>

        <?php
            $msgErreur = ""; // Déclaration de la variable de message d'erreur

            if (isset($_POST['modifier_consultation'])) {

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

                            // Préparation de la requête d'insertion
                            // La prochaine fois utiliser + de paramètres dans le where pour éviter de modifier les infos d'un homonyme 
                            $reqModification = $linkpdo->prepare('UPDATE consultation SET date_consultation = :nouvelleDate_consultation, heure_debut = :nouvelleHeure_debut, duree = :nouvelleDuree, idP = :nouveauIdP, idM = :nouveauIdM WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree AND idP = :idP AND idM = :idM');

                            if ($reqModification === false) {
                                echo "Erreur de préparation de la requête.";
                            } else {
                                $reqModification->bindParam(':nouvelleDate_consultation', $_POST['date_consultation'], PDO::PARAM_STR);
                                $reqModification->bindParam(':nouvelleHeure_debut', $_POST['heure_debut'], PDO::PARAM_STR);
                                $reqModification->bindParam(':nouvelleDuree', $_POST['duree'], PDO::PARAM_STR);                   
                                $reqModification->bindParam(':nouveauIdM', $_POST['idM'], PDO::PARAM_INT);
                                $reqModification->bindParam(':nouveauIdP', $_POST['idP'], PDO::PARAM_INT);


                                // Paramètres du where
                                $reqModification->bindParam(':date_consultation', $date_BD, PDO::PARAM_STR);
                                $reqModification->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                                $reqModification->bindParam(':duree', $duree, PDO::PARAM_STR);
                                $reqModification->bindParam(':idM', $idM, PDO::PARAM_STR);
                                $reqModification->bindParam(':idP', $idP, PDO::PARAM_STR);

                                // Exécution de la requête
                                $reqModification->execute();

                                if($reqModification == false) {
                                    echo "Erreur dans l'exécution de la requête de modification.";
                                } else {
                                    // Afficher un message de succès
                                    $msgErreur = "La consultation a été modifiée avec succès !";

                                    // vider les valeurs dans les champs de saisie pour éviter les erreurs de récupération de champs vides par $_POST
                                    $date_BD = null;
                                    $heure_debut = null;
                                    $duree = null;
                                    $idM = null;
                                    $idP = null;
                                    $npMedecin = null;
                                    $npPatient = null;
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
                                <h2 class="heading text-center">Modifier une consultation</h2>
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
                                            <input type="date" name="date_consultation" required value="<?php echo $date_BD ?>">  
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <label> Choisissez un creneau </label>
                                        <div class="input-group">
                                            <select name="heure_debut" required>
                                                <option> <?php echo $heure_debut; ?> </option> 

                                                <option> </option>

                                                <?php
                                                    $interval = new DateInterval('PT30M');
                                                    $start_time = new DateTime('08:00');
                                                    $end_time = new DateTime('17:00');

                                                    while ($start_time <= $end_time) {
                                                        $formatted_time = $start_time->format('H:i');

                                                        // Exclure les créneaux entre 12h30 et 14h00
                                                        if ($formatted_time !== '12:30' && $formatted_time !== '13:00' && $formatted_time !== '13:30') {
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
                                            <option> <?php echo $duree ?> </option>
                                            <option> </option>
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
                                            <option  value="<?php echo $idP ?>" > <?php echo $npPatient ?> </option>
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
                                            <option value="<?php echo $idM ?>"> <?php echo $npMedecin ?> </option>
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
                                        <input type="submit" name="modifier_consultation" value="Valider les modifications" class="btn">
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