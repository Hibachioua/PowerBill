
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisis ta consommation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    </head>
<?php include __DIR__ . "/../Mise_en_page/header_client.php"; ?>
<body>

<div id="notification" class="notification" style="display:none;">
    <span id="notification-message"></span>
    <button id="notification-close" onclick="closeNotification()">×</button>
</div>

<div class="consumption-container">
    <h2>Enregistrez votre consommation du mois</h2>
    <div id="messageContainer"></div>

    <form id="consumptionForm" action="../../Traitement/consommation_traitement.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="ID_Compteur">ID Compteur :</label>
            <select id="ID_Compteur" name="ID_Compteur" required onchange="fetchLastCounterImage(this.value)">
                <option value="" disabled selected>Chargement...</option>
            </select>
        </div>

        <input type="hidden" id="Mois" name="Mois" value="">
        <input type="hidden" id="Annee" name="Annee" value="">

        <div class="form-group">
            <label for="Qté_consommé">Quantité Consommée (kWh):</label>
            <input type="number" id="Qté_consommé" name="Qté_consommé" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="counterPicture">Photo du compteur:</label>
            <input type="file" id="counterPicture" name="counterPicture" accept="image/*" required>
        </div>
        
        <button type="submit" id="submitBtn" class="btn-dark">Enregistrer</button>
    </form>
</div>

<div id="imagePopup" class="popup-container" style="display: none;">
    <div id="lastCounterImage" class="popup-content"></div>
</div>

<script src="../assets/js/saisie_consommation.js"></script>
<?php include('../Mise_en_page/footer.php'); ?>
</body>
</html>