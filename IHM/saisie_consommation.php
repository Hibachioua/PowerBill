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
    <h2>Enregistrez votre consommation du mois</h2>
    
    <!-- Affichage des messages d'erreur/succès -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert <?= isset($_GET['success']) ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars(urldecode($_GET['message'])) ?>
        </div>
    <?php endif; ?>
    
    <form id="monthlyConsumptionForm" action="../Traitement/consommation_traitement.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="clientID">Identifiant Client</label>
            <input type="text" id="clientID" name="clientID" required 
                   pattern="[0-9]+" title="Veuillez entrer uniquement des chiffres">
        </div>
        <div class="form-group">
            <label for="meterValue">Valeur du compteur (kWh):</label>
            <input type="number" id="meterValue" name="meterValue" required 
                   step="0.01" min="0" placeholder="123.45">
        </div>
        <div class="form-group">
            <label for="counterPicture">Photo du compteur:</label>
            <input type="file" id="counterPicture" name="counterPicture" 
                   accept="image/*,.pdf" required
                   onchange="previewImage(this)">
            <div id="imagePreview" style="margin-top: 10px;"></div>
        </div>
        <button type="submit" class="btn btn-dark">ENVOYER</button>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (input.files[0].type.includes('image')) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                preview.appendChild(img);
            } else {
                preview.innerHTML = '<p>Fichier PDF sélectionné</p>';
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
<?php include "footer.php"; ?>