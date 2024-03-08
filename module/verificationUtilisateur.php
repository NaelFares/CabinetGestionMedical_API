<?php
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit();
}
?>