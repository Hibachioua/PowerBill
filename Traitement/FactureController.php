<?php


// Inclusion des modèles et configuration
require_once '../BD/Factures.php';
require_once '../BD/connexion.php';

// Initialisation de la connexion à la BD
$pdo = connectDB(); // Correction : définir $pdo avant de l'utiliser

// Vérification de l'action
if (empty($_GET['action'])) {
    header('Location: ../IHM/factures_fournisseur.php');
    exit;
}

// Création du modèle avec la connexion PDO
$model = new FactureModel($pdo);

// Traitement en fonction de l'action
switch ($_GET['action']) {
    case 'getFactures':
        $factures = $model->getFactures();
        header('Content-Type: application/json');
        echo json_encode($factures);
        break;
        
    case 'payerFacture':
        if (empty($_GET['factureID'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID facture manquant']);
            exit;
        }
        
        try {
            $success = $model->payerFacture($_GET['factureID']);
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
