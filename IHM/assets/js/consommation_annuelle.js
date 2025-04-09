class ConsommationController {
    constructor() {
        this.data = [];
        this.filteredData = [];
        this.initEventListeners();
        this.loadData();
    }

    initEventListeners() {
        $('#clientSearch').on('input', () => this.filterData());
        $('#filterYear').on('change', () => this.filterData());
        $('#resetFilters').on('click', () => this.resetFilters());
    }

    async loadData() {
        try {
            const response = await fetch('../../Traitement/consommation_annuelle_controller.php?action=getConsommations');
            this.data = await response.json();
            this.filteredData = [...this.data];
            this.updateYearFilter();
            this.renderTable();
        } catch (error) {
            console.error('Error:', error);
        }
    }

    updateYearFilter() {
        const yearSelect = $('#filterYear');
        yearSelect.empty().append('<option value="all">Toutes</option>');
        
        // Get all unique years
        const years = [...new Set(this.data.map(item => item.Annee))]
                     .sort((a, b) => b - a); // Sort in descending order
                     
        years.forEach(year => {
            yearSelect.append(`<option value="${year}">${year}</option>`);
        });
    }

    renderTable(data = this.filteredData) {
        const rows = data.map(item => `
            <tr>
                <td>${item.Annee}</td>
                <td>${item.ID_Client}</td>
                <td>${item.ID_Compteur}</td>
                <td>${item.NomComplet}</td>
                <td>${item.ConsommationReelle?.toLocaleString() || '0'} kWh</td>
                <td>${item.ConsommationSaisie?.toLocaleString() || 'N/A'} kWh</td>
                <td class="${item.Difference > 50 ? 'difference-negative' : 'difference-positive'}">
                    ${item.Difference?.toLocaleString() || '0'} kWh
                </td>
                <td>
                    <span class="status-badge ${item.Difference > 50 ? 'status-not-tolerated' : 'status-tolerated'}">
                        ${item.Difference > 50 ? 'Dépassement' : 'Conforme'}
                    </span>
                </td>
                <td>
                    ${item.factureExist === 0 && item.Difference > 50 ? 
                        `<button class="btn-envoyer btn-generer" 
                            data-client="${item.ID_Client}"
                            data-compteur="${item.ID_Compteur}"
                            data-year="${item.Annee}">
                            <i class="fas fa-file-invoice"></i> Facturer
                        </button>` : 
                        '<span class="text-muted facture-facturee">Déjà facturé</span>'}
                </td>
            </tr>
        `).join('');

        $('#consommationTable').html(rows);
        this.initFactureButtons();
    }

    initFactureButtons() {
        $('.btn-generer').off('click').on('click', (e) => {
            const target = e.currentTarget;
            this.genererFacture(target.dataset);
        });
    }

    async genererFacture({ client, compteur, year }) {
        if (!confirm(`Générer la facture pour ${year} ?`)) return;

        try {
            const response = await fetch(
                `../../Traitement/consommation_annuelle_controller.php?action=genererFacture&clientId=${client}&compteurId=${compteur}&year=${year}`
            );
            const result = await response.json();
            
            if (result.success) {
                alert('Facture générée avec succès');
                this.loadData();
            } else {
                throw new Error(result.error || 'Erreur inconnue');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(`Échec de la génération: ${error.message}`);
        }
    }

    filterData() {
        const searchTerm = $('#clientSearch').val().toLowerCase().replace(/\s+/g, ' ').trim();
        const yearFilter = $('#filterYear').val();

        this.filteredData = this.data.filter(item => {
            // Concatenate all visible data
            const rowContent = `
                ${item.Annee}
                ${item.ID_Client}
                ${item.ID_Compteur}
                ${item.NomComplet}
                ${item.ConsommationReelle}
                ${item.ConsommationSaisie ?? 'N/A'}
                ${item.Difference}
                ${item.Difference > 50 ? 'Dépassement' : 'Conforme'}
                ${item.factureExist === 0 ? 'Facturer' : 'Déjà facturé'}
            `.toLowerCase().replace(/\s+/g, ' ');

            return (rowContent.includes(searchTerm)) && 
                   (yearFilter === 'all' || item.Annee.toString() === yearFilter);
        });

        this.renderTable();
    }

    resetFilters() {
        $('#clientSearch').val('');
        $('#filterYear').val('all');
        this.filteredData = [...this.data];
        this.renderTable();
    }
}

async function loadSidebar() {
    try {
        const response = await fetch("../Mise_en_page/sidebar.php");
        const sidebarContent = await response.text();
        document.getElementById('sidebar').innerHTML = sidebarContent;
    } catch (error) {
        console.error('Error loading sidebar:', error);
    }
}

async function checkUserAccess() {
    try {
        const response = await fetch('../../Traitement/auth_check.php');
        const data = await response.json();
        if (!data.isAuthorized) {
            window.location.href = '/login'; // Redirect to login if not authorized
        }
    } catch (error) {
        console.error('Error checking user access:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadSidebar();
    checkUserAccess();
    new ConsommationController();
});