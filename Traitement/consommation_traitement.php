<?php
// Activation du reporting d'erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug initial
error_log("[DEBUG] Méthode: " . $_SERVER['REQUEST_METHOD']);
error_log("[DEBUG] POST: " . print_r($_POST, true));
error_log("[DEBUG] FILES: " . print_r($_FILES, true));

// Vérification stricte de la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("[ERREUR] Méthode non POST reçue");
    header("Location: ../IHM/saisie_consommation.php?error=method_not_allowed");
    exit();
}

// Vérification de l'enctype
if (empty($_POST) && empty($_FILES)) {
    error_log("[ERREUR] Données POST/FILES vides - enctype manquant ?");
    header("Location: ../IHM/saisie_consommation.php?error=missing_form_data");
    exit();
}

require_once __DIR__ . '/../BD/connexion.php';
require_once __DIR__ . '/../BD/requetes_consommation.php';

try {
    // Connexion DB
    $pdo = DB::connect();
    if (!$pdo) {
        throw new Exception("Échec de la connexion à la base de données");
    }

    // Validation des données avec valeurs système pour mois/année
    $data = [
        'ID_Compteur' => filter_input(INPUT_POST, 'ID_Compteur', FILTER_VALIDATE_INT),
        'Mois' => date('n'), // Mois courant (1-12)
        'Annee' => date('Y'), // Année courante
        'Qté_consommé' => filter_input(INPUT_POST, 'Qté_consommé', FILTER_VALIDATE_FLOAT)
    ];

    // Validation des champs
    foreach ($data as $key => $value) {
        if ($value === false || $value === null) {
            throw new Exception("Champ $key invalide ou manquant");
        }
    }

    // Validation fichier
    if (!isset($_FILES['counterPicture']) || $_FILES['counterPicture']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erreur lors de l'upload du fichier (code: " . ($_FILES['counterPicture']['error'] ?? 'NULL') . ")");
    }

    // Configuration upload
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new Exception("Impossible de créer le dossier d'upload");
    }

    // Sécurisation du nom de fichier
    $originalName = basename($_FILES['counterPicture']['name']);
    $safeName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $originalName);
    $filename = uniqid() . '_' . $safeName;
    $targetPath = $uploadDir . $filename;

    // Déplacement fichier
    if (!move_uploaded_file($_FILES['counterPicture']['tmp_name'], $targetPath)) {
        throw new Exception("Échec du déplacement du fichier uploadé");
    }

    // Insertion en base
    if (!insererConsommation(
        $pdo,
        $data['ID_Compteur'],
        $data['Mois'],
        $data['Annee'],
        $data['Qté_consommé'],
        'uploads/' . $filename
    )) {
        throw new Exception("Échec de l'insertion en base de données");
    }

    // Succès
    header("Location: ../IHM/saisie_consommation.php?success=1");
    exit();

} catch (Exception $e) {
    // Nettoyage en cas d'erreur
    if (isset($targetPath) && file_exists($targetPath)) {
        unlink($targetPath);
    }
    
    error_log("[ERREUR] " . $e->getMessage());
    header("Location: ../IHM/saisie_consommation.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>