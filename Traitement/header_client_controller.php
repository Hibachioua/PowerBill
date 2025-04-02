<?php
// Traitement/header_client_controller.php
require_once "auth_check.php";

$current_page = basename($_SERVER['PHP_SELF']);
    
// Préparer les données
$header_client_data = prepareHeaderClientData($current_page);


function prepareHeaderClientData($current_page) {
    // Construction des éléments de navigation
    $nav_items = [
        'dashboard' => [
            'label' => 'Tableau de bord',
            'url' => 'client_dashboard.php',
            'active' => ($current_page == 'client_dashboard.php')
        ],
        'consommation' => [
            'label' => 'Ma Consommation',
            'url' => 'client_consommation.php', 
            'active' => ($current_page == 'client_consommation.php')
        ],
        'factures' => [
            'label' => 'Mes Factures',
            'url' => 'client_factures.php',
            'active' => ($current_page == 'client_factures.php') 
        ],
        'reclamations' => [
            'label' => 'Réclamations',
            'url' => 'client_reclamations.php',
            'active' => ($current_page == 'client_reclamations.php')
        ]
    ];
    
    // Vérifier si la session contient l'email de l'utilisateur
    $user_email = '';
    if (isset($_SESSION['user_email'])) {
        $user_email = $_SESSION['user_email'];
    } else {
        // Si email pas disponible, essayer de récupérer d'autres infos disponibles
        $user_email = isset($_SESSION['user_id']) ? 'Client #' . $_SESSION['user_id'] : 'Client';
    }
    
    return [
        'nav_items' => $nav_items,
        'logout_url' => '../Traitement/logout.php',
        'user_email' => $user_email  // Assurez-vous que cette clé existe toujours
    ];
}
?>