<?php
require_once "../Traitement/sidebar_controller.php";



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Tableau de bord Fournisseur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/four_dashboard.css">

</head>
<body>
<?php include "sidebar.php"; ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <div class="breadcrumb">PowerBill / Dashboard</div>
                <h2>Espace Client</h2>
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