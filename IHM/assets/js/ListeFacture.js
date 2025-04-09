
$(document).ready(function() {
    let allFactures = [];
    
    function chargerFactures() {
        $.ajax({
            url: '../../Traitement/traitement_listefacture.php',
            type: 'GET',
            data: { action: 'getFactures' },
            dataType: 'json',
            success: function(response) {
                console.log(response);  // Affiche la réponse complète du serveur
                if (response.status === "success") {
                    allFactures = response.data;
                    afficherFactures(response.data);
                    populateYearFilter(response.data);
                } else {
                    showError(response.message || "Erreur de chargement");
                }
            },
            error: function(xhr, status, error) {
                showError("Erreur de connexion au serveur");
            }
        });
    }

    function afficherFactures(factures) {
const tbody = $('#facture-table-body').empty();

if (factures.length === 0) {
    tbody.append('<tr><td colspan="8">Aucune facture trouvée</td></tr>');
    return;
}

factures.forEach(facture => {
    // Vérification du type de facture et ajustement de l'affichage de la période
    let periode = '';
    if (facture.type === 'complementaire') {
        // Afficher uniquement l'année pour les factures complémentaires
        periode = facture.Annee;
    } else {
        // Afficher le mois et l'année pour les autres types
        periode = `${facture.Mois}/${facture.Annee}`;
    }

    // Affichage des données dans le tableau
    const row = `
    <tr>
        <td>${periode}</td>
        <td>${facture.ID_Compteur}</td>
        <td>${facture.Nom || 'Nom non disponible'} ${facture.Prenom || 'Prénom non disponible'}</td>
        <td>${facture.Date_émission}</td>
        <td>${facture.Qté_consommé || 'Consommation non disponible'} kWh</td>
        <td>${facture.Prix_TTC} DH</td>
        <td>
            <span class="badge ${facture.Statut_paiement === 'paye' ? 'bg-success' : 'bg-warning'}">
                ${facture.Statut_paiement}
            </span>
        </td>
        <td>
            <button class="btn btn-sm btn-secondary download-btn" data-id="${facture.ID_Facture}">
                <i class="fas fa-download"></i> PDF
            </button>
        </td>
    </tr>`;
    tbody.append(row);
});
}

    // Fonction pour télécharger la facture au format PDF
$(document).on('click', '.download-btn', function() {
const factureID = $(this).data('id');
if (!factureID || isNaN(factureID)) {
    alert("Facture invalide !");
    return;
}

// Trouver la facture correspondante
const facture = allFactures.find(f => f.ID_Facture === factureID);

// Vérifier le type de la facture et rediriger en conséquence
if (facture) {
    if (facture.type === 'complementaire') {
        window.location.href = `../../Traitement/telecharger_facture_complementaire.php?factureCompID=${factureID}`;
    } else {
        window.location.href = `../../Traitement/telecharger_facture.php?factureID=${factureID}`;
    }
} else {
    alert("Facture non trouvée !");
}
});


    // Redirection pour consulter les anciennes factures
    $('#consulterAnciennesFactures').click(function() {
        window.location.href = 'ListeFacturesPayees.php';
    });

    // Charger les factures au démarrage et toutes les 30 secondes
    chargerFactures();
    setInterval(chargerFactures, 5000);
});
function showNotification(message) {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
    
    notificationMessage.textContent = message;
    notification.style.display = 'flex';

    // Cacher la notification après 10 secondes
    setTimeout(() => {
        notification.style.display = 'none';
    }, 10000);
}

function closeNotification() {
    const notification = document.getElementById('notification');
    notification.style.display = 'none';
}

// Vérifier si un message est passé dans l'URL
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    if (message) {
        showNotification(decodeURIComponent(message));
    }
});