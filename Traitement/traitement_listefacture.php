<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

require_once __DIR__ . '/../BD/connexion.php';
require_once __DIR__ . '/../BD/FactureModel.php';

header('Content-Type: application/json');

try {
    $pdo = connectDB();
    if (!$pdo) {
        throw new Exception("Connexion DB impossible");
    }

    $model = new FactureModel($pdo);

    if (!isset($_GET['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Action non spécifiée']);
        exit;
    }

    switch ($_GET['action']) {
        case 'getFactures':
            $factures = $model->getNonPayes();
            echo json_encode([
                'status' => 'success',
                'data' => $factures
            ]);
            break;

        case 'payerFacture':
            if (!isset($_GET['factureID'])) {
                throw new Exception("ID facture manquant");
            }
            $success = $model->payerFacture($_GET['factureID']);
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Paiement enregistré' : 'Échec du paiement'
            ]);
            break;

        default:
            throw new Exception("Action non reconnue");
    }

} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}