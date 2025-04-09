<?php
require_once "connexion.php";

function getAnomalies($pdo) {
    $sql = "SELECT 
            c.ID_Client,
            co.ID_Compteur,
            curr.Mois AS Mois_actuel,
            curr.Annee AS Annee_actuel,
            prev.Qté_consommé AS Consommation_precedent,
            prev.Image_Compteur AS Image_precedent,
            curr.Qté_consommé AS Consommation_actuelle,
            curr.Image_Compteur AS Image_actuelle,
            curr.ID_Consommation
        FROM consommation curr
        JOIN consommation prev ON 
            curr.ID_Compteur = prev.ID_Compteur AND
            (
                (curr.Mois = 1 AND prev.Mois = 12 AND curr.Annee = prev.Annee + 1) OR
                (curr.Mois = prev.Mois + 1 AND curr.Annee = prev.Annee)
            )
        JOIN compteur co ON curr.ID_Compteur = co.ID_Compteur
        JOIN client c ON co.ID_Client = c.ID_Client
        WHERE curr.status = 'anomalie'
        ORDER BY curr.Annee DESC, curr.Mois DESC"; 

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function corrigerAnomalie($pdo, $idConsommation, $nouvelleConsommation, $mois, $annee, $idCompteur) {
    $pdo->beginTransaction();
    try {
        // Mise à jour de la consommation
        $sqlUpdate = "UPDATE consommation 
                     SET Qté_consommé = :consommation, status = 'pas d\'anomalie' 
                     WHERE ID_Consommation = :id";
        $stmt = $pdo->prepare($sqlUpdate);
        $stmt->execute([
            ':consommation' => $nouvelleConsommation,
            ':id' => $idConsommation
        ]);

        // Calcul des prix
        $prixHT = _calculerPrixHT($nouvelleConsommation);
        $prixTTC = $prixHT * 1.18;

        // Création de la facture
        $sqlFacture = "INSERT INTO facture 
                      (ID_Compteur, ID_Consommation, Date_émission, Mois, Annee, Prix_HT, Prix_TTC, Statut_paiement)
                      VALUES 
                      (:idCompteur, :idConsommation, NOW(), :mois, :annee, :prixHT, :prixTTC, 'non paye')";
        $stmtFacture = $pdo->prepare($sqlFacture);
        $stmtFacture->execute([
            ':idCompteur' => $idCompteur,
            ':idConsommation' => $idConsommation,
            ':mois' => $mois,
            ':annee' => $annee,
            ':prixHT' => $prixHT,
            ':prixTTC' => $prixTTC
        ]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function _calculerPrixHT($consommation) {
    if ($consommation <= 100) {
        return $consommation * 0.82;
    } elseif ($consommation <= 150) {
        return 100 * 0.82 + ($consommation - 100) * 0.92;
    } else {
        return 100 * 0.82 + 50 * 0.92 + ($consommation - 150) * 1.1;
    }
}
?>