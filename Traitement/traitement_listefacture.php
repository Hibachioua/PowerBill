<?php
// Inclure la connexion à la base de données et les fonctions nécessaires
include_once(__DIR__ . '/../BD/connexion.php');
include_once(__DIR__ . '/../BD/liste_facture_BD.php');

// Vérifier si une facture doit être payée
if (isset($_GET['facture_id']) && is_numeric($_GET['facture_id'])) {
    $facture_id = $_GET['facture_id'];

    // Mettre à jour l'état de la facture à "payée"
    $result = updateFacturePayee($facture_id);

    // Si le paiement est réussi
    if ($result) {
        // Récupérer toutes les factures non payées après paiement
        $factures = getNonPayeFactures();

        // Encoder les factures en JSON pour les passer à la page de redirection
        $factures_json = urlencode(json_encode($factures));

        // Rediriger vers la page ListeFactures.php avec les nouvelles données de factures
        header('Location: ../IHM/ListeFactures.php?factures=' . $factures_json . '&paiement_succes=true');
    } else {
        // Rediriger avec un message d'erreur
        $error_message = urlencode("Erreur lors du paiement de la facture.");
        header('Location: ../IHM/ListeFactures.php?error=' . $error_message);
    }
    exit;
}

// Si aucun paiement, récupérer toutes les factures non payées
$factures = getNonPayeFactures();

// Rediriger vers la page avec les factures au format JSON
$factures_json = urlencode(json_encode($factures));
header('Location: ../IHM/ListeFactures.php?factures=' . $factures_json);
exit;
?>


