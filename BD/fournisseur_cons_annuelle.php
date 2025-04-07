<?php
require_once "connexion.php";
function getConsommations($pdo) {
    $sql = "SELECT 
    cons.Annee,
    cl.ID_Client,
    cp.ID_Compteur,
    CONCAT(cl.Nom, ' ', cl.Prenom) AS NomComplet,
    SUM(cons.Qté_consommé) AS ConsommationReelle,
    MAX(fc.Consommation) AS ConsommationSaisie,
    ABS(SUM(cons.Qté_consommé) - MAX(fc.Consommation)) AS Difference,
    (SELECT COUNT(*) FROM facture_complementaire 
     WHERE ID_Compteur = cp.ID_Compteur AND YEAR(Date_emission) = cons.Annee) AS factureExist
FROM client cl
JOIN compteur cp 
    ON cl.ID_Client = cp.ID_Client
JOIN consommation cons 
    ON cp.ID_Compteur = cons.ID_Compteur
LEFT JOIN fichier_consommation fc 
    ON cp.ID_Compteur = fc.ID_Compteur
    AND cons.Annee = fc.Annee
GROUP BY cons.Annee, cl.ID_Client, cp.ID_Compteur
ORDER BY cons.Annee DESC ";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function genererFacture($pdo) {
    if (empty($_GET['clientId']) || empty($_GET['compteurId']) || empty($_GET['year'])) {
        return ['success' => false, 'error' => 'Paramètres manquants'];
    }

    $clientId = $_GET['clientId'];
    $compteurId = $_GET['compteurId'];
    $year = $_GET['year'];

    // Vérification si une facture a déjà été générée pour le client, le compteur et l'année
    $sqlCheck = "
        SELECT COUNT(*) FROM facture_complementaire 
        WHERE ID_Compteur = :compteurId AND YEAR(Date_emission) = :year
    ";

    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':compteurId', $compteurId, PDO::PARAM_INT);
    $stmtCheck->bindParam(':year', $year, PDO::PARAM_INT);
    $stmtCheck->execute();
    $factureExist = $stmtCheck->fetchColumn();

    if ($factureExist > 0) {
        return ['success' => false, 'message' => 'Cette facture a déjà été générée pour cette période.'];
    }

    // Calcul de la consommation et de la différence
    $sql = "
        SELECT 
            SUM(cons.Qté_consommé) AS ConsommationReelle,
            MAX(fc.Consommation) AS ConsommationSaisie,
            ABS(SUM(cons.Qté_consommé) - MAX(fc.Consommation)) AS Difference
        FROM client cl
        JOIN compteur cp ON cl.ID_Client = cp.ID_Client
        JOIN consommation cons ON cp.ID_Compteur = cons.ID_Compteur
        LEFT JOIN fichier_consommation fc ON cp.ID_Compteur = fc.ID_Compteur
        WHERE cl.ID_Client = :clientId AND cp.ID_Compteur = :compteurId AND cons.Annee = :year
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
    $stmt->bindParam(':compteurId', $compteurId, PDO::PARAM_INT);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Récupérer la consommation réelle
        $consommation = $result['Difference'];

        // Calcul du prix HT basé sur la consommation
        if ($consommation <= 100) { 
            $prixHT = $consommation * 0.82;
        } elseif ($consommation <= 150) {
            $prixHT = 100 * 0.82 + ($consommation - 100) * 0.92;
        } else {
            $prixHT = 100 * 0.82 + 50 * 0.92 + ($consommation - 150) * 1.1;
        }

        // Calcul du prix TTC (en ajoutant la TVA de 20% par exemple)
        $prixTTC = $prixHT * 1.2;

        // Insérer la nouvelle ligne dans la table facture_complementaire
        $sqlInsert = "
            INSERT INTO facture_complementaire (ID_Facture_Complementaire, ID_Compteur, Date_emission, Consommation, Prix_HT, Prix_TTC, Statut_paiement)
            VALUES (NULL, :compteurId, NOW(), :consommation, :prixHT, :prixTTC, 'non payee')
        ";

        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->bindParam(':compteurId', $compteurId, PDO::PARAM_INT);
        $stmtInsert->bindParam(':consommation', $consommation, PDO::PARAM_STR);
        $stmtInsert->bindValue(':prixHT', $prixHT, PDO::PARAM_INT);
        $stmtInsert->bindValue(':prixTTC', $prixTTC, PDO::PARAM_INT);
        $stmtInsert->execute();

        return ['success' => true, 'message' => 'Facture envoyée au client avec succès'];
    } else {
        return ['success' => false, 'error' => 'Aucune donnée trouvée pour la facture'];
    }
}
?>
