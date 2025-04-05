<?php 
// D'abord, on inclut auth_check.php qui va gérer la session et vérifier l'authentification
require_once "../../Traitement/auth_check.php";

// Pas besoin de démarrer une session ici, c'est déjà fait dans auth_check.php
// Pas besoin non plus de vérifier si l'utilisateur est connecté, c'est déjà fait dans auth_check.php

// Récupérer l'ID du client depuis la session
$id_client = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter mes réclamations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/consulter_reclamations.css">
</head>

<body>
<?php include __DIR__ . "/../Mise_en_page/header_client.php"; ?>
    
    <div class="containerListe">
        <div class="btn-container">
            <a href="../../Traitement/traitement_reclamation.php?action=creer_reclamation" class="btn btn-primary">Nouvelle réclamation</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Réponse</th>
                    </tr>
                </thead>
                <tbody id="reclamationsTable">
                    <!-- Les réclamations seront insérées ici -->
                </tbody>
            </table>
        </div>
    </div>

<script src="../assets/js/consulter_reclamations.js"></script>
    <?php include('../Mise_en_page/footer.php'); ?>
</body>
</html>