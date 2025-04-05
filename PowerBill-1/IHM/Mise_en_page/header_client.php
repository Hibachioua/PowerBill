<?php

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Votre solution de facturation d'électricité</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header Client-->
    <header>
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <i class="fas fa-bolt me-2" style="color: #f39c12; font-size: 24px;"></i>
                    <span style="color: #3498db; font-weight: bold;">PowerBill</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="client_dashboard.php" >Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="enter_bill.php">Enter Bill</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_bill.php" >View Bill</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="consulter_reclamations.php">Réclamations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</body>

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

</html>