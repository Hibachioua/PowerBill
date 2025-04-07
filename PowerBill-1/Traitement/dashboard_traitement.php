<?php
// Traitement/dashboard_controller.php - Contrôleur pour le tableau de bord
require_once __DIR__ . "../../BD/dashboardModel.php";
require_once __DIR__ . "/sidebar_controller.php";


function prepareDashboardData() {
   // Vérifier que l'utilisateur a le rôle fournisseur (ID 3)
   checkUserAccess(3);
   
   // Récupérer les données brutes du modèle
   $rawData = getDashboardData();
   
   // Préparer les données pour les graphiques
   $formattedData = formatDashboardData($rawData);
   
   return $formattedData;
}

/**
* Formate les données brutes pour l'affichage dans la vue
* @param array $rawData Données brutes du modèle
* @return array Données formatées
*/
function formatDashboardData($rawData) {
   $formattedData = $rawData;
   
   // Formater les données de consommation mensuelle pour le graphique
   $monthlyConsumptionData = [
       'labels' => [],
       'data' => []
   ];
   
   $monthNames = [
       1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
       5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 
       9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
   ];
   
   // Initialiser les mois manquants avec des valeurs nulles
   for ($i = 1; $i <= 12; $i++) {
       $monthlyConsumptionData['labels'][] = $monthNames[$i];
       $monthlyConsumptionData['data'][] = 0;
   }
   
   // Remplir avec les données réelles
   foreach ($rawData['monthly_consumption'] as $item) {
       $monthIndex = $item['mois'] - 1; // Ajuster l'index pour tableau 0-indexé
       $monthlyConsumptionData['data'][$monthIndex] = floatval($item['total_consommation']);
   }
   
   $formattedData['monthly_consumption_chart'] = $monthlyConsumptionData;
   
   // Formater les données de nouveaux clients pour le graphique
   $newClientsData = [
       'labels' => array_values($monthNames),
       'data' => array_fill(0, 12, 0)
   ];
   
   foreach ($rawData['new_clients'] as $item) {
       $monthIndex = $item['mois'] - 1;
       $newClientsData['data'][$monthIndex] = intval($item['nouveaux_clients']);
   }
   
   $formattedData['new_clients_chart'] = $newClientsData;
   
   // Formater les données de statut de consommation pour le graphique circulaire
   $consumptionStatusData = [
       'labels' => [],
       'data' => [],
       'colors' => [
           'pas d\'anomalie' => '#28a745', // Vert
           'anomalie' => '#dc3545'        // Rouge
       ]
   ];
   
   foreach ($rawData['consumption_status'] as $item) {
       $consumptionStatusData['labels'][] = $item['Statut'];
       $consumptionStatusData['data'][] = intval($item['total']);
   }
   
   $formattedData['consumption_status_chart'] = $consumptionStatusData;
   
   // Formater les données de consommation par compteur
   $consumptionByMeterData = [
       'labels' => [],
       'data' => []
   ];
   
   foreach ($rawData['consumption_by_meter'] as $item) {
       $consumptionByMeterData['labels'][] = 'Compteur ' . $item['compteur'];
       $consumptionByMeterData['data'][] = floatval($item['total_consommation']);
   }
   
   $formattedData['consumption_by_meter_chart'] = $consumptionByMeterData;
   
   // Formater les données de revenus mensuels pour le graphique
   $monthlyRevenueData = [
       'labels' => array_values($monthNames),
       'data' => array_fill(0, 12, 0)
   ];
   
   foreach ($rawData['monthly_revenue'] as $item) {
       $monthIndex = $item['mois'] - 1;
       $monthlyRevenueData['data'][$monthIndex] = floatval($item['revenue']);
   }
   
   $formattedData['monthly_revenue_chart'] = $monthlyRevenueData;
   
   return $formattedData;
}

/**
* Formate une date pour l'affichage
* @param string $date Date au format SQL (YYYY-MM-DD)
* @return string Date formatée pour l'affichage
*/
function formatDate($date) {
   if (empty($date)) return '';
   
   $timestamp = strtotime($date);
   return date('d/m/Y', $timestamp);
}

/**
* Charge les données nécessaires pour l'interface de la sidebar
*/
function loadSidebarData() {
   $current_page = basename($_SERVER['PHP_SELF']);
   $user_role = $_SESSION['user_role'];
   return getSidebarData($current_page, $user_role);
}
?>