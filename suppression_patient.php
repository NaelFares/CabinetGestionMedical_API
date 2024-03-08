<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Suppression d'un patient</title>
    <link href="style/style.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="icon" href="image/favicon.ico" type="image/x-icon">
</head>

<body>
        
    <?php
    require('module/bd_connexion.php');

    if (!empty($_GET['civilite']) && !empty($_GET['nom']) && !empty($_GET['prenom']) && !empty($_GET['adresse']) && !empty($_GET['cp']) && !empty($_GET['ville']) && !empty($_GET['date_BD']) && !empty($_GET['date_naissance']) && !empty($_GET['lieu_naissance']) && !empty($_GET['num_secu_sociale']) && !empty($_GET['idP'])) {
        // Récupérez les valeurs des paramètres GET
        $civilite = $_GET['civilite'];
        $nom = $_GET['nom'];
        $prenom = $_GET['prenom'];
        $adresse = $_GET['adresse'];
        $cp = $_GET['cp'];
        $ville = $_GET['ville'];
        $date_BD = $_GET['date_BD'];
        // uniquement pour l'affichage dans la page suppression
        $date_naissance = $_GET['date_naissance'];
        $lieu_naissance = $_GET['lieu_naissance'];
        $num_secu_sociale = $_GET['num_secu_sociale'];
        $idM = $_GET['idM'];
        $idP = $_GET['idP'];
    }
    ?>

    <?php
        $msgErreur = ""; // Déclaration de la variable de message d'erreur

        if (isset($_POST['supprimer_patient'])) {

            // Préparation de la requête de test de présence d'une consultation pour ce patient
            $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM consultation WHERE idP = :idP');

            //Test de la requete de présence d'une consultation => die si erreur
            if($reqExisteDeja == false) {
                die("Erreur de préparation de la requête de test de présence de consultations.");
            } else {

                $reqExisteDeja->bindParam(':idP', $idP , PDO::PARAM_STR);

                // Exécution de la requête
                $reqExisteDeja->execute();

                //Vérification de la bonne exécution de la requete ExisteDéja
                //Si oui on arrete et on affiche une erreur
                //Si non on execute la requete
                if($reqExisteDeja == false) {
                    die("Erreur dans l'exécution de la requête de test de présence d'une consultation.");
                } else {
                     // Récupération du résultat
                     $nbConsultations = $reqExisteDeja->fetchColumn();

                     // Vérification si la consultation existe déjà
                     if ($nbConsultations > 0) {
                         $reqDeleteConsultationDuPatient = $linkpdo->prepare('DELETE FROM consultation WHERE idP = :idP');

                         if($reqDeleteConsultationDuPatient == false) {
                            die("Erreur de préparation de la requête de suppression de consultations.");
                        } else {
                            $reqDeleteConsultationDuPatient->bindParam(':idP', $idP , PDO::PARAM_STR);
            
                            // Exécution de la requête
                            $reqDeleteConsultationDuPatient->execute();
                        }
                    }

                    // Préparation de la requête de suppression
                    // La prochaine fois utiliser + de paramètres dans le where pour éviter de supprimer les infos d'un homonyme  
                    $reqSuppression = $linkpdo->prepare('DELETE FROM patient WHERE idP = :idP AND num_secu_sociale = :num_secu_sociale');

                    if ($reqSuppression === false) {
                        echo "Erreur de préparation de la requête.";
                    } else {
                        // Liaison des paramètres
                        $reqSuppression->bindParam(':idP', $idP, PDO::PARAM_STR);
                        $reqSuppression->bindParam(':num_secu_sociale', $num_secu_sociale, PDO::PARAM_STR);

                        $reqSuppression->execute();

                        if ($reqSuppression === false) {
                            $msgErreur = "Erreur dans l'exécution de la requête de suppression : ";
                        } else {
                            // Afficher un message de succès
                            $msgErreur = "Le patient a été supprimé avec succès !";
                            
                        }
                    }
                }
            }
        }
    ?>

    <div class="centrer-milieu-page">
        <div class="row justify-content-center">
            <div class=" col-lg-7 col-md-8">
                <div class="card p-9">
                    <form class="form-card" method="post" action="">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <h3 class="titre-suppression">Voulez-vous vraiment supprimer ce patient ?</h3>
                            </div>
                            <div class="errormessage text-center">
                                <p><?php echo $msgErreur; ?></p>
                            </div>
                            <div class="informations">
                                <p class="informations-medecin-patient-consultation"><?php echo $civilite . " " . $nom . " " . $prenom?></p>
                                <p> Date de naissance : <?php echo $date_naissance?></p> 
                                <p> Num sécurité sociale : <?php echo $num_secu_sociale?></p> 
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="boutons-suppression">
                                    <a href="affichage_patient.php" class="btn-supp-annuler">Retour à la liste</a>
                                    <div><input class="input-supp-valider" type="submit" name="supprimer_patient" value="Supprimer le patient"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>
