document.addEventListener("DOMContentLoaded", function() {
    console.log("Chargement des réclamations...");
    
    // Requête AJAX pour récupérer les réclamations
    fetch('../../Traitement/traitement_reclamation.php?action=consulter_reclamations')
    .then(response => {
        console.log("Statut de la réponse:", response.status);
        // Débogage - afficher le texte brut avant de tenter de le parser en JSON
        return response.text().then(text => {
            console.log("Réponse brute:", text);
            try {
                // Essayer de parser le texte en JSON
                return JSON.parse(text);
            } catch(e) {
                // Si le parsing échoue, afficher l'erreur
                throw new Error("Réponse non-JSON reçue: " + text);
            }
        });
    })
    .then(data => {
        console.log("Données JSON traitées:", data);
        const tableBody = document.getElementById("reclamationsTable");
        tableBody.innerHTML = "";

        if (data && data.error) {
            tableBody.innerHTML = `<tr><td colspan="5">${data.error}</td></tr>`;
            return;
        }

        if (!Array.isArray(data) || data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5">Aucune réclamation trouvée.</td></tr>`;
            return;
        }

        data.forEach(reclamation => {
            const row = `<tr>
                <td>${reclamation.Type_Réclamation || ''}</td>
                <td>${reclamation.Description || ''}</td>
                <td>${reclamation.Date_Réclamation || ''}</td>
                <td>${reclamation.Statut || ''}</td>
                <td>${reclamation.Réponse_Fournisseur ? reclamation.Réponse_Fournisseur : "En attente"}</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    })
    .catch(error => {
        console.error("Erreur:", error);
        document.getElementById("reclamationsTable").innerHTML = 
            `<tr><td colspan="5">Erreur lors du chargement des réclamations: ${error.message}</td></tr>`;
    });
});