<?php
require_once "../BD/user_model.php";

/**
 * Traite les actions sur les utilisateurs
 */
function processUserAction() {
    $message = '';
    $messageType = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'add':
                // Récupérer et valider les données
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                $nom = htmlspecialchars($_POST['nom']);
                $prenom = htmlspecialchars($_POST['prenom']);
                $adresse = htmlspecialchars($_POST['adresse']);
                
                // Appeler la fonction du modèle
                $result = addUser($nom, $prenom, $email, $password, $adresse);
                
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'warning';
                break;

            case 'edit':
                if (isset($_POST['user_id'])) {
                    // Récupérer et valider les données
                    $userId = intval($_POST['user_id']);
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    $password = $_POST['password'];
                    $nom = htmlspecialchars($_POST['nom']);
                    $prenom = htmlspecialchars($_POST['prenom']);
                    $adresse = htmlspecialchars($_POST['adresse']);
                    
                    // Appeler la fonction du modèle
                    $result = updateUser($userId, $nom, $prenom, $email, $password, $adresse);
                    
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'warning';
                }
                break;
                
            case 'delete':
                if (isset($_POST['user_id'])) {
                    $userId = intval($_POST['user_id']);
                    $currentUserId = $_SESSION['user_id'];
                    
                    // Appeler la fonction du modèle
                    $result = deleteUser($userId, $currentUserId);
                    
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'danger';
                }
                break;
                
            default:
                $message = 'Action non reconnue';
                $messageType = 'danger';
        }
    }
    
    return [
        'message' => $message,
        'messageType' => $messageType
    ];
}

/**
 * Prépare les données pour la vue
 */
function prepareUserData() {
    // Récupérer les utilisateurs et les rôles
    $users = getAllUsers();
    $roles = getAllRoles();
    
    return [
        'users' => $users,
        'roles' => $roles
    ];
}
?>