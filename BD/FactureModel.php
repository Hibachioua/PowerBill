<?php
require_once __DIR__ . '/connexion.php';

class FactureModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getNonPayes() {
        $sql = "
        SELECT 
        f.ID_Facture,
        f.ID_Compteur,
        f.ID_Consommation,
        f.Date_émission,
        f.Mois,
        f.Annee,
        f.Prix_HT,
        f.Prix_TTC,
        f.Statut_paiement,
        cp.ID_Client,
        cl.Nom,
        cl.Prenom,   
        co.Qté_consommé
    FROM facture f
    JOIN consommation co ON f.ID_Consommation = co.ID_Consommation
    JOIN compteur cp ON f.ID_Compteur = cp.ID_Compteur
    JOIN client cl ON cp.ID_Client = cl.ID_Client
    WHERE f.Statut_paiement = 'non paye'
    ORDER BY f.Date_émission DESC;
        ";
    
        try {
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Log des résultats pour le débogage
            error_log("Factures récupérées: " . json_encode($result));
    
            return $result;
        } catch (Exception $e) {
            error_log("Erreur dans getNonPayes: " . $e->getMessage());
            return [];
        }
    }
    
    public function payerFacture($factureID) {
        $sql = "UPDATE facture SET Statut_paiement = 'paye', Date_paiement = NOW() 
                WHERE ID_Facture = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $factureID]);
    }

    public function getDetails($factureID) {
        $sql = "SELECT f.*, c.ID_Compteur, cl.Nom, cl.Prenom, u.Email, fc.* 
                FROM facture f
                JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
                JOIN client cl ON c.ID_Client = cl.ID_Client
                JOIN utilisateur u ON cl.ID_Utilisateur = u.ID_Utilisateur
                JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
                WHERE f.ID_Facture = :id";
        
        // Préparer la requête
        $stmt = $this->pdo->prepare($sql);
        
        // Exécuter la requête avec le paramètre :id
        $stmt->execute([':id' => $factureID]);
        
        // Récupérer et retourner les résultats sous forme de tableau associatif
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    
}