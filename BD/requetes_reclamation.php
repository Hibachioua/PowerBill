<?php
include  'connexion.php';

$conn = DB::connect();
if ($conn === null) {
    die("Échec de la connexion à la base de données.");
}

function insererReclamation($conn, $id_client, $type_reclamation, $description) {
    // Obtenir la date du système
    $date_reclamation = date('Y-m-d'); 

    // Préparer la requête d'insertion avec la date du système
    $sql = "INSERT INTO reclamation (ID_Client, Type_Reclamation, Description, Date_Reclamation) 
            VALUES (:id_client, :type_reclamation, :description, :date_reclamation)";

    // Préparer la requête avec PDO
    try {
        $stmt = $conn->prepare($sql);
        
        // Lier les paramètres
        $stmt->bindParam(':id_client', $id_client, PDO::PARAM_INT);
        $stmt->bindParam(':type_reclamation', $type_reclamation, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':date_reclamation', $date_reclamation, PDO::PARAM_STR);

        // Exécuter la requête
        if ($stmt->execute()) {
            return "Réclamation insérée avec succès.";
        } else {
            // Récupérer l'erreur d'exécution
            $errorInfo = $stmt->errorInfo();
            return "Erreur lors de l'insertion : " . $errorInfo[2];
        }
    } catch (PDOException $e) {
        return "Erreur lors de l'exécution de la requête : " . $e->getMessage();
    }
}


