<?php
require_once 'connexion.php';

function insererConsommation(
    int $ID_Compteur,
    int $Mois,
    int $Annee,
    float $Qté_consommé,
    string $cheminFichierTemp,
    PDO $pdo
): array {
    // Configuration du dossier d'upload
    $uploadDir = __DIR__ . '/../uploads/compteurs/';
    
    // Création du dossier si inexistant
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Erreur : Impossible de créer le dossier d'upload");
            return ['success' => false, 'message' => "Erreur système"];
        }
    }

    // Validation du fichier
    $maxFileSize = 10 * 1024 * 1024; // 10MB
    $fileSize = filesize($cheminFichierTemp);
    if ($fileSize > $maxFileSize) {
        return ['success' => false, 'message' => "L'image dépasse 10 Mo"];
    }

    // Génération d'un nom de fichier unique
    $fileExtension = pathinfo($_FILES['counterPicture']['name'], PATHINFO_EXTENSION);
    $filename = sprintf(
        'compteur_%d_%d_%d_%s.%s',
        $ID_Compteur,
        $Mois,
        $Annee,
        uniqid(),
        $fileExtension
    );
    $destinationPath = $uploadDir . $filename;

    // Déplacement du fichier
    if (!move_uploaded_file($cheminFichierTemp, $destinationPath)) {
        error_log("Erreur déplacement fichier: " . $_FILES['counterPicture']['error']);
        return ['success' => false, 'message' => "Erreur lors de l'enregistrement"];
    }

    // Chemin relatif pour la base de données
    $relativePath = 'uploads/compteurs/' . $filename;

    try {
        $pdo->beginTransaction();

        // Vérification de la consommation précédente
        $stmt_last = $pdo->prepare("
            SELECT Qté_consommé 
            FROM consommation 
            WHERE ID_Compteur = ? 
            ORDER BY ID_Consommation DESC 
            LIMIT 1
        ");
        $stmt_last->execute([$ID_Compteur]);
        $dernierEnregistrement = $stmt_last->fetch(PDO::FETCH_ASSOC);

        $status = "pas d'anomalie";
        $message = "Consommation enregistrée avec succès.";

        if ($dernierEnregistrement) {
            $dernierQté = (float)$dernierEnregistrement['Qté_consommé'];
            $seuilSup = $dernierQté * 1.4;
            $seuilInf = $dernierQté * 0.6;

            if ($Qté_consommé > $seuilSup || $Qté_consommé < $seuilInf) {
                $status = "anomalie";
                $message = "Anomalie détectée. La consommation a été enregistrée mais nécessite validation.";
            }
        }

        // Insertion dans la table consommation
        $stmt = $pdo->prepare("
            INSERT INTO consommation 
            (ID_Compteur, Mois, Annee, Qté_consommé, Image_Compteur, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $ID_Compteur,
            $Mois,
            $Annee,
            $Qté_consommé,
            $relativePath,
            $status
        ]);

        $ID_Consommation = $pdo->lastInsertId();
        $result = ['success' => true, 'message' => $message];

        // Création de la facture SEULEMENT si pas d'anomalie
        if ($status === "pas d'anomalie") {
            // Calcul du prix par tranches
            if ($Qté_consommé <= 100) {
                $prixHT = $Qté_consommé * 0.82;
            } 
            elseif ($Qté_consommé <= 150) {
                $prixHT = 100 * 0.82 + ($Qté_consommé - 100) * 0.92;
            } 
            else {
                $prixHT = 100 * 0.82 + 50 * 0.92 + ($Qté_consommé - 150) * 1.1;
            }

            $tauxTVA = 0.18; // TVA 18%
            $prixTTC = $prixHT * (1 + $tauxTVA);

            // Insertion dans la table facture
            $stmtFacture = $pdo->prepare("
                INSERT INTO facture 
                (ID_Compteur, ID_Consommation, Date_émission, Mois, Annee, Prix_HT, Prix_TTC, Statut_paiement)
                VALUES (?, ?, CURDATE(), ?, ?, ?, ?, 'non paye')
            ");
            $stmtFacture->execute([
                $ID_Compteur,
                $ID_Consommation,
                $Mois,
                $Annee,
                round($prixHT, 2),
                round($prixTTC, 2)
            ]);

            $factureID = $pdo->lastInsertId();
            $result['message'] .= " Facture #$factureID générée.";
            $result['factureID'] = $factureID;
        }

        $pdo->commit();
        return $result;

    } catch (PDOException $e) {
        $pdo->rollBack();
        if (file_exists($destinationPath)) {
            unlink($destinationPath);
        }
        error_log("Erreur PDO: " . $e->getMessage());
        return ['success' => false, 'message' => "Erreur technique: " . $e->getMessage()];
    }
}


function getLastCounterImage(int $compteurId): array {
    $baseUrl = '/powerbill/'; // À adapter à votre configuration
    
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