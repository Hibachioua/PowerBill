<?php

/**  
 * @param string $current_page Page actuelle
 * @param int $user_role Rôle de l'utilisateur
 * @return array Données pour la vue sidebar
 */
function getSidebarData($current_page, $user_role) {

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
    
    return [
        'nav_items' => $nav_items,
        'logout_url' => '../Traitement/logout.php'
    ];
}

/**  
 * @param string $current_page Nom de la page actuelle
 * @return bool True si c'est une page de dashboard
 */
function isDashboardActive($current_page) {
    $dashboard_pages = [
        'dashboard.php',
        'client_dashboard.php',
        'agent_dashboard.php',
        'fournisseur_dashboard.php'
    ];
    
    return in_array($current_page, $dashboard_pages);
}

/**
 * Obtient l'URL du dashboard selon le rôle
 * 
 * @param int $user_role Rôle de l'utilisateur
 * @return string URL du dashboard
 */
function getDashboardUrl($user_role) {
    switch ($user_role) {
        case 1:
            return 'client_dashboard.php';
        case 2:
            return 'agent_dashboard.php';
        case 3:
            return 'fournisseur_dashboard.php';
        default:
            return 'dashboard.php';
    }
}
?>