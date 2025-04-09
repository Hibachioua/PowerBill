<?php
require_once __DIR__ . "/../../Traitement/auth_check.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Tableau de bord Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . "/../Mise_en_page/header_client.php"; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2>Tableau de bord Client</h2>
                    </div>
                    <div class="card-body">
                        <h4>Bienvenue à votre espace client </h4>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Connexion réussie!
                        </div>
                        
                        <a href="../../Traitement/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . "/../Mise_en_page/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>