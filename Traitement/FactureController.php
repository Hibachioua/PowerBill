<?php

require_once '../BD/FactureModel.php'; 
require_once '../BD/connexion.php';

$pdo = connectDB();

// Vérification de l'action
if (empty($_GET['action'])) {
    header('Location: ../IHM/factures_fournisseur.php');
    exit;
}

// Traitement en fonction de l'action
switch ($_GET['action']) {
    case 'getFactures':
        $factures = getNonPayes($pdo); 
        header('Content-Type: application/json');
        echo json_encode($factures);
        error_log("Test d'enregistrement dans error.logs");

        error_log("Factures récupérées: " . json_encode($factures));


        break;
        
    case 'payerFacture':
        if (empty($_GET['factureID']) || empty($_GET['type'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID ou type de facture manquant']);
            exit;
        }
    
        try {
            $success = payerFacture($pdo, $_GET['factureID'], $_GET['type']);
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Paiement effectué' : 'Échec du paiement'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Action non reconnue']);
        break;
}
