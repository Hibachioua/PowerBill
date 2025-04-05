<?php
require_once __DIR__ . "/../BD/connexion.php";
$pdo = connectDB();
    function ajouterReclamation($id_client, $type_reclamation, $description) { 
        global $pdo;
        if (!$pdo) {
            die("Connexion PDO non initialisée !");
        }
        $sql ="INSERT INTO réclamation (id_client, type_réclamation, description, date_réclamation) VALUES (?, ?, ?, NOW())";
        $stmt= $pdo->prepare($sql);
        return $stmt->execute([$id_client, $type_reclamation, $description]);
    }

    function getReclamationsByClient($id_client) {
        global $pdo;
        if (!$pdo) {
            die("Connexion PDO non initialisée !");
        }
        $sql = "SELECT ID_Réclamation, Type_Réclamation, Description, Date_Réclamation, Statut, Réponse_Fournisseur 
                FROM réclamation WHERE ID_Client = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_client]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
       
     

?>
