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
<div id="imagePopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h3>Dernière photo du compteur</h3>
        <div id="lastCounterImage"></div>
    </div>
</div>
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
        <input type="number" id="ID_Compteur" name="ID_Compteur" required onchange="fetchLastCounterImage(this.value)">
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
    
    <button type="submit" class="btn-dark">Enregistrer</button>
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
async function fetchLastCounterImage(compteurId) {
    const popup = document.getElementById('imagePopup');
    const imageContainer = document.getElementById('lastCounterImage');
    
    popup.style.display = 'flex';
    imageContainer.innerHTML = '<div class="loader">Chargement...</div>';

    try {
        const response = await fetch(`../Traitement/consommation_traitement.php?action=get_last_image&compteur_id=${compteurId}`);
        
        if (!response.ok) {
            throw new Error(`Échec de la requête : ${response.status}`);
        }
        
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Erreur de récupération');
        }

        // Si vous voulez afficher l'image directement
        if (data.image_data) {
            // Création de l'URL base64 pour l'image
            const imageSrc = 'data:' + data.content_type + ';base64,' + data.image_data;

            // Affichage de l'image dans le container
            imageContainer.innerHTML = `
                <img src="${imageSrc}" 
                     alt="Photo du compteur" 
                     style="max-width:100%; max-height:70vh;"
                     onerror="this.onerror=null;this.parentElement.innerHTML='<p class=\'error\'>Erreur de chargement de l\'image</p><button onclick=\'closePopup()\' class=\'close-btn\'>Fermer</button>'">
                <button onclick="closePopup()" class="close-btn">Fermer</button>
            `;
        } else {
            throw new Error('Aucune donnée image disponible');
        }

    } catch (error) {
        imageContainer.innerHTML = `
            <div class="error-container">
                <p class="error-title">Erreur de chargement</p>
                <p class="error-message">${error.message}</p>
                <p class="error-debug">ID Compteur: ${compteurId}</p>
                <button onclick="closePopup()" class="close-btn">Fermer</button>
            </div>
        `;
    }
}

function closePopup() {
    document.getElementById('imagePopup').style.display = 'none';
}

// Fermer quand on clique en dehors du contenu
document.getElementById('imagePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});

// Fermer avec la touche ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup();
    }
});
// Fonction pour vérifier si une string est un JSON valide
function isJson(str) {
    try {
        JSON.parse(str);
        return true;
    } catch (e) {
        return false;
    }
}

// Fonction pour nettoyer les réponses fetch
async function parseFetchResponse(response) {
    const text = await response.text();
    if (!isJson(text)) {
        throw new Error(`Réponse invalide: ${text.substring(0, 100)}...`);
    }
    return JSON.parse(text);
}
</script>
</body>
</html>
<?php include "footer.php"; ?>