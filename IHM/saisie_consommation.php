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
    
    <!-- Dans saisie_consommation.php -->
    <form id="consumptionForm" action="../Traitement/consommation_traitement.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    <!-- Notez bien method="POST" et enctype -->
    
    <div class="form-group">
        <label for="ID_Compteur">ID Compteur:</label>
        <input type="number" id="ID_Compteur" name="ID_Compteur" required>
    </div>

        <input type="hidden" id="Mois" name="Mois" min="1" max="12" required>
        <input type="hidden" id="Annee" name="Annee" min="2020" max="2030" required>
    
    <div class="form-group">
        <label for="Qté_consommé">Quantité Consommée (kWh):</label>
        <input type="number" id="Qté_consommé" name="Qté_consommé" step="0.01" min="0" required>
    </div>
    
    <div class="form-group">
        <label for="counterPicture">Photo du compteur:</label>
        <input type="file" id="counterPicture" name="counterPicture" accept="image/*" required>
    </div>
    
    <button type="submit" class="btn">Enregistrer</button>
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

function validateForm() {
    // Désactiver le bouton pour éviter les doubles soumissions
    document.getElementById('submitBtn').disabled = true;
    return true; // Retourner true pour permettre la soumission
}

</script>
</body>
</html>
<?php include "footer.php"; ?>