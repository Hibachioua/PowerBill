<?php
require_once '../BD/connexion.php'; // Assurez-vous que cette connexion est correcte
require_once '../BD/requetes_consommation.php';

// Activation du reporting d'erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Nettoyer tout buffer de sortie existant
if (ob_get_length() > 0) {
    ob_clean();
}

// Debug initial
error_log("[DEBUG] Méthode: " . $_SERVER['REQUEST_METHOD']);
error_log("[DEBUG] GET: " . print_r($_GET, true));
error_log("[DEBUG] POST: " . print_r($_POST, true));
error_log("[DEBUG] FILES: " . print_r($_FILES, true));

// Traitement des différentes actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $ID_Compteur = filter_input(INPUT_POST, 'ID_Compteur', FILTER_VALIDATE_INT);
    $Mois = date('n'); // Mois actuel
    $Annee = date('Y'); // Année actuelle
    $Qte = filter_input(INPUT_POST, 'Qté_consommé', FILTER_VALIDATE_FLOAT);
    // Vérification du fichier uploadé
    if (!isset($_FILES['counterPicture']) || $_FILES['counterPicture']['error'] !== UPLOAD_ERR_OK) {
        header("Location: ../IHM/saisie_consommation.php?message=" . urlencode("Erreur lors de l'upload de l'image"));
        exit;
    }

    // Vérification du type MIME
    $typeMime = mime_content_type($_FILES['counterPicture']['tmp_name']);
    if (!in_array($typeMime, ['image/jpeg', 'image/png', 'image/gif'])) {
        header("Location: ../IHM/saisie_consommation.php?message=" . urlencode("Type de fichier non supporté"));
        exit;
    }

    // Insertion
    $pdo = DB::connect();
    if (!$pdo) {
        die("Erreur de connexion à la base de données.");
    }

    if (insererConsommation(
        $pdo,
        $ID_Compteur,
        $Mois,
        $Annee,
        $Qte,
        $_FILES['counterPicture']['tmp_name']
    )) {
        header("Location: ../IHM/saisie_consommation.php?success=1&message=" . urlencode("Consommation enregistrée avec succès"));
        exit;
    } else {
        // Supprimer l'image si l'insertion échoue
        unlink($_FILES['counterPicture']['tmp_name']);
        header("Location: ../IHMsaisie_consommation.php?message=" . urlencode("Échec de l'enregistrement."));
        exit;
    }
    exit;
}elseif (isset($_GET['action']) && $_GET['action'] === 'get_last_image') {
    header('Content-Type: application/json');
    ob_end_clean(); // Efface les sorties indésirables

    try {
        if (!isset($_GET['compteur_id'])) {
            throw new Exception('Paramètre compteur_id manquant');
        }

        $compteurId = (int)$_GET['compteur_id'];
        if ($compteurId <= 0) {
            throw new Exception('ID Compteur invalide');
        }

        $pdo = DB::connect();
        if (!$pdo) {
            throw new Exception('Échec de la connexion à la base de données');
        }

        error_log("Tentative de récupération image pour compteur ID: $compteurId");
        $imageData = getLastCounterImage($pdo, $compteurId);

        if (!is_array($imageData) || !isset($imageData['success'])) {
            throw new Exception('Données image invalides ou réponse inattendue');
        }

        echo json_encode($imageData);
        exit;

    } catch (Exception $e) {
        error_log("Erreur dans get_last_image: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erreur serveur',
            'message' => $e->getMessage(),
            'debug' => [
                'compteur_id' => $compteurId ?? null,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
        exit;
    }
}

?>