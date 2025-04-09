<?php
require_once '../BD/AnomalieModel.php';
require_once '../BD/connexion.php';

$pdo = connectDB();

if (empty($_GET['action'])) {
    header('Location: ../IHM/Liste_anomalies.php');
    exit;
}

switch ($_GET['action']) {
    case 'getAnomalies':
        $anomalies = getAnomalies($pdo);
        header('Content-Type: application/json');
        echo json_encode($anomalies);
        break;

    case 'corrigerAnomalie':
        if (empty($_POST['id']) || empty($_POST['nouvelleConsommation']) || 
            empty($_POST['mois']) || empty($_POST['annee']) || empty($_POST['idCompteur'])) {
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
            exit;
        }

        try {
            $success = corrigerAnomalie(
                $pdo,
                $_POST['id'],
                $_POST['nouvelleConsommation'],
                $_POST['mois'],
                $_POST['annee'],
                $_POST['idCompteur']
            );
            
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Anomalie corrigée et facture créée' : 'Échec de la correction'
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
?>