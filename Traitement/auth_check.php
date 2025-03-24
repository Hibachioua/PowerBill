<?php
// auth_check.php - À placer dans le dossier Traitement ou Controllers
require_once "../BD/connexion.php";
require_once "../BD/LoginController.php";

// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Créer l'instance du contrôleur
$db = DB::connect();
$loginController = new LoginController($db);

// Vérifier si l'utilisateur est connecté via session
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;

// Si l'utilisateur n'est pas connecté via session, essayer avec le cookie "remember_token"
if (!$isLoggedIn) {
    $rememberedUser = $loginController->checkRememberToken();
    if ($rememberedUser && $rememberedUser['success']) {
        $isLoggedIn = true;
        $_SESSION['loggedIn'] = true;
    }
}

// Si l'utilisateur n'est toujours pas connecté, rediriger vers la page de connexion
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
?>