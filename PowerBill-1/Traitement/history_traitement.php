<?php
// Traitement/history_traitement.php - Contrôleur pour la page d'historique
require_once "../../BD/historyModel.php";

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
 * Formate la taille du fichier en unités lisibles
 * 
 * @param int $bytes Taille en octets
 * @return string Taille formatée
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' Go';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' Mo';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' Ko';
    } else {
        return $bytes . ' octets';
    }
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
?>