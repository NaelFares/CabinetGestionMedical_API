<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Suppression d'une consultation</title>
    <link href="style/style.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="icon" href="image/favicon.ico" type="image/x-icon">
</head>

<body>
        
    <?php
    require('module/bd_connexion.php');


    if (!empty($_GET['date_consultation']) && !empty($_GET['heure_debut']) && !empty($_GET['duree']) && !empty($_GET['npPatient']) && !empty($_GET['npMedecin']) && !empty($_GET['idP']) && !empty($_GET['idM']) && !empty($_GET['date_BD'])) {
        // Récupérez les valeurs des paramètres GET
        $date_consultation = $_GET['date_consultation'];
        $heure_debut = $_GET['heure_debut'];
        $duree = $_GET['duree'];
        $npPatient = $_GET['npPatient'];
        $npMedecin = $_GET['npMedecin'];
        $idP = $_GET['idP'];
        $idM = $_GET['idM'];
        $date_BD = $_GET['date_BD'];
    }
    ?>

    <?php
        $msgErreur = ""; // Déclaration de la variable de message d'erreur

        if (isset($_POST['supprimer_consultation'])) {
            // Préparation de la requête de suppression
            // La prochaine fois utiliser + de paramètres dans le where pour éviter de supprimer les infos d'un homonyme  
            $reqSuppression = $linkpdo->prepare('DELETE FROM consultation WHERE date_consultation = :date_consultation AND heure_debut = :heure_debut AND duree = :duree AND idP = :idP AND idM = :idM');

            if ($reqSuppression === false) {
                echo "Erreur de préparation de la requête.";
            } else {
                // Liaison des paramètres
                $reqSuppression->bindParam(':date_consultation', $date_BD, PDO::PARAM_STR);
                $reqSuppression->bindParam(':heure_debut', $heure_debut, PDO::PARAM_STR);
                $reqSuppression->bindParam(':duree', $duree, PDO::PARAM_STR);
                $reqSuppression->bindParam(':idP', $idP, PDO::PARAM_STR);
                $reqSuppression->bindParam(':idM', $idM, PDO::PARAM_STR);

                //Debug
                //echo "DELETE FROM consultation WHERE date_consultation = " . $date_BD . " AND heure_debut =" . $heure_debut . " AND duree = " . $duree . " AND idP = " . $idP . " AND idM = " . $idM ;

                // Exécution de la requête
                $reqSuppression->execute();

                if ($reqSuppression === false) {
                    $msgErreur = "Erreur dans l'exécution de la requête de suppression : ";
                } else {
                    // Afficher un message de succès
                    $msgErreur = "La consultation a été supprimée avec succès !";
                   
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
                                <h3 class="titre-suppression">Voulez-vous vraiment supprimer cette consultation ?</h3>
                            </div>
                            <div class="errormessage text-center">
                                <p><?php echo $msgErreur; ?></p>
                            </div>
                            <div class="informations">
                                <p class="informations-medecin-patient-consultation"><?php echo "Le " . $date_consultation . " à " . $heure_debut?> </p>
                                <p> Patient : <?php echo $npPatient?></p> 
                                <p> Medecin : <?php echo $npMedecin?></p> 
                                <p> Durée : <?php echo $duree?></p> 
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="boutons-suppression">
                                    <a href="affichage_consultation.php" class="btn-supp-annuler">Retour à la liste</a>
                                    <div><input class="input-supp-valider" type="submit" name="supprimer_consultation" value="Supprimer la consultation"></div>
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
