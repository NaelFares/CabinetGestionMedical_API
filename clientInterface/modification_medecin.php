<?php
require('module/verificationUtilisateur.php');
?>

<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Modification d'un medecin</title>
        <link href="style/style.css" rel="stylesheet">
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <link rel="icon" href="image/favicon.ico" type="image/x-icon">       
    </head>
    <body>

    <?php
    require('module/bd_connexion.php');
    require('module/header.php');


    if (!empty($_GET['civilite']) && !empty($_GET['nom']) && !empty($_GET['prenom'])) {
        // Récupérez les valeurs des paramètres GET
        $civilite = $_GET['civilite'];
        $nom = $_GET['nom'];
        $prenom = $_GET['prenom'];
    }
    ?>

    <?php
         $msgErreur = ""; // Déclaration de la variable de message d'erreur

        if (isset($_POST['modifier_medecin'])) {
            // Préparation de la requête d'insertion
            // La prochaine fois utiliser + de paramètres dans le where pour éviter de modifier les infos d'un homonyme 
            $reqModification = $linkpdo->prepare('UPDATE medecin SET civilite = :nouvelleCivilite, nom = :nouveauNom, prenom = :nouveauPrenom WHERE nom = :nom AND prenom = :prenom');

            if ($reqModification === false) {
                echo "Erreur de préparation de la requête.";
            } else {
                $reqModification->bindParam(':nouvelleCivilite', $_POST['civilite'], PDO::PARAM_STR);
                $reqModification->bindParam(':nouveauNom', $_POST['nom'], PDO::PARAM_STR);
                $reqModification->bindParam(':nouveauPrenom', $_POST['prenom'], PDO::PARAM_STR);        
                }

                // Paramètres du where
                $reqModification->bindParam(':nom', $nom, PDO::PARAM_STR);
                $reqModification->bindParam(':prenom', $prenom, PDO::PARAM_STR);

                // Exécution de la requête
                 $reqModification->execute();

                if($reqModification == false) {
                    echo "Erreur dans l'exécution de la requête de modification.";
                } else {
                    // Afficher un message de succès
                    $msgErreur = "Le medecin a été modifié avec succès !";

                    // vider les valeurs dans les champs de saisie pour éviter les erreurs de récupération de champs vides par $_POST
                    $civilite = null;
                    $nom = null;
                    $prenom = null;
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
                                <h2 class="heading text-center">Modifier un medecin</h2>
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
                                            <input id="customRadioInline1" type="radio" name="civilite" value="Mme" class="custom-control-input"  
                                            <?php echo ($civilite == 'Mme') ? 'checked' : ''; ?>> <!--Vérifie et coche la case correspondant à la civilité-->
                                            <label for="customRadioInline1" class="custom-control-label label-radio">Madame</label> 
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline"> 
                                            <input id="customRadioInline2" type="radio" name="civilite" value="M." class="custom-control-input"
                                            <?php echo ($civilite == 'M.') ? 'checked' : ''; ?>> <!--Vérifie et coche la case correspondant à la civilité-->
                                            <label for="customRadioInline2" class="custom-control-label label-radio">Monsieur</label> 
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="input-group"> <input type="text" name="nom" required value="<?php echo $nom; ?>"> <label>Nom</label> </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="input-group"> <input type="text" name="prenom" required value="<?php echo $prenom; ?>"> <label>Prénom</label> </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="col-12">
                                        <input type="submit" name="modifier_medecin" value="Valider les modifications" class="btn">
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