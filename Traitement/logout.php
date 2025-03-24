<?php
// Démarrer la session
session_start();


$_SESSION = array();

// Détruire le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Détruire la session
session_destroy();

// Supprimer le cookie "remember_user" s'il existe
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Rediriger vers la page d'accueil
header("Location: ../IHM/index.php");
exit();
?>