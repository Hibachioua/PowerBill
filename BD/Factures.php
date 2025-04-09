<?php
require_once "connexion.php";

function getNonPayes($pdo, $clientId) {
    $sql = "SELECT 
    f.ID_Facture, 
    'facture' AS type,   -- Ajout d'un type pour la première partie de la requête
    f.Mois, 
    f.Annee, 
    c.ID_Compteur,  
    cl.Nom, 
    cl.Prenom, 
    f.Date_émission, 
    fc.Qté_consommé, 
    f.Prix_HT, 
    f.Prix_TTC, 
    f.Statut_paiement
FROM facture f
JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
JOIN client cl ON c.ID_Client = cl.ID_Client
JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
WHERE cl.ID_Client = :clientId 
AND f.Statut_paiement = 'non paye'

UNION ALL

SELECT 
    fc.ID_Facture_Complementaire AS ID_Facture,
    'complementaire' AS type,
    NULL AS Mois,         
    fcs.Annee,
    fc.ID_Compteur,
    cl2.Nom,
    cl2.Prenom,
    fc.Date_emission AS Date_émission,
    fc.Consommation AS Qté_consommé,
    fc.Prix_HT,
    fc.Prix_TTC,
    fc.Statut_paiement
    
FROM facture_complementaire fc
JOIN compteur cp2 ON fc.ID_Compteur = cp2.ID_Compteur
JOIN client cl2 ON  cl2.ID_Client = :clientId 
JOIN fichier_consommation fcs ON fc.ID_Compteur = fcs.ID_Compteur
WHERE fc.Statut_paiement = 'non payee'

ORDER BY Date_émission DESC";


    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function payerFacture($pdo, $factureID) {
    $sql = "UPDATE facture SET Statut_paiement = 'paye' WHERE ID_Facture = :factureID";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':factureID' => $factureID]);
}


function getFacturesPayees($pdo, $clientId) {
    $sql = "SELECT 
    f.ID_Facture, 
    'facture' AS type,   -- Ajout d'un type pour la première partie de la requête
    f.Mois, 
    f.Annee, 
    c.ID_Compteur,  
    cl.Nom, 
    cl.Prenom, 
    f.Date_émission, 
    fc.Qté_consommé, 
    f.Prix_HT, 
    f.Prix_TTC, 
    f.Statut_paiement
FROM facture f
JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
JOIN client cl ON c.ID_Client = cl.ID_Client
JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
WHERE cl.ID_Client = :clientId 
AND f.Statut_paiement = 'paye'

UNION ALL

SELECT 
    fc.ID_Facture_Complementaire AS ID_Facture,
    'complementaire' AS type,
    NULL AS Mois,                -- Ajout d'une colonne NULL pour correspondre à la structure
    fcs.Annee,
    fc.ID_Compteur,
    cl2.Nom,
    cl2.Prenom,
    fc.Date_emission AS Date_émission,
    fc.Consommation AS Qté_consommé,
    fc.Prix_HT,
    fc.Prix_TTC,
    fc.Statut_paiement
    
FROM facture_complementaire fc
JOIN compteur cp2 ON fc.ID_Compteur = cp2.ID_Compteur
JOIN client cl2 ON  cl2.ID_Client = :clientId 
JOIN fichier_consommation fcs ON fc.ID_Compteur = fcs.ID_Compteur
WHERE fc.Statut_paiement = 'payee'

ORDER BY Date_émission DESC";


    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
function getDetailsFacture($pdo, $factureID) {
    $sql = "SELECT f.*, c.ID_Compteur, cl.Nom, cl.Prenom,cl.Adresse,cl.CIN, u.Email, fc.* 
            FROM facture f
            JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
            JOIN client cl ON c.ID_Client = cl.ID_Client
            JOIN utilisateur u ON cl.ID_Utilisateur = u.ID_Utilisateur
            JOIN consommation fc ON f.ID_Consommation = fc.ID_Consommation
            WHERE f.ID_Facture = :id";
    
    // Prepare the SQL query
    $stmt = $pdo->prepare($sql);
    
    // Execute the query with the provided factureID
    $stmt->execute([':id' => $factureID]);

    // Fetch the result as an associative array
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
}


function getDetailsFactureComplementaire($pdo, $factureID) {
    $sql = "SELECT f.*, c.ID_Compteur, cl.Nom, cl.Prenom, cl.Adresse, cl.CIN, u.Email 
            FROM facture_complementaire f
            JOIN compteur c ON f.ID_Compteur = c.ID_Compteur
            JOIN client cl ON c.ID_Client = cl.ID_Client
            JOIN utilisateur u ON cl.ID_Utilisateur = u.ID_Utilisateur
            WHERE f.ID_Facture_Complementaire = :id";
    
    // Le reste du code reste inchangé
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $factureID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>