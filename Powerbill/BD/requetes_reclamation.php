<?php
require_once '../BD/connexion.php';

function ajouterReclamation($id_client, $type_reclamation, $description) { 
    global $pdo;
    $sql ="INSERT INTO réclamation (id_client, type_réclamation, description, date_réclamation) VALUES (?, ?, ?, NOW())";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$id_client, $type_reclamation, $description]);
}
?>
