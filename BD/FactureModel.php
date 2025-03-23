<?php
require_once "../BD/connexion.php";

class FactureModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect(); // Utilisation de la connexion unique
    }

    // Récupérer les factures non payées
    public function getNonPayes() {
        $sql = "SELECT f.ID_Facture, f.Mois, f.Annee, c.ID_Compteur, 
                       u.Nom, u.Prénom, f.Date_émission, fc.Qté_consommé, 
                       f.Prix_HT, f.Prix_TTC, f.Statut_paiement
                FROM facture f
                JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
                JOIN utilisateur u ON c.ID_Client = u.ID_Utilisateur
                JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
                WHERE f.Statut_paiement = 'non paye'";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour l'état de la facture à "payée"
    public function updateFacturePayee($factureID) {
        $sql = "UPDATE facture SET Statut_paiement = 'paye' WHERE ID_Facture = :factureID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':factureID', $factureID, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getFacturesPayees() {
        $sql = "SELECT f.ID_Facture, f.Mois, f.Annee, c.ID_Compteur, 
                       u.Nom, u.Prénom, f.Date_émission, fc.Qté_consommé, 
                       f.Prix_HT, f.Prix_TTC, f.Statut_paiement
                FROM facture f
                JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
                JOIN utilisateur u ON c.ID_Client = u.ID_Utilisateur
                JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
                WHERE f.Statut_paiement = 'paye'";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}


?>
