<?php
// Traitement/sidebar_controller.php
require_once "auth_check.php";
$current_page = basename($_SERVER['SCRIPT_NAME']); 
$user_role = $_SESSION['user_role'];
$sidebar_data = prepareSidebarData($current_page, $user_role);
/**
 * Prépare les données pour la sidebar
 * 
 * @param string $current_page Page actuelle
 * @param int $user_role Rôle de l'utilisateur
 * @return array Données pour l'affichage de la sidebar
 */
function prepareSidebarData($current_page, $user_role) {
    // Construction des données de navigation en fonction du rôle et de la page active
    $nav_items = [
        'dashboard' => [
            'icon' => 'fas fa-home',
            'label' => 'Dashboard',
            'url' => getDashboardUrl($user_role),
            'active' => isDashboardActive($current_page)
        ],
        'history' => [
            'icon' => 'fas fa-history',
            'label' => 'History',
            'url' => 'history.php',
            'active' => ($current_page == 'history.php')
        ],
        'invoices' => [
            'icon' => 'fas fa-file-invoice',
            'label' => 'Manage invoices',
            'url' => 'manage_invoices.php',
            'active' => ($current_page == 'manage_invoices.php')
        ],
        'users' => [
            'icon' => 'fas fa-user',
            'label' => 'Manage User',
            'url' => 'manage_user.php',
            'active' => ($current_page == 'manage_user.php')
        ],
        'complaints' => [
            'icon' => 'fas fa-exclamation-circle',
            'label' => 'Manage complaints',
            'url' => 'manage_complaints.php',
            'active' => ($current_page == 'manage_complaints.php')
        ]
    ];
    
    // Retourner les données formatées pour la vue
    return [
        'nav_items' => $nav_items,
        'logout_url' => '../Traitement/logout.php'
    ];
}

/**
 * Détermine si la page actuelle est un dashboard
 * 
 * @param string $current_page Page actuelle
 * @return bool True si c'est un dashboard
 */
function isDashboardActive($current_page) {
    return in_array($current_page, [
        'dashboard.php',
        'client_dashboard.php',
        'agent_dashboard.php',
        'fournisseur_dashboard.php'
    ]);
}

/**
 * Retourne l'URL du dashboard en fonction du rôle
 * 
 * @param int $user_role Rôle de l'utilisateur
 * @return string URL du dashboard
 */
function getDashboardUrl($user_role) {
    switch ($user_role) {
        case 1: return 'client_dashboard.php';
        case 2: return 'agent_dashboard.php';
        case 3: return 'fournisseur_dashboard.php';
        default: return 'dashboard.php';
    }
}

// Point d'entrée si le fichier est appelé directement
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    // Redirection vers une page par défaut
    header("Location: ../IHM/index.php");
    exit();
}
?>