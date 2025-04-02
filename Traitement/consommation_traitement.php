<?php
require_once '../BD/connexion.php';
require_once '../BD/requetes_consommation.php';

// Configuration
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

// Nettoyage du buffer
while (ob_get_level()) ob_end_clean();

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validation des entrées
        $ID_Compteur = filter_input(INPUT_POST, 'ID_Compteur', FILTER_VALIDATE_INT);
        $Mois = date('n');
        $Annee = date('Y');
        $Qte = filter_input(INPUT_POST, 'Qté_consommé', FILTER_VALIDATE_FLOAT);

        if (!$ID_Compteur || !$Qte) {
            throw new Exception("Données invalides");
        }

        // Validation du fichier
        if (!isset($_FILES['counterPicture'])){
            throw new Exception("Aucun fichier uploadé");
        }

        $file = $_FILES['counterPicture'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur upload: " . $file['error']);
        }

        $typeMime = mime_content_type($file['tmp_name']);
        if (!in_array($typeMime, ['image/jpeg', 'image/png', 'image/gif'])) {
            throw new Exception("Type de fichier non supporté");
        }

        // Connexion DB
        $pdo = connectDB();
        if (!$pdo) {
            throw new Exception("Connexion DB échouée");
        }

        // Traitement
        $resultat = insererConsommation(
            $ID_Compteur,
            $Mois,
            $Annee,
            $Qte,
            $file['tmp_name'],
            $pdo
        );

        header("Location: ../IHM/saisie_consommation.php?message=" . urlencode($resultat['message']));
        exit;

    } catch (Exception $e) {
        error_log("Erreur traitement: " . $e->getMessage());
        header("Location: ../IHM/saisie_consommation.php?message=" . urlencode("Erreur: " . $e->getMessage()));
        exit;
    }
}

// Traitement GET (récupération image)
if (isset($_GET['action']) && $_GET['action'] === 'get_last_image') {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_GET['compteur_id'])) {
            throw new Exception('Paramètre manquant');
        }

        $compteurId = (int)$_GET['compteur_id'];
        if ($compteurId <= 0) {
            throw new Exception('ID invalide');
        }

        $imageData = getLastCounterImage($compteurId);
        
        if (!$imageData['success']) {
            throw new Exception($imageData['error']);
        }

        echo json_encode([
            'success' => true,
            'image_url' => $imageData['image_url'],
            'date' => $imageData['date']
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

// Requête non reconnue
http_response_code(400);
die("Requête invalide");
?>