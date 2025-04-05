<?php
require_once "../BD/logoutModel.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Supprimer le token de la base de données
if (isset($_SESSION['user_id'])) {
    removeRememberToken($_SESSION['user_id']);
}

// Supprimer le cookie de mémorisation s'il existe
if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

// Détruire la session
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
session_destroy();

// Rediriger vers la page d'accueil
header("Location: ../IHM/index.php");
exit();
?>