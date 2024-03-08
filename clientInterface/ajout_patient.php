<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Ajout d'un patient</title>
        <link href="style/style.css" rel="stylesheet">
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <link rel="icon" href="image/favicon.ico" type="image/x-icon">
    </head>
    <body>

        <?php 
        require('module/header.php');
        require('module/bd_connexion.php');
        ?>

        <?php
            $msgErreur = ""; // Déclaration de la variable de message d'erreur

            if (isset($_POST['ajouter_patient'])) {
                // Préparation de la requête de test de présence d'un contact
                $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM patient WHERE nom = :nom AND prenom = :prenom');

                //Test de la requete de présence d'un contact => die si erreur
                if($reqExisteDeja == false) {
                    die("Erreur de préparation de la requête de test de présence d'un patient.");
                } else {

                    // Liaison des paramètres
                    //PDO::PARAM_STR : C'est le type de données que vous spécifiez pour le paramètre. 
                    //Ici, on indique que :nom doit être traité comme une chaîne de caractères (string). 
                    //Cela permet à PDO de s'assurer que la valeur est correctement échappée et protégée contre les injections SQL
                    $reqExisteDeja->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
                    $reqExisteDeja->bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);

                    // Exécution de la requête
                    $reqExisteDeja->execute();

                    //Vérification de la bonne exécution de la requete ExisteDéja
                    //Si oui on arrete et on affiche une erreur
                    //Si non on execute la requete
                    if($reqExisteDeja == false) {
                        die("Erreur dans l'exécution de la requête de test de présence d'un patient.");
                    } else {

                        // Récupération du résultat
                        $nbPatients = $reqExisteDeja->fetchColumn();

                        // Vérification si le patient existe déjà
                        if ($nbPatients > 0) {
                            $msgErreur = "Ce patient existe déjà dans la base de données";
                        } else {
                            // Préparation de la requête d'insertion
                            $req = $linkpdo->prepare('INSERT INTO patient(civilite, nom, prenom, adresse, ville, cp, date_naissance, lieu_naissance, num_secu_sociale, idM) VALUES(:civilite, :nom, :prenom, :adresse, :ville, :cp, :date_naissance, :lieu_naissance, :num_secu_sociale, :idM)');

                            // Vérification du fonctionnement de la requete d'insertion
                            if($req == false) {
                                die('Probleme de la préparation de la requete d\'insertion');
                            }

                            if (empty($_POST['civilite']) || empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['adresse']) || empty($_POST['ville']) || empty($_POST['cp']) || empty($_POST['date_naissance']) || empty($_POST['lieu_naissance']) || empty($_POST['num_secu_sociale'])) {
                                $msgErreur = "";
                            } else {

                                // Attribution des paramètres
                                $req->bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                                $req->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
                                $req->bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                                $req->bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                                $req->bindParam(':ville', $_POST['ville'], PDO::PARAM_STR);
                                $req->bindParam(':cp', $_POST['cp'], PDO::PARAM_STR);
                                $req->bindParam(':date_naissance', $_POST['date_naissance'], PDO::PARAM_STR);
                                $req->bindParam(':lieu_naissance', $_POST['lieu_naissance'], PDO::PARAM_STR);
                                $req->bindParam(':num_secu_sociale', $_POST['num_secu_sociale'], PDO::PARAM_STR);

                                 // Vérification si un médecin référent a été choisi et que la valeur n'est pas vide
                                 if (isset($_POST['idM']) && !empty($_POST['idM'])) {
                                    $idM = $_POST['idM'];      
                                } else {
                                    // Exécuter la requête avec NULL
                                    $idM = null; 
                                }

                                $req->bindParam(':idM', $idM, PDO::PARAM_INT);
                                
                                /// Exécution de la requête d'insertion
                                try {
                                    $req->execute();
                                    $msgErreur = "Le patient a été ajouté avec succès !";
                                } catch (PDOException $e) {
                                    $msgErreur = "Erreur d'exécution de la requête : " . $e->getMessage();
                                }

                                    //Permet de voir comment les requetes SQL agisse sur phpMyAdmin
                                    //$req->debugDumpParams();

                                    //pour rediriger vers le tableau d'affichage des l'insertion
                                    //header("Location: affichage_patient.php?success=1");
                                    //exit;
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
                                <h2 class="heading text-center">Ajouter un patient</h2>
                            </div>
                            <div class="errormessage text-center">
                                <p><?php echo $msgErreur; ?></p>
                            </div>
                        </div>
                        <form class="form-card" method="post" action="">
                            <div class="row justify-content-center form-group">
                                <div class="col-12 px-auto">
                                    <fieldset>
                                        <div class="custom-control custom-radio custom-control-inline"> 
                                            <input id="customRadioInline1" type="radio" name="civilite" value="Mme" class="custom-control-input" checked="true"> 
                                            <label for="customRadioInline1" class="custom-control-label label-radio">Madame</label> 
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline"> 
                                            <input id="customRadioInline2" type="radio" name="civilite" value="M." class="custom-control-input"> 
                                            <label for="customRadioInline2" class="custom-control-label label-radio">Monsieur</label> 
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="input-group"> <input type="text" name="nom" required> <label>Nom</label> </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="input-group"> <input type="text" name="prenom" required> <label>Prénom</label> </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="input-group"> <input type="text" name="adresse" required> <label>Adresse</label> </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group"> <input type="text" minlength="5" maxlength="5"  name="cp" required> <label>Code Postal</label> </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group"> <input type="text" name="ville" required> <label>Ville</label> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <!--Définit la date max comme la date actuelle, permet d'éviter de mettre une date de naissance antérieur-->
                                    <div class="input-group"> <input type="date" name="date_naissance" required value="<?php echo $date_naissance; ?>" max="<?php echo date('Y-m-d'); ?>"> <label>Date de naissance</label> </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="input-group"> <input type="text" name="lieu_naissance" required> <label>Lieu de naissance</label> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="input-group"> <input type="text" name="num_secu_sociale" minlength="13" maxlength="13" required> <label>Numéro de sécurité sociale </label> </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                <label> Choisissez un medecin référent (facultatif)</label>
                                    <div class="input-group">
                                        <select name="idM"> <!--required-->
                                            <option value=""> </option>
                                            <?php
                                            //requête pour afficher la liste des medecins pour le choix d'un medecin référent
                                            $reqMedecins = $linkpdo->prepare('SELECT idM, civilite,nom, prenom FROM medecin');
                                            $reqMedecins->execute();
                                            while ($medecin = $reqMedecins->fetch(PDO::FETCH_ASSOC)) {
                                                $idMedecin = $medecin['idM'];
                                                $civiliteMedecin = $medecin['civilite'];
                                                $nomMedecin = $medecin['nom'];
                                                $prenomMedecin = $medecin['prenom'];
                                                echo "<option value=\"$idMedecin\">$civiliteMedecin $nomMedecin $prenomMedecin</option>";}
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="col-12">
                                        <input type="submit" name="ajouter_patient" value="Ajouter" class="btn">
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