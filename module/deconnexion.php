<?php
    session_start();
    session_destroy();
    header("location: ../index.php"); // On précise le chemin avec ../ (le répertoire parent) car on est dans un sous dossier de l'application
?>