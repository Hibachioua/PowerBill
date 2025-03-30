<?php
require_once "../BD/user_model.php";


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
                
                // Stocker le message dans la session
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_message_type'] = $result['success'] ? 'success' : 'warning';
                
                // Rediriger pour éviter la résoumission du formulaire
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
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
                    
                    // Stocker le message dans la session
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_message_type'] = $result['success'] ? 'success' : 'warning';
                    
                    // Rediriger pour éviter la résoumission du formulaire
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
                break;
                
            case 'delete':
                if (isset($_POST['user_id'])) {
                    $userId = intval($_POST['user_id']);
                    $currentUserId = $_SESSION['user_id'];
                    
                    // Appeler la fonction du modèle
                    $result = deleteUser($userId, $currentUserId);
                    
                    // Stocker le message dans la session
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_message_type'] = $result['success'] ? 'success' : 'danger';
                    
                    // Rediriger pour éviter la résoumission du formulaire
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
                break;
                
            default:
                $_SESSION['flash_message'] = 'Action non reconnue';
                $_SESSION['flash_message_type'] = 'danger';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
        }
    }
    
    // Récupérer les messages de la session
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $messageType = $_SESSION['flash_message_type'];
        
        // Supprimer les messages de la session après les avoir récupérés
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_message_type']);
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