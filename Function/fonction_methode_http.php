<?php
	/*Ce fichier contiendra toutes les définitions des fonctions de manipulations des données en SQL.*/

    require("auth_API/jwt_utils.php");
	require("GestionConsultations_API/Switch.php");
	require("GestionMedecins_API/Switch.php");
	require("GestionUsagers_API/Switch.php");

	function getAllPhrases(){
		$resultat = $linkpdo->prepare('SELECT * FROM chuckn_facts');
		if ($resultat == false) {
			deliver_response('500', 'Erreur dans la préparation de la requête', $resultat);
	    } else {
	        $resultat->execute();
	    	if ($resultat == false) {
	    		deliver_response('500', 'Erreur dans l\'exécution de la requête', $resultat);
	        } else {
	            return $resultat;
			}
		}
	}

	// A faire : Verifier si le id existe bien 
	function getPhrase($id){
		$resultat = $linkpdo->prepare('SELECT * FROM chuckn_facts WHERE id = $id');
		if ($resultat == false) {
			deliver_response('500', 'Erreur dans la préparation de la requête', $resultat);
		} else {
			$resultat->execute();
			if ($resultat == false) {
				deliver_response('500', 'Erreur dans l\'exécution de la requête', $resultat);
			} else {			         
				return $resultat;		                	
			}
		}
	}

	function addPhrase(){
		$resultat = $linkpdo->prepare('INSERT INTO chuckn_facts (phrase) VALUES nouvellePhrase'); //nouvelle phrase a trouver
		if ($resultat == false) {
			deliver_response('500', 'Erreur dans la préparation de la requête', $resultat);
		} else {
			$resultat->execute();
			if ($resultat == false) {
			    deliver_response('500', 'Erreur dans l\'exécution de la requête', $resultat);
			} else {
				//.........................................................
			}
		}
	}

	function majPartielPhrase($id){
		$resultat = $linkpdo->prepare(''); ///////////A faire
		if ($resultat == false) {
			deliver_response('500', 'Erreur dans la préparation de la requête', $resultat);
		} else {
			$resultat->execute();
			if ($resultat == false) {
			    deliver_response('500', 'Erreur dans l\'exécution de la requête', $resultat);
			} else {
				//................................
			}
		}
	}

	function majTotalPhrase($id, $phrase, $vote, $faute, $signalement){
		$resultat = $linkpdo->prepare(''); ///////////A faire
		if ($resultat == false) {
			deliver_response('500', 'Erreur dans la préparation de la requête', $resultat);
		} else {
			$resultat->execute();
			if ($resultat == false) {
			    deliver_response('500', 'Erreur dans l\'exécution de la requête', $resultat);
			} else {
				//................................
			}
		}
	}

	function deletePhrase($id){
		$resultat = $linkpdo->prepare(''); ///////////A faire
		if ($resultat == false) {
			deliver_response('500', 'Erreur dans la préparation de la requête', $resultat);
		} else {
			$resultat->execute();
			if ($resultat == false) {
			    deliver_response('500', 'Erreur dans l\'exécution de la requête', $resultat);
			} else {
				//................................
			}
		}
	}
?>