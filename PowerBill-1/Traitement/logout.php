<?php
// Traitement/logout.php - Contrôleur pour la déconnexion
require_once __DIR__ . "/../BD/logoutModel.php";

/**
 * Gère le processus de déconnexion complet
 * En suivant le pattern MVC strictement, ce contrôleur coordonne le processus
 * mais délègue toutes les opérations de données au modèle
 */
function processLogout() {
    // 1. Démarrer la session si elle n'est pas active
    if (!isSessionActive()) {
        session_start();
    }
    
    // 2. Supprimer le token de mémorisation en base de données si l'utilisateur est connecté
    $userId = getCurrentUserId();
    if ($userId) {
        removeRememberToken($userId);
    }
    
    // 3. Supprimer le cookie de mémorisation
    removeRememberCookie();
    
    // 4. Vider les données de session
    $_SESSION = array();
    
    // 5. Supprimer le cookie de session
    removeSessionCookie();
    
    // 6. Détruire la session
    session_destroy();
    
    // 7. Redirection vers la page d'accueil
    return true;
}

// Exécuter la déconnexion
if (processLogout()) {
    header("Location: ../IHM/index.php");
    exit();
} else {
    // En cas d'erreur (rare mais possible)
    header("Location: ../IHM/error.php?message=Erreur lors de la déconnexion");
    exit();
}
?>