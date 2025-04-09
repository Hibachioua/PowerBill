<?php
session_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

require_once __DIR__ . '/../BD/connexion.php';
require_once __DIR__ . '/../BD/Factures.php';

header('Content-Type: application/json');

try {
    // Vérification de l'authentification
    if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
        throw new Exception("Accès non autorisé - Session invalide");
    }

    // Vérification du rôle client (ID_Role = 1)
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
        throw new Exception("Accès réservé aux clients");
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Identifiant client manquant");
    }

    $clientId = $_SESSION['user_id'];
    $pdo = connectDB();

    if (!$pdo) {
        throw new Exception("Connexion DB impossible");
    }

    if (!isset($_GET['action'])) {
        throw new Exception("Action non spécifiée");
    }

    switch ($_GET['action']) {
        case 'getFactures':
            $factures = getNonPayes($pdo, $clientId);
            echo json_encode([
                'status' => 'success',
                'data' => $factures
            ]);
            break;
            
        case 'payerFacture':
            $factureID = $_POST['factureID'] ?? null;
            if (!$factureID) {
                throw new Exception("ID facture manquant");
            }
            
            if (!is_numeric($factureID)) {
                throw new Exception("ID facture invalide");
            }
            
            $success = payerFacture($pdo, (int)$factureID);
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Paiement effectué' : 'Échec du paiement'
            ]);
            break;

        case 'getFacturesPayees':
            $factures = getFacturesPayees($pdo, $clientId);
            echo json_encode([
                'status' => 'success',
                'data' => $factures
                
            ]);
            break;
        
        case 'retour':
                header('Location: ../IHM/Client/ListeFactures.php');
                exit();
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
?>