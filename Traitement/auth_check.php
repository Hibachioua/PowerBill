<?php

require_once "../BD/connexion.php";
require_once "../BD/LoginController.php";
require_once "../BD/NavigationController.php";



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = DB::connect();
$loginController = new LoginController($db);

$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;

if (!$isLoggedIn) {
    $rememberedUser = $loginController->checkRememberToken();
    if ($rememberedUser && $rememberedUser['success']) {
        $isLoggedIn = true;
        $_SESSION['loggedIn'] = true;
    }
}

if (!$isLoggedIn) {
    header("Location: ../IHM/login.php");
    exit();
}

// Définir le niveau d'accès nécessaire (à personnaliser selon la page)
function checkUserAccess($requiredRole = null) {
    global $loginController;
    
    if ($requiredRole !== null && $_SESSION['user_role'] != $requiredRole) {
        // Rediriger vers la page appropriée selon le rôle
        header("Location: " . $loginController->getRedirectPath($_SESSION['user_role']));
        exit();
    }
}

// Préparer les données communes pour les vues
$current_page = basename($_SERVER['PHP_SELF']);
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];


$navController = new NavigationController();
$sidebar_data = $navController->getSidebarData(basename($_SERVER['PHP_SELF']), $_SESSION['user_role']);

?>