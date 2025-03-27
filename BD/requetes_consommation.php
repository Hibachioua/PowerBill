<?php
require_once 'connexion.php'; // Assurez-vous que cette connexion est correcte
function insererConsommation(
    PDO $pdo,
    int $ID_Compteur,
    int $Mois,
    int $Annee,
    float $Qté_consommé,
    string $cheminFichierTemp // $_FILES['counterPicture']['tmp_name']
): bool {
    // Lire le contenu binaire du fichier
    $contenuImage = file_get_contents($cheminFichierTemp);
    if ($contenuImage === false) {
        error_log("Erreur: Impossible de lire le fichier image");
        return false;
    }

    $sql = "INSERT INTO consommation 
            (ID_Compteur, Mois, Annee, Qté_consommé, Image_Compteur) 
            VALUES (?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $ID_Compteur,
            $Mois,
            $Annee,
            $Qté_consommé,
            $contenuImage // Données binaires
        ]);
    } catch (PDOException $e) {
        error_log("Erreur insertion: " . $e->getMessage());
        return false;
    }
}
function getLastCounterImage(PDO $pdo, int $compteurId): array {
    // Vérification de l'ID Compteur
    if ($compteurId <= 0) {
        error_log("Erreur : ID Compteur invalide ($compteurId)");
        return ['success' => false, 'error' => 'ID Compteur invalide'];
    }

    // Requête SQL pour récupérer la dernière image associée à un compteur
    $sql = "SELECT Image_Compteur, 
                   DATE_FORMAT(STR_TO_DATE(CONCAT(Annee, '-', Mois, '-01'), '%Y-%m-%d'), '%Y-%m') as date_prise 
            FROM consommation 
            WHERE ID_Compteur = :compteurId
            ORDER BY ID_Consommation DESC 
            LIMIT 1";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':compteurId', $compteurId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            error_log("Erreur lors de l'exécution de la requête SQL");
            return ['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête'];
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            error_log("Aucun enregistrement trouvé pour ID_Compteur = $compteurId");
            return ['success' => false, 'error' => 'Aucune image trouvée'];
        }

        if (empty($result['Image_Compteur'])) {
            error_log("Image vide pour ID_Compteur = $compteurId");
            return ['success' => false, 'error' => 'Image introuvable'];
        }

        // Encoder l'image en base64
        $imageData = base64_encode($result['Image_Compteur']);

        return [
            'success' => true,
            'image_data' => $imageData,
            'date' => $result['date_prise'] ?? 'Date inconnue',
            'content_type' => 'image/png'
        ];

    } catch (PDOException $e) {
        error_log("Erreur PDO : " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur interne du serveur'];
    }
}
