<?php
// sidebar_view.php - À placer dans le dossier IHM ou Views
// Ne contient aucune logique, seulement l'affichage
?>
<div class="sidebar">
    <div class="logo-container">
        <img src="assets/images/bolt-icon.png" alt="PowerBill" class="logo">
        <h2 class="brand-name">PowerBill</h2>
    </div>
    
    <div class="nav-menu">
        <ul class="nav-list">
            <li class="nav-item <?php echo ($current_page == 'dashboard.php' || $current_page == 'client_dashboard.php' || $current_page == 'agent_dashboard.php' || $current_page == 'fournisseur_dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo basename($loginController->getRedirectPath($user_role)); ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>">
                <a href="history.php">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
            </li>
            
            <li class="nav-item <?php echo ($current_page == 'manage_invoices.php') ? 'active' : ''; ?>">
                <a href="manage_invoices.php">
                    <i class="fas fa-file-invoice"></i>
                    <span>Manage invoices</span>
                </a>
            </li>
            
            <li class="nav-item <?php echo ($current_page == 'manage_user.php') ? 'active' : ''; ?>">
                <a href="manage_user.php">
                    <i class="fas fa-user"></i>
                    <span>Manage User</span>
                </a>
            </li>
            
            <li class="nav-item <?php echo ($current_page == 'manage_complaints.php') ? 'active' : ''; ?>">
                <a href="manage_complaints.php">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Manage complaints</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="logout-container">
        <a href="../Traitement/logout.php" class="logout-btn">
            Logout
        </a>
    </div>
</div>

<link rel="stylesheet" href="assets/sidebar.css">