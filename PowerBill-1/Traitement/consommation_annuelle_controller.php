<?php
require_once '../BD/connexion.php';

$pdo = connectDB();
header('Content-Type: application/json');

try {
    if (empty($_GET['action'])) {
        throw new Exception('Action non spécifiée');
    }

    switch ($_GET['action']) {
        case 'getConsommations':
            echo json_encode(getConsommations($pdo));
            break;
        
        case 'genererFacture':
            echo json_encode(genererFacture($pdo));
            break;

        default:
            throw new Exception('Action non reconnue');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

