<?php
// Démarrer la session
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

$user_email = htmlspecialchars($_SESSION['user_email']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Tableau de bord Client</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Styles personnalisés -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
    <!-- En-tête du client -->
    <?php include "header.php"; ?>

    <main class="container mt-5">
        <section class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Tableau de bord Client</h2>
                    </div>
                    <div class="card-body">
                        <h4>Bienvenue, <?php echo $user_email; ?> 👋</h4>
                        <p class="text-muted">Vous êtes connecté en tant que client.</p>
                        
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i> Connexion réussie !
                        </div>

                        <div class="alert alert-success d-flex align-items-center">
                          <a href="saisie_consommation.php">  <i class="fas fa-check-circle me-2"></i> Saisis ta consommation </a>
                        </div>

                        <a href="../Traitement/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Pied de page -->
    <?php include "footer.php"; ?>

    <!-- Bootstrap JS (optionnel si pas nécessaire) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
