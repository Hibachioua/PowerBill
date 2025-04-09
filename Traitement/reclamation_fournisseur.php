<?php
require_once '../BD/connexion.php';
require_once '../BD/fournisseur/reclamationModel.php';

header('Content-Type: application/json');

try {
    // Si GET → envoyer les données en cours
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $reclamations = getReclamationsEnCours($conn);
        $traitees = countReclamationsByStatus($conn, 'Traité');
        $encours = countReclamationsByStatus($conn, 'En cours');

        echo json_encode([
            'reclamations' => $reclamations,
            'stats' => [
                'traitees' => $traitees,
                'encours' => $encours
            ]
        ]);
        exit;
    }

    // Si POST → traiter la réponse
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération des données POST
        $input = $_POST; // Utilisez $_POST directement pour les données form-data
        
        $id = $input['id_reclamation'] ?? null;
        $reponse = $input['reponse'] ?? null;

        if (!$id || !$reponse) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants']);
            exit;
        }

        // Mise à jour dans la base de données
        $success = updateReclamation($conn, $id, $reponse);

        if ($success) {
            // Retourner les données mises à jour
            $reclamations = getReclamationsEnCours($conn);
            $traitees = countReclamationsByStatus($conn, 'Traité');
            $encours = countReclamationsByStatus($conn, 'En cours');

            echo json_encode([
                'success' => true,
                'reclamations' => $reclamations,
                'stats' => [
                    'traitees' => $traitees,
                    'encours' => $encours
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la mise à jour']);
        }
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
    exit;
}
?>