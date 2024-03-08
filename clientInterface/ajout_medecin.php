<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Ajout d'un medecin</title>
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

            if (isset($_POST['ajouter_medecin'])) {

                // Préparation de la requête de test de présence d'un medecin
                $reqExisteDeja = $linkpdo->prepare('SELECT COUNT(*) FROM medecin WHERE nom = :nom AND prenom = :prenom');

                //Test de la requete de présence d'un medecin => die si erreur
                if($reqExisteDeja == false) {
                    die("Erreur de préparation de la requête de test de présence d'un medecin.");
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
                        die("Erreur dans l'exécution de la requête de test de présence d'un medecin.");
                    } else {

                        // Récupération du résultat
                        $nbMedecins = $reqExisteDeja->fetchColumn();

                        // Vérification si le patient existe déjà
                        if ($nbMedecins > 0) {
                            $msgErreur = "Ce medecin existe déjà dans la base de données";
                        } else {
                            // Préparation de la requête d'insertion
                            $req = $linkpdo->prepare('INSERT INTO medecin(civilite, nom, prenom) VALUES(:civilite, :nom, :prenom)');

                            // Vérification du fonctionnement de la requete d'insertion
                            if($req == false) {
                                die('Probleme de la préparation de la requete d\'insertion');
                            }

                            if (empty($_POST['civilite']) || empty($_POST['nom']) || empty($_POST['prenom'])) {
                                $msgErreur = "";
                            } else {

                                    // Exécution de la requête d'insertion
                                    $req->bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                                    $req->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
                                    $req->bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                                    $req->execute();

                                        //Permet de voir comment les requetes SQL agisse sur phpMyAdmin
                                        //$req->debugDumpParams();

                                        $msgErreur = "Le medecin a été ajouté avec succès !";
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
                                <h2 class="heading text-center">Ajouter un medecin</h2>
                            </div>
                            <div class="errormessage text-center">
                                <p><?php echo $msgErreur; ?></p>
                            </div>
                        </div>
                        <form class="form-card" method="post" action="ajout_medecin.php">
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
                                    <div class="col-12">
                                        <input type="submit" name="ajouter_medecin" value="Ajouter" class="btn">
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


<script>
        $(document).ready(function(){


//For Date formatted input
var expDate = document.getElementById('exp');
expDate.onkeyup = function (e) {
    if (this.value == this.lastValue) return;
    var caretPosition = this.selectionStart;
    var sanitizedValue = this.value.replace(/[^0-9]/gi, '');
    var parts = [];
    
    for (var i = 0, len = sanitizedValue.length; i < len; i += 2) {
        parts.push(sanitizedValue.substring(i, i + 2));
    }
    
    for (var i = caretPosition - 1; i >= 0; i--) {
        var c = this.value[i];
        if (c < '0' || c > '9') {
            caretPosition--;
        }
    }
    caretPosition += Math.floor(caretPosition / 2);
    
    this.value = this.lastValue = parts.join('/');
    this.selectionStart = this.selectionEnd = caretPosition;
}
	
	// Radio button
	$('.radio-group .radio').click(function(){
	    $(this).parent().parent().find('.radio').removeClass('selected');
	    $(this).addClass('selected');
	});
})
</script>