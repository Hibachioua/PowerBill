<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consommation - PowerBill</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="consumption-container">
    <h2>Enter your consumption of the month</h2>
    <form id="monthlyConsumptionForm" action="#" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="clientID">ID CLIENT</label>
            <input type="text" id="clientID" name="clientID" required>
        </div>
        <div class="form-group">
            <label for="meterValue">Entrez la valeur du compteur:</label>
            <input type="text" id="meterValue" name="meterValue" required>
        </div>
        <div class="form-group">
            <label for="counterPicture">Entrez la photo de votre compteur:</label>
            <input type="file" id="counterPicture" name="counterPicture" accept="image/jpeg, image/png, image/jpg, image/pdf" required>
        </div>
        <button type="submit" class="btn btn-dark">SEND</button>
    </form>
</div>
</body>

<?php include "footer.php"; ?>