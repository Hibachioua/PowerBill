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


// // Fonction pour construire les données de la sidebar
// function getSidebarData($current_page, $user_role) {
//     return [
//         'nav_items' => [
//             [
//                 'icon' => 'fas fa-home',
//                 'label' => 'Dashboard',
//                 'url' => getDashboardUrl($user_role),
//                 'active' => ($current_page == 'fournisseur_dashboard.php')
//             ],
//             [
//                 'icon' => 'fas fa-history',
//                 'label' => 'History',
//                 'url' => 'history.php',
//                 'active' => ($current_page == 'history.php')
//             ],
//             [
//                 'icon' => 'fas fa-file-invoice',
//                 'label' => 'Manage invoices',
//                 'url' => 'manage_invoices.php',
//                 'active' => ($current_page == 'manage_invoices.php')
//             ],
//             [
//                 'icon' => 'fas fa-user',
//                 'label' => 'Manage User',
//                 'url' => 'manage_user.php',
//                 'active' => ($current_page == 'manage_user.php')
//             ],
//             [
//                 'icon' => 'fas fa-exclamation-circle',
//                 'label' => 'Manage complaints',
//                 'url' => 'manage_complaints.php',
//                 'active' => ($current_page == 'manage_complaints.php')
//             ]
//         ],
//         'logout_url' => '../../Traitement/logout.php' // Assurez-vous que ce chemin est correct
//     ];
// }



?>