<?php
require_once "../BD/connexion.php";

$connexion = connectDB();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Supprimer le token de la base de données
if (isset($_SESSION['user_id']) && $connexion) {
    try {
        $stmt = $connexion->prepare("
            UPDATE utilisateur 
            SET remember_token = NULL, 
                token_expiry = NULL 
            WHERE ID_Utilisateur = :id
        ");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur déconnexion: " . $e->getMessage());
    }
}

if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
session_destroy();

header("Location: ../IHM/index.php");
exit();
?>