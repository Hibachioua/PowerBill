<?php
require_once "connexion.php";



// Fonction pour récupérer l'ID_Agent basé sur user_id
function getAgentIdByUserId($pdo, $userId) {
    $sql = "SELECT ID_Agent FROM agent WHERE ID_Utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $agent = $stmt->fetch();
    
    if ($agent) {
        return $agent['ID_Agent'];
    } else {
        return null;  // Retourne null si l'agent n'est pas trouvé
    }
}

// Fonction pour insérer la consommation
function insertConsommation($pdo, $compteurId, $consommation, $annee, $agentId, $fileName) {
    $sql = "INSERT INTO fichier_consommation (ID_Compteur, Consommation, Annee, ID_Agent, Date_creation, Chemin_Fichier)
            VALUES (?, ?, ?, ?, CURDATE(), ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $compteurId, PDO::PARAM_INT);
    $stmt->bindParam(2, $consommation, PDO::PARAM_STR);
    $stmt->bindParam(3, $annee, PDO::PARAM_INT);
    $stmt->bindParam(4, $agentId, PDO::PARAM_INT);
    $stmt->bindParam(5, $fileName, PDO::PARAM_STR);

    return $stmt->execute();
}



