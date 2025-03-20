<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamation - PowerBill</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <main>
    <div class="complaint-container">
    <h2>Votre réclamation</h2>
    <form id="complaintForm" action="../Traitement/traitement_reclamation.php" method="POST">
        <label for="clientID">ID CLIENT</label>
        <input type="text" id="clientID" name="clientID" required>

        <label>Type de réclamation :</label>
        <div class="radio-group">
            <label><input type="radio" name="complaintType" value="fuite interne" checked> Fuite interne</label>
            <label><input type="radio" name="complaintType" value="fuite externe"> Fuite externe</label>
            <label><input type="radio" name="complaintType" value="facture"> Facture</label>
            <label><input type="radio" name="complaintType" value="autre"> Autre</label>
        </div>
        
        <label for="description">Description :</label>
        <textarea id="description" name="description" rows="4" required></textarea>
        
        <button type="submit">Envoyer</button>
    </form>
</div>
    </main>
<?php include "footer.php"; ?>
    <script src="script.js"></script>
</body>
</html>
