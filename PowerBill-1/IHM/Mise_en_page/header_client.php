<?php
// Ce fichier est inclus depuis des pages qui ont déjà démarré une session
// Pas besoin de session_start() ici

// Pas de redirection ici, c'est géré par auth_check.php qui est inclus avant
?>
<!-- Header Client-->
<header>
    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="client_dashboard.php">
                <i class="fas fa-bolt me-2" style="color: #f39c12; font-size: 24px;"></i>
                <span style="color: #3498db; font-weight: bold;">PowerBill</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="client_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="saisie_consommation.php">Consommation</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ListeFactures.php">Factures</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="consulter_reclamations.php">Réclamations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Traitement/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentLocation = window.location.pathname.split("/").pop();
        let menuLinks = document.querySelectorAll(".navbar-nav .nav-link");

        menuLinks.forEach(link => {
            if (link.getAttribute("href") === currentLocation) {
                link.classList.add("active");
                link.style.color = "#4169E1"; 
                link.style.fontWeight = "bold";
            } else {
                link.classList.remove("active");
                link.style.color = ""; 
                link.style.fontWeight = "normal";
            }
        });
    });
</script>