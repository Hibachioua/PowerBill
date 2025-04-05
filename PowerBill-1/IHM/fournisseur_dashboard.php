<?php
// fournisseur_dashboard.php - À placer dans le dossier IHM
require_once "../Traitement/auth_check.php";

// Vérifier que l'utilisateur a le rôle fournisseur (ID_Role = 3)
checkUserAccess(3);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Tableau de bord Fournisseur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            display: flex;
            background-color: #e9f3fc;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
        }
        
        .dashboard-header {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #3498db;
        }
        
        .dashboard-header .breadcrumb {
            margin: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .dashboard-header .search-bar {
            display: flex;
            align-items: center;
        }
        
        .dashboard-header .search-bar input {
            border-radius: 30px;
            padding: 8px 15px;
            border: 1px solid #e9ecef;
            width: 250px;
            font-size: 0.9rem;
        }
        
        .dashboard-header .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .dashboard-header .user-menu .icon {
            color: #6c757d;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .dashboard-header .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .dashboard-header .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .content-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="user-menu">
                <i class="fas fa-bell icon"></i>
                <i class="fas fa-cog icon"></i>
                <div class="user-avatar">
                    <img src="assets/images/user.jpg" alt="User">
                </div>
            </div>
        </div>
        
        <div class="content-container">
            <!-- Contenu du tableau de bord ici -->
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>