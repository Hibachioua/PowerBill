<?php
// Traitement/auth_controller.php
require_once __DIR__ . "/../BD/connexion.php";
require_once __DIR__ . "/../BD/loginModel.php";


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$connexion = connectDB();


// Vérifie si l'utilisateur est connecté via session
$isLoggedIn = isset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;

// Si non connecté, essaye via cookie
if (!$isLoggedIn && $connexion) {
    $isLoggedIn = checkRememberToken($connexion);
}

// Redirige si non connecté
if (!$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

// Fonction pour vérifier les droits d’accès
function checkUserAccess($requiredRole = null) {
    if ($requiredRole !== null && $_SESSION['user_role'] != $requiredRole) {
        header("Location: " . getRedirectPath($_SESSION['user_role']));
        exit();
    }
}


// Fonction pour obtenir l’URL du dashboard selon le rôle



// Fonction utilitaire pour vérifier si la page est un dashboard


// Fonction pour redirection selon le rôle

?>