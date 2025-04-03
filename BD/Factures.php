<?php
class FactureModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getFactures() {
        $sql = "SELECT f.ID_Facture, f.Mois, f.Annee, c.ID_Compteur, 
               cl.Nom, cl.Prenom, f.Date_émission, fc.Qté_consommé, 
               f.Prix_HT, f.Prix_TTC, f.Statut_paiement
        FROM facture f
        JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
        JOIN client cl ON c.ID_Client = cl.ID_Client
        JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation";

        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function payerFacture($factureID) {
        $sql = "UPDATE facture SET Statut_paiement = 'paye' WHERE ID_Facture = :factureID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':factureID', $factureID, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>