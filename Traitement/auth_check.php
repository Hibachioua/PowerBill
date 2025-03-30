<?php
// auth_check.php

require_once "../BD/connexion.php";

// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$connexion = connectDB();

// Vérifier si l'utilisateur est connecté via session
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;

// Si l'utilisateur n'est pas connecté via session, essayer avec le cookie "remember_token"
if (!$isLoggedIn && $connexion) {
    $isLoggedIn = checkRememberToken($connexion);
}

// Si l'utilisateur n'est toujours pas connecté, rediriger vers la page de connexion
if (!$isLoggedIn) {
    header("Location: ../IHM/login.php");
    exit();
}

// Définir une fonction pour vérifier les accès par rôle
function checkUserAccess($requiredRole = null) {
    if ($requiredRole !== null && $_SESSION['user_role'] != $requiredRole) {
        // Rediriger vers la page appropriée selon le rôle
        header("Location: " . getRedirectPath($_SESSION['user_role']));
        exit();
    }
}

// Fonction pour obtenir le chemin de redirection selon le rôle
function getRedirectPath($roleId) {
    switch ($roleId) {
        case 1: // Client
            return '../IHM/client_dashboard.php';
        case 2: // Agent
            return '../IHM/agent_dashboard.php';
        case 3: // Fournisseur
            return '../IHM/fournisseur_dashboard.php';
        default:
            return '../IHM/dashboard.php';
    }
}

// Fonction pour vérifier si un token "Se souvenir de moi" est valide
function checkRememberToken($connexion) {
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    try {
        $token = $_COOKIE['remember_token'];
        
        // Vérifier si le token existe et est valide
        $stmt = $connexion->prepare("
            SELECT ID_Utilisateur, Email, ID_Role 
            FROM utilisateur 
            WHERE remember_token = :token 
            AND token_expiry > NOW()
        ");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Stocker les informations dans la session
            $_SESSION['user_id'] = $user['ID_Utilisateur'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_role'] = $user['ID_Role'];
            $_SESSION['loggedIn'] = true;
            
            // Renouveler le token
            createRememberToken($connexion, $user['ID_Utilisateur']);
            
            return true;
        }
    } catch (PDOException $e) {
        error_log("Erreur vérification token: " . $e->getMessage());
    }
    
    return false;
}

// Fonction pour créer un token "Se souvenir de moi"
function createRememberToken($connexion, $userId) {
    try {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        
        // Stocker le token dans la base de données
        $stmt = $connexion->prepare("
            UPDATE utilisateur 
            SET remember_token = :token, 
                token_expiry = DATE_ADD(NOW(), INTERVAL 30 DAY) 
            WHERE ID_Utilisateur = :id
        ");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        // Créer un cookie qui expire dans 30 jours
        setcookie("remember_token", $token, time() + (86400 * 30), "/");
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur création token: " . $e->getMessage());
        return false;
    }
}

// Préparation des variables pour la vue
$current_page = basename($_SERVER['PHP_SELF']);
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// Préparer les données pour la sidebar
$sidebar_data = getSidebarData($current_page, $user_role);

// Fonction pour préparer les données de navigation pour la sidebar
function getSidebarData($current_page, $user_role) {
    // Définition des liens de navigation
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

// Fonction pour déterminer si la page actuelle est une page de dashboard
function isDashboardActive($current_page) {
    $dashboard_pages = [
        'dashboard.php',
        'client_dashboard.php',
        'agent_dashboard.php',
        'fournisseur_dashboard.php'
    ];
    
    return in_array($current_page, $dashboard_pages);
}

// Fonction pour obtenir l'URL du dashboard selon le rôle
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