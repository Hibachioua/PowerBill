<?php
require_once __DIR__ . '/connexion.php';

class FactureModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getNonPayes() {
        $sql = "SELECT f.*, c.ID_Compteur, u.Nom, u.Prénom, fc.Qté_consommé 
                FROM facture f
                JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
                JOIN utilisateur u ON c.ID_Client = u.ID_Utilisateur
                JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
                WHERE f.Statut_paiement = 'non paye'
                ORDER BY f.Date_émission DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getNonPayes: " . $e->getMessage());
            return false;
        }
    }

    public function payerFacture($factureID) {
        $sql = "UPDATE facture SET Statut_paiement = 'paye', Date_paiement = NOW() 
                WHERE ID_Facture = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $factureID]);
        } catch (PDOException $e) {
            error_log("Error in payerFacture: " . $e->getMessage());
            return false;
        }
    }

    public function getDetails($factureID) {
        $sql = "SELECT f.*, c.ID_Compteur, u.*, fc.* 
                FROM facture f
                JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
                JOIN utilisateur u ON c.ID_Client = u.ID_Utilisateur
                JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
                WHERE f.ID_Facture = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $factureID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getDetails: " . $e->getMessage());
            return false;
        }
    }
}