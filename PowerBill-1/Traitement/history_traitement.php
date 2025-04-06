<?php
// Traitement/history_traitement.php
require_once "../BD/historyModel.php";

// Pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Prépare les données pour la vue d'historique
 * 
 * @return array Données pour la vue
 */
function prepareHistoryData() {
    // Récupérer les filtres depuis l'URL
    $filtres = [
        'annee' => isset($_GET['annee']) ? $_GET['annee'] : '',
        'mois' => isset($_GET['mois']) ? $_GET['mois'] : '',
        'client' => isset($_GET['client']) ? $_GET['client'] : ''
    ];
    
    // Récupérer les données filtrées
    $consommations = getAllConsommations($filtres);
    
    // Récupérer les données pour les filtres
    $years = getDistinctYears();
    $clients = getClientsForFilter();
    
    // Liste des mois pour le filtre
    $mois = [
        '1' => 'Janvier',
        '2' => 'Février',
        '3' => 'Mars',
        '4' => 'Avril',
        '5' => 'Mai',
        '6' => 'Juin',
        '7' => 'Juillet',
        '8' => 'Août',
        '9' => 'Septembre',
        '10' => 'Octobre',
        '11' => 'Novembre',
        '12' => 'Décembre'
    ];
    
    return [
        'consommations' => $consommations,
        'filtres' => $filtres,
        'years' => $years,
        'clients' => $clients,
        'mois' => $mois
    ];
}

/**
 * Vérifie si un filtre est actif
 * 
 * @param array $filtres Tableau des filtres
 * @return bool Vrai si au moins un filtre est actif
 */
function hasActiveFilters($filtres) {
    foreach ($filtres as $filtre) {
        if (!empty($filtre)) {
            return true;
        }
    }
    return false;
}

// Point d'entrée API - renvoie les données au format JSON
if (isset($_GET['api']) && $_GET['api'] === 'true') {
    try {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *'); // Pour éviter les problèmes CORS
        
        $data = prepareHistoryData();
        $data['hasFilters'] = hasActiveFilters($data['filtres']);
        
        // Assurez-vous que la sortie est propre (pas d'erreurs/warnings PHP)
        ob_clean();
        echo json_encode($data);
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}