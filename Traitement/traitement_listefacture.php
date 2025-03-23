<?php

if (empty($_GET['action'])) {
    header('Location: ../IHM/ListeFactures.php');
    exit;  // Assure-toi que le script s'arrête après la redirection
}

header('Content-Type: application/json'); // Toujours retourner du JSON

require_once "../BD/FactureModel.php";
require_once "../BD/connexion.php";

$model = new FactureModel();

// Récupérer les factures non payées (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getFactures') {
    $factures = $model->getNonPayes();
    echo json_encode($factures);
    exit;
}

// Mettre à jour le statut de paiement d'une facture (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'payerFacture') {
    if (isset($_GET['factureID'])) {
        $factureID = $_GET['factureID'];
        if ($model->updateFacturePayee($factureID)) {
            // Retourner une réponse JSON
            echo json_encode(["status" => "success"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Échec de la mise à jour de la facture."]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Facture ID manquant."]);
        exit;
    }
}

// Rediriger vers la page des factures payées (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'consulterAnciennesFactures') {
    header('Location: ../IHM/ListeFacturesPayees.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getFacturesPayees') {
    $factures = $model->getFacturesPayees();
    echo json_encode($factures);
    exit;
}

// Rediriger vers la page ListeFactures.php (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'retour') {
    header('Location: ../IHM/ListeFactures.php');
    exit;
}

// Si aucune action n'est reconnue
echo json_encode(["status" => "error", "message" => "Action non valide."]);
exit;
?>  