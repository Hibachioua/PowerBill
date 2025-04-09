<?php
require_once __DIR__ . "/../BD/connexion.php";

/**
 * Ajoute une nouvelle réclamation
 */
function ajouterReclamation($id_client, $type_reclamation, $description) { 
    $pdo = connectDB();
    if (!$pdo) {
        return false;
    }
    
    try {
        // Assurez-vous que les noms de colonnes correspondent exactement à ceux de votre base de données
        $sql = "INSERT INTO Réclamation (ID_Client, Type_Réclamation, Description, Date_Réclamation, Statut) 
                VALUES (?, ?, ?, NOW(), 'En cours')";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_client, $type_reclamation, $description]);
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout d'une réclamation: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère toutes les réclamations d'un client
 */
function getReclamationsByClient($id_client) {
    $pdo = connectDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        // Attention aux noms de colonnes et à la casse
        $sql = "SELECT ID_Réclamation, Type_Réclamation, Description, 
                DATE_FORMAT(Date_Réclamation, '%Y-%m-%d') as Date_Réclamation, 
                Statut, Réponse_Fournisseur 
                FROM Réclamation WHERE ID_Client = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_client]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur SQL lors de la récupération des réclamations: " . $e->getMessage());
        return [];
    }
}
?>