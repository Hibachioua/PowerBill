<?php
// BD/logoutModel.php - Gère l'accès aux données pour la déconnexion
require_once "connexion.php";

/**
 * Supprime le token de mémorisation pour un utilisateur
 * @param int $userId ID de l'utilisateur
 * @return bool Succès ou échec
 */
function removeRememberToken($userId) {
    $connexion = connectDB();
    if (!$connexion) {
        return false;
    }
    
    try {
        $stmt = $connexion->prepare("
            UPDATE utilisateur 
            SET remember_token = NULL, 
                token_expiry = NULL 
            WHERE ID_Utilisateur = :id
        ");
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur de suppression du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un cookie de mémorisation existe
 * @return bool True si le cookie existe, sinon false
 */
function hasRememberCookie() {
    return isset($_COOKIE['remember_token']);
}

/**
 * Supprime le cookie de mémorisation
 * @return void
 */
function removeRememberCookie() {
    if (hasRememberCookie()) {
        setcookie("remember_token", "", time() - 3600, "/");
    }
}

/**
 * Vérifie si la session est active
 * @return bool True si la session est active, sinon false
 */
function isSessionActive() {
    return session_status() == PHP_SESSION_ACTIVE;
}

/**
 * Obtient l'ID de l'utilisateur actuellement connecté
 * @return int|null L'ID de l'utilisateur ou null s'il n'est pas connecté
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Vérifie si un cookie de session existe
 * @return bool True si le cookie existe, sinon false
 */
function hasSessionCookie() {
    return isset($_COOKIE[session_name()]);
}

/**
 * Supprime le cookie de session
 * @return void
 */
function removeSessionCookie() {
    if (hasSessionCookie()) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
}
?>