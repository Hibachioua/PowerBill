class AnomalieController {
    constructor() {
        this.allAnomalies = [];
        this.initEventListeners();
        this.loadAnomalies();
        
        // Rafraîchissement automatique toutes les 5 secondes
        setInterval(() => this.loadAnomalies(), 5000);
    }

    initEventListeners() {
        $('#globalSearch').on('keyup', () => this.applyFilters());
        $(document).on('click', '.btn-corriger', (e) => this.openCorrectionModal(e));
        $('#confirmCorrection').on('click', () => this.corrigerAnomalie());
        $(document).on('click', '.compteur-img', (e) => this.showImageModal(e));
    }

    async loadAnomalies() {
console.log("Chargement des anomalies..."); // Log au début de la fonction
try {
    const response = await fetch('../../Traitement/AnomalieController.php?action=getAnomalies');
    
    if (!response.ok) {
        throw new Error('Erreur réseau');
    }
    
    const data = await response.json();
    console.log("Réponse reçue:", data); // Log de la réponse de l'API
    
    if (data && data.length > 0) {
        this.allAnomalies = data;
        this.displayAnomalies(this.allAnomalies);
    } else {
        console.log("Aucune anomalie trouvée."); // Log si aucune anomalie n'est trouvée
        $('#anomalie-table-body').html('<tr><td colspan="8" class="text-center">Aucune anomalie trouvée.</td></tr>');
    }
} catch (error) {
    console.error("Erreur:", error); // Log des erreurs
}
}

    displayAnomalies(anomalies) {
        const html = anomalies.length > 0 
            ? anomalies.map(anomalie => this.createAnomalieRow(anomalie)).join('')
            : '<tr><td colspan="8" class="text-center">Aucune anomalie trouvée avec ces critères.</td></tr>';
        
        $('#anomalie-table-body').html(html);
    }

    createAnomalieRow(anomalie) {
        return `
            <tr>
                <td>${anomalie.ID_Client}</td>
                <td>${anomalie.ID_Compteur}</td>
                <td>${anomalie.Mois_actuel}/${anomalie.Annee_actuel}</td>
                <td>${anomalie.Consommation_precedent} kWh</td>
                <td>
                    ${anomalie.Image_precedent 
                        ? `<img src="../../${anomalie.Image_precedent}" class="compteur-img" alt="Compteur précédent">`
                        : 'N/A'}
                </td>
                <td>${anomalie.Consommation_actuelle} kWh</td>
                <td>
                    ${anomalie.Image_actuelle 
                        ? `<img src="../../${anomalie.Image_actuelle}" class="compteur-img" alt="Compteur actuel">`
                        : 'N/A'}
                </td>
                <td>
                    <button class="btn-corriger" data-id="${anomalie.ID_Consommation}">
                        Corriger
                    </button>
                </td>
            </tr>
        `;
    }

    openCorrectionModal(event) {
const button = $(event.currentTarget);
const row = button.closest('tr');
const anomalieId = button.data('id');
const currentConsommation = row.find('td:eq(5)').text().replace(' kWh', '');
const periode = row.find('td:eq(2)').text().split('/');
const mois = periode[0];
const annee = periode[1];
const idCompteur = row.find('td:eq(1)').text();

$('#anomalieId').val(anomalieId);
$('#nouvelleConsommation').val(currentConsommation);
$('#mois').val(mois);
$('#annee').val(annee);
$('#idCompteur').val(idCompteur);

const modal = new bootstrap.Modal(document.getElementById('correctionModal'));
modal.show();
}

async corrigerAnomalie() {
const id = $('#anomalieId').val();
const nouvelleConsommation = $('#nouvelleConsommation').val();
const mois = $('#mois').val();
const annee = $('#annee').val();
const idCompteur = $('#idCompteur').val();

if (!id || !nouvelleConsommation || !mois || !annee || !idCompteur) {
    alert('Veuillez vérifier tous les champs requis');
    return;
}

try {
    const response = await fetch('../../Traitement/AnomalieController.php?action=corrigerAnomalie', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&nouvelleConsommation=${nouvelleConsommation}&mois=${mois}&annee=${annee}&idCompteur=${idCompteur}`
    });
    
    if (!response.ok) {
        throw new Error('Erreur réseau');
    }
    
    const result = await response.json();
    
    if (result.status === "success") {
        alert('Anomalie corrigée avec succès');
        $('#correctionModal').modal('hide');
        this.loadAnomalies();
    } else {
        throw new Error(result.message || 'Échec de la correction');
    }
} catch (error) {
    console.error("Erreur:", error);
    alert(error.message || "Une erreur s'est produite lors de la correction.");
}
}
    showImageModal(event) {
        const imgSrc = $(event.currentTarget).attr('src');
        $('#modalImage').attr('src', imgSrc);
        
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    applyFilters() {
const searchText = $('#globalSearch').val().toLowerCase();

const filtered = this.allAnomalies.filter(anomalie => {
    // Création d'une chaîne de recherche combinée
    const searchString = [
        anomalie.ID_Client.toString(),
        anomalie.ID_Compteur.toString(),
        `${anomalie.Mois_actuel}/${anomalie.Annee_actuel}`,
        anomalie.Consommation_precedent.toString(),
        anomalie.Consommation_actuelle.toString(),
        anomalie.Image_precedent ? 'photo' : 'n/a',
        anomalie.Image_actuelle ? 'photo' : 'n/a'
    ].join('|').toLowerCase();

    return searchString.includes(searchText);
});

this.displayAnomalies(filtered);
}
}

// Initialisation du contrôleur lorsque le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    new AnomalieController();
});
