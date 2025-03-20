<?php

require_once "../BD/connexion.php";

// Fonction pour récupérer les factures non payées
function getNonPayeFactures() {
    global $pdo;  // Utiliser la connexion globale

    $sql = "
        SELECT 
            f.ID_Facture,
            f.Mois,
            f.Annee,
            c.ID_Compteur,
            u.Nom,
            u.Prénom,
            f.Date_émission,
            fc.Qté_consommé,
            f.Prix_HT,
            f.Prix_TTC,
            f.Statut_paiement
        FROM 
            facture f
        JOIN 
            compteur c ON f.ID_Compteur = c.ID_Compteur
        JOIN 
            utilisateur u ON c.ID_Client = u.ID_Utilisateur  
        JOIN 
            consommation fc ON f.ID_Consommation = fc.ID_Consommation
        WHERE 
            f.Statut_paiement = 'non paye';
    ";

    // Exécuter la requête avec la connexion globale
    $result = $pdo->query($sql);
    
    if ($result->rowCount() > 0) {
        $factures = [];
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $factures[] = [
                'ID_Facture' => $row['ID_Facture'],
                'Mois' => $row['Mois'],
                'Annee' => $row['Annee'],
                'ID_Compteur' => $row['ID_Compteur'],
                'Nom' => $row['Nom'],
                'Prenom' => $row['Prénom'],  
                'Date_émission' => $row['Date_émission'],
                'Consommation' => $row['Qté_consommé'],  
                'Prix_HT' => $row['Prix_HT'],
                'Prix_TTC' => $row['Prix_TTC'],
                'Statut_paiement' => $row['Statut_paiement']
            ];
        }

        return $factures;
    } else {
        return [];
    }
}

function updateFacturePayee($factureID) {
    global $pdo; 
    try {
        // Préparer la requête SQL
        $sql = "UPDATE facture SET Statut_paiement = 'paye' WHERE ID_Facture = :factureID";
        $stmt = $pdo->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':factureID', $factureID, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();

        // Vérifier si une ligne a été mise à jour
        if ($stmt->rowCount() > 0) {
            return true; // La mise à jour a réussi
        } else {
            error_log("Aucune ligne affectée, la facture n'existe peut-être pas ou est déjà payée.");
            return false; // Aucune ligne mise à jour
        }
    } catch (PDOException $e) {
        // En cas d'erreur, afficher le message d'erreur
        error_log("Erreur de mise à jour de la facture : " . $e->getMessage());
        return false;
    }
}

?>

