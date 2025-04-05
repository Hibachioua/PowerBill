<?php
require_once __DIR__.'/connexion.php';

function getCompteursClient($userId) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT compteur.ID_Compteur 
                         FROM compteur
                         JOIN client ON compteur.ID_Client = client.ID_Client
                         WHERE client.ID_Utilisateur = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLastCounterImage(int $compteurId): array {
    $baseUrl = '/powerbill/'; 
    try {
        $pdo = connectDB();
        if (!$pdo) {
            throw new Exception("Connexion DB échouée");
        }

        $stmt = $pdo->prepare("
            SELECT Image_Compteur, 
                   CONCAT(Annee, '-', LPAD(Mois, 2, '0')) as date_prise
            FROM consommation 
            WHERE ID_Compteur = ?
            ORDER BY ID_Consommation DESC 
            LIMIT 1
        ");
        $stmt->execute([$compteurId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result['Image_Compteur'])) {
            throw new Exception("Aucune image trouvée en base");
        }

        $imagePath = __DIR__ . '/../' . $result['Image_Compteur'];
        if (!file_exists($imagePath)) {
            throw new Exception("Fichier introuvable: " . $imagePath);
        }

        return [
            'success' => true,
            'image_url' => $baseUrl . $result['Image_Compteur'],
            'date' => $result['date_prise']
        ];

    } catch (Exception $e) {
        error_log("Erreur getLastCounterImage: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
function insererConsommation($data) {
    $pdo = connectDB();
    $pdo->beginTransaction();

    try {
        // Insertion consommation
        $stmt = $pdo->prepare("INSERT INTO consommation 
                             (ID_Compteur, Mois, Annee, Qté_consommé, Image_Compteur, status)
                             VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['compteurId'],
            $data['mois'],
            $data['annee'],
            $data['quantite'],
            $data['imagePath'],
            $data['status']
        ]);
        $consommationId = $pdo->lastInsertId();

        // Création facture
        $stmt = $pdo->prepare("INSERT INTO facture
                             (ID_Compteur, ID_Consommation, Date_émission, Mois, Annee, Prix_HT, Prix_TTC, Statut_paiement)
                             VALUES (?, ?, NOW(), ?, ?, ?, ?, 'non paye')");
        $stmt->execute([
            $data['compteurId'],
            $consommationId,
            $data['mois'],
            $data['annee'],
            $data['prixHT'],
            $data['prixTTC']
        ]);

        $pdo->commit();
        return ['success' => true, 'factureID' => $pdo->lastInsertId()];

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function verifierCompteurClient($userId, $compteurId) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT 1 FROM compteur c
                         JOIN client cl ON c.ID_Client = cl.ID_Client
                         WHERE cl.ID_Utilisateur = ? AND c.ID_Compteur = ?");
    $stmt->execute([$userId, $compteurId]);
    return (bool)$stmt->fetch();
}
?>