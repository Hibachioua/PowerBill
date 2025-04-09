<?php
require_once "../../Traitement/auth_check.php";
require_once "../../Traitement/dashboard_traitement.php";

$viewData = loadDashboardView();
extract($viewData);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Tableau de bord Fournisseur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/four_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>

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


<?php include __DIR__ . "/../Mise_en_page/sidebar.php"; ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <div class="breadcrumb">PowerBill / Dashboard</div>
                <h2>Espace Fournisseur</h2>
            </div>
            <div class="user-menu">
                <i class="fas fa-bell icon"></i>
                <i class="fas fa-cog icon"></i>
                <div class="user-avatar" style="cursor: pointer;">
                    <img src="../assets/images/user.svg" alt="User">
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
        </div>
    </div>
    
    <!-- Modal Profil Utilisateur -->
    <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userProfileModalLabel">Profil Fournisseur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="../assets/images/user.svg" alt="Photo de profil" class="rounded-circle" width="100" height="100">
                        <h4 class="mt-2" id="userCompanyName">Chargement...</h4>
                        <p class="text-muted" id="userEmail">Chargement...</p>
                    </div>
                    
                    <div class="user-info">
                        <div class="mb-3">
                            <label class="fw-bold">ID Fournisseur:</label>
                            <p id="userFournisseurId">Chargement...</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">ID Utilisateur:</label>
                            <p id="userId">Chargement...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard_charts.js"></script>
</body>
</html>