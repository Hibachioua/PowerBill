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
        const response = await fetch('../../Traitement/consommation_traitement.php?action=get_compteurs', {
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
        const response = await fetch(`../../Traitement/consommation_traitement.php?action=get_last_image&compteur_id=${compteurId}`);
        const text = await response.text();  // Get raw response
        console.log("Response from server:", text);  // Debugging purpose

        const data = JSON.parse(text); // Parse JSON manually
        
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
        console.error("Error fetching image:", error);
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

document.addEventListener("DOMContentLoaded", function () {
    fetch('../../Traitement/consommation_traitement?action=get_message') 
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                afficherMessage(data.message, "success");
            }
            if (data.error) {
                afficherMessage(data.error, "error");
            }
        })
        .catch(error => console.error("Erreur récupération message:", error));
});

function afficherMessage(message, type) {
    const messageDiv = document.createElement("div");
    messageDiv.textContent = message;
    messageDiv.style.padding = "10px";
    messageDiv.style.margin = "10px 0";
    messageDiv.style.borderRadius = "5px";
    messageDiv.style.fontWeight = "bold";
    
    if (type === "success") {
        messageDiv.style.backgroundColor = "#d4edda";
        messageDiv.style.color = "#155724";
    } else {
        messageDiv.style.backgroundColor = "#f8d7da";
        messageDiv.style.color = "#721c24";
    }

    document.body.prepend(messageDiv); 
}

function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}

function showNotification(message) {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
    
    notificationMessage.textContent = message;
    notification.style.display = 'flex';
    setTimeout(() => {
        notification.style.display = 'none';
    }, 10000);
}

function closeNotification() {
    const notification = document.getElementById('notification');
    notification.style.display = 'none';
}

const errorMessage = getURLParameter('error');
if (errorMessage) {
    showNotification(errorMessage);
}
