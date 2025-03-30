<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>   
<!-- Hero Section -->
<section class="hero">
    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Bienvenue sur votre espace de facturation électrique</h1>
                <p>Générez z vos factures d'électricité facilement avec PowerBill.</p>
                <a href="login.php" class="btn btn-dark">Commencer</a>
            </div>
            <div class="col-md-6">
                <img src="assets/images/Hero5.png" alt="Hero Image" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- About Us -->
<section class="about" id="about">
    <div class="container text-center">
        <h2>À Propos de Nous</h2>
        <p><strong>La gestion des factures d'électricité devient plus pratique et conviviale avec PowerBill.</strong></p>
        <p>Nous simplifions la génération de vos factures d'électricité grâce à une plateforme conviviale qui vous permet de suivre votre consommation, de gérer vos factures et d'accéder au service client.</p>
        <a href="#" class="btn btn-outline-primary">En Savoir Plus</a>
    </div>
</section>

<!-- Services Section -->
<section class="services" id="services">
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

<section class="tarifs" id="tarifs">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Nos Tarifs</h2>
            <p class="section-subtitle">Des prix transparents selon votre consommation</p>
        </div>

        <!-- Informations tarifaires principales -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <div class="tarifs-info">
                    <div class="tarifs-icon mb-4">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Prix unitaire selon votre consommation</h3>
                    <p class="lead">Nous proposons des tarifs dégressifs qui s'adaptent à votre consommation mensuelle d'électricité.</p>
                    
                    <div class="tarif-detail mt-4">
                        <span class="badge">TVA applicable : 18%</span>
                    </div>
                    
                    <div class="tarif-note mt-3">
                        <small><i class="fas fa-info-circle"></i> Relevé de compteur le 18 de chaque mois</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="consumption-slider">
                    <div class="consumption-gauge">
                        <div class="gauge-scale"></div>
                        <div class="gauge-pointer" id="gauge-pointer"></div>
                        <div class="gauge-labels">
                            <span>0</span>
                            <span>100</span>
                            <span>150</span>
                            <span>200+</span>
                        </div>
                    </div>
                    
                    <input type="range" class="form-range mt-3" id="consumption-range" min="0" max="250" value="80">
                    
                    <div class="consumption-value">
                        <span id="consumption-display">80</span> kWh/mois
                    </div>
                    
                    <div class="price-display">
                        <div class="current-price">
                            <span id="price-value">0,82</span> DH/kWh
                        </div>
                        <div class="monthly-estimate">
                            Estimation mensuelle: <span id="monthly-total">65,60</span> DH
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grille tarifaire -->
        <div class="tarif-grid">
            <div class="row">
                <div class="col-md-4">
                    <div class="tarif-box">
                        <div class="tarif-range">
                            <div class="consumption-icon small">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h4>Consommation Économique</h4>
                            <div class="range-value">Entre 0 et 100 KWH</div>
                        </div>
                        <div class="tarif-price">
                            <span class="price-value">0,82</span>
                            <span class="price-unit">DH/kWh</span>
                        </div>
                        <div class="tarif-description">
                            <p>Idéal pour les petits consommateurs et ceux qui font attention à leur consommation énergétique.</p>
                        </div>
                        <div class="tarif-examples">
                            <small>Exemple: Appartement d'une personne avec utilisation modérée d'appareils électriques.</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="tarif-box">
                        <div class="tarif-range">
                            <div class="consumption-icon medium">
                                <i class="fas fa-home"></i>
                            </div>
                            <h4>Consommation Moyenne</h4>
                            <div class="range-value">Entre 101 et 150 KWH</div>
                        </div>
                        <div class="tarif-price">
                            <span class="price-value">0,92</span>
                            <span class="price-unit">DH/kWh</span>
                        </div>
                        <div class="tarif-description">
                            <p>Adapté aux foyers avec une consommation standard et des équipements électriques moyens.</p>
                        </div>
                        <div class="tarif-examples">
                            <small>Exemple: Appartement familial avec réfrigérateur, télévision, ordinateur et appareils ménagers.</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="tarif-box">
                        <div class="tarif-range">
                            <div class="consumption-icon large">
                                <i class="fas fa-industry"></i>
                            </div>
                            <h4>Grande Consommation</h4>
                            <div class="range-value">Plus de 151 KWH</div>
                        </div>
                        <div class="tarif-price">
                            <span class="price-value">1,1</span>
                            <span class="price-unit">DH/kWh</span>
                        </div>
                        <div class="tarif-description">
                            <p>Pour les gros consommateurs avec des besoins énergétiques importants ou des équipements énergivores.</p>
                        </div>
                        <div class="tarif-examples">
                            <small>Exemple: Grandes maisons, systèmes de climatisation, équipements professionnels.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="eco-tip"><i class="fas fa-lightbulb text-warning"></i> Conseil: Réduisez votre consommation pour bénéficier des tarifs les plus avantageux!</p>
            
        </div>
    </div>
</section>

<!-- Support Section -->
<section class="support" id="support">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>Une assistance à chaque étape</h2>
                <p class="lead">Notre équipe d'experts est disponible pour vous aider avec n'importe quelle question.</p>
                
                <div class="support-features">
                    <div class="support-feature">
                        <div class="icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="content">
                            <h4>Support technique 24/7</h4>
                            <p>Notre équipe est toujours disponible pour résoudre vos problèmes.</p>
                        </div>
                    </div>
                    
                    <div class="support-feature">
                        <div class="icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="content">
                            <h4>Centre de ressources</h4>
                            <p>Accédez à nos guides et tutoriels détaillés.</p>
                        </div>
                    </div>
                    
                    <div class="support-feature">
                        <div class="icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="content">
                            <h4>Chat en direct</h4>
                            <p>Obtenez des réponses instantanées à vos questions.</p>
                        </div>
                    </div>
                </div>
                <a href="login.php" class="btn btn-primary mt-4">Espace client</a>
                
            </div>
            
            
            
        </div>
    </div>
</section>

<!-- JavaScript pour le défilement doux vers les ancres -->
<script src="assets/js/index.js"></script>


<?php include "footer.php"; ?>