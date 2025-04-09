<?php
require_once __DIR__ . "/../connexion.php";

$conn = connectDB(); 

function getReclamationsEnCours($conn) {
    $sql = "SELECT r.ID_Réclamation, r.Type_Réclamation, r.Description, r.Date_Réclamation, 
                   r.Statut, r.Réponse_Fournisseur, 
                   c.Nom, c.Prenom
            FROM réclamation r
            JOIN client c ON r.ID_Client = c.ID_Client
            ORDER BY r.Date_Réclamation DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function updateReclamation($conn, $id, $reponse) {
    $sql = "UPDATE `réclamation` 
            SET `Réponse_Fournisseur` = :reponse, 
                `Statut` = 'Traité'
            WHERE `ID_Réclamation` = :id";

    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':reponse' => $reponse,
        ':id' => $id
    ]);
}

function countReclamationsByStatus($conn, $status) {
    $sql = "SELECT COUNT(*) as count FROM réclamation WHERE Statut = :status";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':status' => $status]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

?>
