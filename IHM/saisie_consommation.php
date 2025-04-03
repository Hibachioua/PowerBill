<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}
?>
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
    <div id="messageContainer"></div>

    <form id="consumptionForm" action="../Traitement/consommation_traitement.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    
    <!-- Sélection de l'ID Compteur -->
    <div class="form-group">
        <label for="ID_Compteur">ID Compteur :</label>
        <select id="ID_Compteur" name="ID_Compteur" required onchange="fetchLastCounterImage(this.value)">
            <option value="" disabled selected>Chargement...</option>
        </select>
    </div>

    <input type="hidden" id="Mois" name="Mois" value="<?= date('n'); ?>">
    <input type="hidden" id="Annee" name="Annee" value="<?= date('Y'); ?>">

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

<!-- Pop-up pour afficher l'image du compteur -->
<div id="imagePopup" class="popup-container" style="display: none;">
    <div id="lastCounterImage" class="popup-content"></div>
</div>

<script>
// Récupérer dynamiquement les compteurs via AJAX
async function fetchCompteurs() {
    try {
        const response = await fetch('../Traitement/consommation_traitement.php?action=get_compteurs');
        const data = await response.json();

        const select = document.getElementById('ID_Compteur');
        select.innerHTML = '<option value="" disabled selected>Choisissez votre compteur</option>';

        if (!data.success) {
            throw new Error(data.error || 'Impossible de récupérer les compteurs.');
        }

        data.compteurs.forEach(compteur => {
            const option = document.createElement('option');
            option.value = compteur.ID_Compteur;
            option.textContent = `Compteur ${compteur.ID_Compteur}`;
            select.appendChild(option);
        });

    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('messageContainer').innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
    }
}

// Récupérer la dernière image associée à un compteur
async function fetchLastCounterImage(compteurId) {
    const popup = document.getElementById('imagePopup');
    const imageContainer = document.getElementById('lastCounterImage');
    
    popup.style.display = 'flex';
    imageContainer.innerHTML = '<div class="loader">Chargement...</div>';

    try {
        const response = await fetch(`../Traitement/consommation_traitement.php?action=get_last_image&compteur_id=${compteurId}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Erreur de récupération');
        }

        if (data.image_url) {
            imageContainer.innerHTML = `
                <div class="image-header">
                    <span>Dernière lecture: ${data.date}</span>
                    <button onclick="closePopup()" class="close-btn">×</button>
                </div>
                <img src="${data.image_url}" alt="Photo du compteur" 
                     onerror="this.onerror=null;this.src='assets/default-counter.jpg'">
            `;
        } else {
            imageContainer.innerHTML = `
                <div class="no-image">
                    <p>Aucune image précédente disponible</p>
                    <button onclick="closePopup()" class="close-btn">Fermer</button>
                </div>
            `;
        }

    } catch (error) {
        imageContainer.innerHTML = `
            <div class="error-message">
                <p>${error.message}</p>
                <button onclick="closePopup()" class="btn">OK</button>
            </div>
        `;
    }
}

// Validation du formulaire pour éviter la double soumission
function validateForm() {
    document.getElementById('submitBtn').disabled = true;
    return true;
}

function closePopup() {
    document.getElementById('imagePopup').style.display = 'none';
}

document.getElementById('imagePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup();
    }
});

// Charger les compteurs au chargement de la page
document.addEventListener('DOMContentLoaded', fetchCompteurs);
</script>

</body>
</html>

<?php include "footer.php"; ?>
