<?php
// Démarrer la session pour afficher les erreurs potentielles
session_start();
include "header.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
<body>
<!-- Section de Connexion -->
<section class="login-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="login-card">
                    <div class="login-header">
                        <h1>Espace Client</h1>
                        <p>Connectez-vous à votre compte PowerBill</p>
                    </div>
                    
                    <?php if(isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Notez le changement dans l'action du formulaire -->
                    <form action="../Traitement/login_traitement.php" method="POST" class="login-form">
                        <div class="form-group mb-3">
                            <label for="email"><i class="fas fa-envelope"></i> Adresse email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Entrez votre adresse email" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                            <div class="password-input-container">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required>
                                <span class="password-toggle" onclick="togglePasswordVisibility()">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-options d-flex justify-content-between mb-4">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Se souvenir de moi</label>
                            </div>
                        </div>
                        
                        <button type="submit" name="login_submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </button>
                    </form>
                    
                    <div class="login-footer text-center mt-4">
                        <p>Vous avez oublié votre identifiant ? <a href="index.php">Contact us</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include "footer.php"; ?>
<script src="assets/js/login.js"></script>

</body>
</html>