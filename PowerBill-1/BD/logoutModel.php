<?php
require_once "../BD/connexion.php";

/**
 * 
 * @param int 
 * @return bool
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
?>