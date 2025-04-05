<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consommation - PowerBill</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="consumption-container">
    <h2>Enregistrez votre consommation du mois</h2>

    <div id="messageContainer"></div>

    <form id="consumptionForm" action="../Traitement/consommation_traitement.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const now = new Date();
    document.getElementById('Mois').value = now.getMonth() + 1;
    document.getElementById('Annee').value = now.getFullYear();
    fetchCompteurs();
});

async function fetchCompteurs() {
    const select = document.getElementById('ID_Compteur');
    const messageContainer = document.getElementById('messageContainer');
    
    select.innerHTML = '<option value="" disabled selected>Chargement...</option>';
    messageContainer.innerHTML = '';

    try {
        const response = await fetch('../Traitement/consommation_traitement.php?action=get_compteurs', {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        if (!response.ok) {
            throw new Error(`Erreur HTTP ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Réponse non-JSON:', text);
            throw new Error('Format de réponse inattendu');
        }
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error || 'Erreur inconnue du serveur');
        }

        // Mettre à jour le select
        select.innerHTML = data.compteurs.length > 0
            ? '<option value="" disabled selected>Sélectionnez votre compteur</option>'
            : '<option value="" disabled selected>Aucun compteur disponible</option>';

        data.compteurs.forEach(compteur => {
            const option = document.createElement('option');
            option.value = compteur.ID_Compteur;
            option.textContent = `Compteur ${compteur.ID_Compteur}`;
            select.appendChild(option);
        });

    } catch (error) {
        console.error('Erreur:', error);
        select.innerHTML = '<option value="" disabled selected>Erreur de chargement</option>';
        messageContainer.innerHTML = `
            <div class="alert alert-danger">
                Erreur lors du chargement des compteurs: ${error.message}
            </div>`;
    }
}
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
function showMessage(message, type = 'success') {
    const container = document.getElementById('messageContainer');
    container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    setTimeout(() => container.innerHTML = '', 5000);
}

function validateForm() {
    document.getElementById('submitBtn').disabled = true;
    return true;
}

function closePopup() {
    document.getElementById('imagePopup').style.display = 'none';
}

document.getElementById('imagePopup').addEventListener('click', (e) => {
    if (e.target === this) closePopup();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePopup();
});
</script>

</body>
</html>