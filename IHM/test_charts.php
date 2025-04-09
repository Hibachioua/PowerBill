<?php
// Version de test simplifiée - sauvegarder comme test_charts.php
// Cette version est complètement indépendante et devrait fonctionner partout

session_start(); // Pour maintenir la session si nécessaire

// Vérifier si l'utilisateur est connecté (simplifié)
if (!isset($_SESSION['user_id'])) {
    // Rediriger ou continuer pour tester
    // header("Location: login.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Tableau de bord Fournisseur</title>
    
    <!-- Bootstrap et FontAwesome (assurez-vous qu'ils sont bien chargés) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Chart.js - Assurez-vous que cette version est bien chargée -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- Style interne pour éviter les conflits CSS -->
    <style>
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
            background-color: #f5f8fa;
        }
        
        .dashboard-header {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #3498db;
        }
        
        .breadcrumb {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-menu .icon {
            color: #6c757d;
            font-size: 18px;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        /* Cartes statistiques */
        .stat-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .stat-card-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-right: 15px;
            font-size: 24px;
            color: #fff;
        }
        
        .bg-primary { background-color: #3498db; }
        .bg-warning { background-color: #f39c12; }
        .bg-danger { background-color: #e74c3c; }
        .bg-success { background-color: #2ecc71; }
        
        .stat-card-info h5 {
            font-size: 14px;
            margin: 0 0 5px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .stat-card-info h2 {
            font-size: 28px;
            margin: 0 0 5px;
            font-weight: 700;
            color: #333;
        }
        
        .text-success { color: #2ecc71; }
        .text-danger { color: #e74c3c; }
        
        /* Cartes de graphiques */
        .chart-container {
            background-color: #fff;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .chart-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .chart-header h5 {
            margin: 0;
            color: #3498db;
            font-size: 16px;
            font-weight: 600;
        }
        
        .chart-body {
            padding: 20px;
            height: 300px; /* Hauteur fixe importante */
            position: relative;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <div class="breadcrumb">PowerBill / Dashboard</div>
                <h2>Espace Fournisseur</h2>
            </div>
            <div class="user-menu">
                <i class="fas fa-bell icon"></i>
                <i class="fas fa-cog icon"></i>
                <div class="user-avatar">
                    <img src="assets/images/user.jpg" alt="User">
                </div>
            </div>
        </div>
        
        <!-- Cartes de statistiques -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5>Clients Total</h5>
                        <h2><?php echo $stats['total_clients']; ?></h2>
                        <p class="text-success"><i class="fas fa-arrow-up"></i> +12% ce mois</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon bg-warning">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5>Réclamations</h5>
                        <h2><?php echo $stats['total_complaints']; ?></h2>
                        <p class="text-danger"><i class="fas fa-arrow-up"></i> +5% ce mois</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5>Consommations Anomalies</h5>
                        <h2><?php echo $stats['anomaly_consumptions']; ?></h2>
                        <p class="text-success"><i class="fas fa-arrow-down"></i> -3% ce mois</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon bg-success">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5>Revenu Mensuel</h5>
                        <h2><?php echo number_format($stats['monthly_revenue']); ?> DH</h2>
                        <p class="text-success"><i class="fas fa-arrow-up"></i> +8% ce mois</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Consommation Mensuelle (kWh)</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Nouveaux Clients par Mois</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="chart2"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Statuts des Consommations</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="chart3"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Consommation par Compteur (kWh)</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="chart4"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Code d'initialisation des graphiques
        window.onload = function() {
            console.log("Fenêtre chargée, démarrage de l'initialisation des graphiques");
            
            // Utiliser un timeout pour éviter les problèmes de timing
            setTimeout(function() {
                // Vérifier que Chart.js est disponible
                if (typeof Chart === 'undefined') {
                    console.error("Chart.js n'est pas chargé");
                    alert("Erreur: Chart.js n'est pas disponible. Veuillez vérifier la console.");
                    return;
                }
                
                console.log("Chart.js est disponible:", Chart.version);
                
                // Données communes
                const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                
                try {
                    // Graphique 1: Consommation Mensuelle
                    const ctx1 = document.getElementById('chart1');
                    if (!ctx1) {
                        console.error("Canvas 'chart1' non trouvé");
                    } else {
                        new Chart(ctx1, {
                            type: 'line',
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Consommation (kWh)',
                                    data: [140, 160, 170, 1810, 0, 0, 0, 0, 0, 0, 0, 0],
                                    fill: false,
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                        console.log("Graphique 1 initialisé");
                    }
                    
                    // Graphique 2: Nouveaux Clients
                    const ctx2 = document.getElementById('chart2');
                    if (!ctx2) {
                        console.error("Canvas 'chart2' non trouvé");
                    } else {
                        new Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Nouveaux Clients',
                                    data: [2, 1, 3, 5, 0, 0, 0, 0, 0, 0, 0, 0],
                                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        console.log("Graphique 2 initialisé");
                    }
                    
                    // Graphique 3: Statuts
                    const ctx3 = document.getElementById('chart3');
                    if (!ctx3) {
                        console.error("Canvas 'chart3' non trouvé");
                    } else {
                        new Chart(ctx3, {
                            type: 'doughnut',
                            data: {
                                labels: ['Normal', 'Anomalie'],
                                datasets: [{
                                    data: [8, 4],
                                    backgroundColor: [
                                        'rgb(75, 192, 192)',
                                        'rgb(255, 99, 132)'
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                        console.log("Graphique 3 initialisé");
                    }
                    
                    // Graphique 4: Consommation par Compteur
                    const ctx4 = document.getElementById('chart4');
                    if (!ctx4) {
                        console.error("Canvas 'chart4' non trouvé");
                    } else {
                        new Chart(ctx4, {
                            type: 'bar',
                            data: {
                                labels: ['Compteur 1', 'Compteur 2'],
                                datasets: [{
                                    label: 'Consommation (kWh)',
                                    data: [1950, 170],
                                    backgroundColor: 'rgba(255, 159, 64, 0.5)'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        console.log("Graphique 4 initialisé");
                    }
                    
                    console.log("Tous les graphiques ont été initialisés avec succès");
                } catch (error) {
                    console.error("Erreur lors de l'initialisation des graphiques:", error);
                    alert("Erreur lors de l'initialisation des graphiques. Vérifiez la console pour plus de détails.");
                }
            }, 500); // Délai de 500ms
        };
    </script>
</body>
</html>