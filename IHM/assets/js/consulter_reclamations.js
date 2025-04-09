// (Le code JavaScript reste identique à la version précédente)
document.addEventListener("DOMContentLoaded", function() {
    console.log("Chargement des réclamations...");
    
    fetch('../../Traitement/traitement_reclamation.php?action=consulter_reclamations')
    .then(response => response.text().then(text => {
        console.log("Réponse brute:", text);
        try {
            return JSON.parse(text);
        } catch(e) {
            throw new Error("Réponse non-JSON reçue: " + text);
        }
    }))
    .then(data => {
        console.log("Données JSON traitées:", data);
        const container = document.getElementById("reclamationsList");
        
        if (data && data.error) {
            showError(container, data.error);
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            showEmptyState(container);
            return;
        }

        renderReclamations(container, data);
    })
    .catch(error => {
        console.error("Erreur:", error);
        showError(document.getElementById("reclamationsList"), error.message);
    });
});

// ... (le reste du code JavaScript reste inchangé)

function renderReclamations(container, reclamations) {
    container.innerHTML = '';
    
    reclamations.forEach(reclamation => {
        const card = document.createElement('div');
        card.className = 'reclamation-card';
        
        const formattedDate = formatDate(reclamation.Date_Réclamation);
        
        card.innerHTML = `
            <div class="card-header">
                <div class="reclamation-type">${escapeHtml(reclamation.Type_Réclamation || '')}</div>
                <div class="reclamation-date">${formattedDate}</div>
            </div>
            <div class="card-body">
                <div class="description-section">
                    <h3 class="section-title">Description</h3>
                    <div class="section-content">
                        ${escapeHtml(reclamation.Description || '')}
                    </div>
                </div>
                
                <div class="status-section">
                    <h3 class="section-title">Statut</h3>
                    <div class="status-badge ${reclamation.Statut === 'Traité' ? 'status-treated' : 'status-pending'}">
                        ${escapeHtml(reclamation.Statut || '')}
                    </div>
                </div>
                
                <div class="response-section">
                    <h3 class="section-title">Réponse du fournisseur</h3>
                    <div class="section-content ${reclamation.Réponse_Fournisseur ? '' : 'no-response'}">
                        ${escapeHtml(reclamation.Réponse_Fournisseur || 'En attente de réponse')}
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function showEmptyState(container) {
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>Aucune réclamation trouvée</h4>
            <p>Vous n'avez pas encore fait de réclamation.</p>
        </div>
    `;
}

function showError(container, message) {
    container.innerHTML = `
        <div class="alert alert-danger">
            Erreur lors du chargement des réclamations: ${escapeHtml(message)}
        </div>
    `;
}

function formatDate(dateString) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR');
    } catch {
        return dateString;
    }
}

function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

