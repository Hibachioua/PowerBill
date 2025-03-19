<?php include "header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Your Electricity Billing Solution</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Bienvenue sur votre espace de facturation électrique</h1>
                    <p>Générez z vos factures d'électricité facilement avec PowerBill.</p>
                    <a href="/login.php" class="btn btn-dark">Commencer</a>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/Hero5.png" alt="Hero Image" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- About Us -->
    <section class="about">
        <div class="container text-center">
            <h2>À Propos de Nous</h2>
            <p><strong>La gestion des factures d'électricité devient plus pratique et conviviale avec PowerBill.</strong></p>
            <p>Nous simplifions la génération de vos factures d'électricité grâce à une plateforme conviviale qui vous permet de suivre votre consommation, de gérer vos factures et d'accéder au service client.</p>
            <a href="#" class="btn btn-outline-primary">En Savoir Plus</a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container text-center">
            <h2>Nous fournissons les meilleurs services</h2>
            <div class="row">
                <div class="col-md-4">
                    <img src="assets/images/invoice.svg" alt="Invoice Tracking" class="img-fluid">
                    <h4>Suivi des Factures</h4>
                    <p>Accédez et téléchargez vos factures d'électricité à tout moment.</p>
                </div>
                <div class="col-md-4">
                    <img src="assets/images/support.svg" alt="Customer Support" class="img-fluid">
                    <h4>Support Client 24/7</h4>
                    <p>Obtenez une assistance instantanée pour tout problème lié à la facturation.</p>
                </div>
                <div class="col-md-4">
                    <img src="assets/images/meter.svg" alt="Automated Readings" class="img-fluid">
                    <h4>Relevés de Compteur Automatisés</h4>
                    <p>Recevez des mises à jour précises et en temps réel sur votre consommation d'électricité.</p>
                </div>
            </div>
        </div>
    </section>
    
    <?php include "footer.php"; ?>


    <script>
        // Simple animation for sections
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.about, .services .col-md-4');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, {
                threshold: 0.1
            });
            
            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>