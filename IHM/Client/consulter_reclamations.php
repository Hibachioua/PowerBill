<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réclamations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/consulter_reclamations.css">
</head>
<body>
    <?php include __DIR__ . "/../Mise_en_page/header_client.php"; ?>
    
    <div class="reclamations-container">
        <div class="header-section">
            <h1 class="page-title">Mes réclamations</h1>
            <a href="../../Traitement/traitement_reclamation.php?action=creer_reclamation" class="new-reclamation-btn">
                <i class="fas fa-plus"></i> Nouvelle réclamation
            </a>
        </div>
        
        <div id="reclamationsList">
            <!-- Les réclamations seront chargées ici dynamiquement -->
        </div>
    </div>

    <script src="../assets/js/consulter_reclamations.js"></script>
    <?php include('../Mise_en_page/footer.php'); ?>
</body>
</html>