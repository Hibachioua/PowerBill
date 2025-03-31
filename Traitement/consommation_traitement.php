<?php
require_once '../BD/connexion.php'; 
require_once '../BD/requetes_consommation.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (ob_get_length() > 0) {
    ob_clean();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ID_Compteur = filter_input(INPUT_POST, 'ID_Compteur', FILTER_VALIDATE_INT);
    $Mois = date('n'); 
    $Annee = date('Y'); 
    $Qte = filter_input(INPUT_POST, 'Qté_consommé', FILTER_VALIDATE_FLOAT);

    if (!isset($_FILES['counterPicture']) || $_FILES['counterPicture']['error'] !== UPLOAD_ERR_OK) {
        header("Location: ../IHM/saisie_consommation.php?message=" . urlencode("Erreur lors de l'upload de l'image"));
        exit;
    }
    $typeMime = mime_content_type($_FILES['counterPicture']['tmp_name']);
    if (!in_array($typeMime, ['image/jpeg', 'image/png', 'image/gif'])) {
        header("Location: ../IHM/saisie_consommation.php?message=" . urlencode("Type de fichier non supporté"));
        exit;
    }
    $pdo = connectDB();
    if (!$pdo) {
        die("Erreur de connexion à la base de données.");
    }

    $resultat = insererConsommation($ID_Compteur, $Mois, $Annee, $Qte, $_FILES['counterPicture']['tmp_name'], $pdo);

    header("Location: ../IHM/saisie_consommation.php?message=" . urlencode($resultat['message']));
    exit;

}if (isset($_GET['action']) && $_GET['action'] === 'get_last_image') {
    header('Content-Type: application/json');
    ob_end_clean();

    try {
        if (!isset($_GET['compteur_id'])) {
            throw new Exception('Paramètre compteur_id manquant');
        }

        $compteurId = (int)$_GET['compteur_id'];
        if ($compteurId <= 0) {
            throw new Exception('ID Compteur invalide');
        }

        // Appel de la fonction getLastCounterImage (assurez-vous qu'elle est incluse)
        $imageData = getLastCounterImage($compteurId);

        if (!$imageData['success']) {
            throw new Exception($imageData['error']);
        }

        // Retourner l'image et les détails au frontend
        echo json_encode([
            'success' => true,
            'image_data' => $imageData['image_data'],
            'date' => $imageData['date'],
            'content_type' => $imageData['content_type']
        ]);
        exit;

    } catch (Exception $e) {
        // Gestion des erreurs
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}


