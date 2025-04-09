<?php 
require_once __DIR__ . '/connexion.php';

function getNonPayes($pdo) {
    $sql = "
    SELECT 
        f.ID_Facture,
        'principale' AS type,
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

    UNION ALL

    SELECT 
        fc.ID_Facture_Complementaire AS ID_Facture,
        'complementaire' AS type,
        fc.ID_Compteur,
        NULL AS ID_Consommation,
        fc.Date_emission AS Date_émission,
        NULL AS Mois,
        fcs.Annee,
        fc.Prix_HT,
        fc.Prix_TTC,
        fc.Statut_paiement,
        cp2.ID_Client,
        cl2.Nom,
        cl2.Prenom,
        fc.Consommation AS Qté_consommé
    FROM facture_complementaire fc
    JOIN compteur cp2 ON fc.ID_Compteur = cp2.ID_Compteur
    JOIN client cl2 ON cp2.ID_Client = cl2.ID_Client
    JOIN fichier_consommation fcs ON fc.ID_Compteur = fcs.ID_Compteur
    WHERE fc.Statut_paiement = 'non payee'
    ORDER BY Date_émission DESC;
    ";

    try {
        $stmt = $pdo->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Factures récupérées: " . json_encode($result));
        return $result;
    } catch (Exception $e) {
        error_log("Erreur dans getNonPayes: " . $e->getMessage());
        return [];
    }
}

function payerFacture($pdo, $factureID, $type) {
    if ($type === 'principale') {
        $update = $pdo->prepare("UPDATE facture SET Statut_paiement = 'paye' WHERE ID_Facture = :id");
    } elseif ($type === 'complementaire') {
        $update = $pdo->prepare("UPDATE facture_complementaire SET Statut_paiement = 'payee' WHERE ID_Facture_Complementaire = :id");
    } else {
        return false; // type inconnu
    }

    return $update->execute([':id' => $factureID]);
}
?>