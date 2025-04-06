class FactureController {
    constructor() {
        this.allFactures = [];
        this.initEventListeners();
        this.loadFactures();
        
        // Rafraîchir toutes les 5 secondes
        setInterval(() => this.loadFactures(), 5000);
    }

    initEventListeners() {
        $('#globalSearch').on('keyup', () => this.applyFilters());
        $('#filterYear, #filterMonth, #filterAmount').on('change', () => this.applyFilters());
        $('#resetFilters').on('click', () => this.resetFilters());
        $(document).on('click', '.btn-payer', (e) => this.payerFacture(e));
    }

    async loadFactures() {
        try {
            const response = await fetch('../Traitement/FactureController.php?action=getFactures');
            
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            
            const data = await response.json();
            
            if (data && data.length > 0) {
                // Tri des factures par date (les plus récentes en premier)
                this.allFactures = data.sort((a, b) => {
                    const dateA = new Date(a.Date_émission || `${a.Annee}-${a.Mois}-01`);
                    const dateB = new Date(b.Date_émission || `${b.Annee}-${b.Mois}-01`);
                    return dateB - dateA;
                });
                
                this.displayFactures(this.allFactures);
                this.populateYearFilter(this.allFactures);
                
               
            } else {
                $('#facture-table-body').html('<tr><td colspan="7" class="text-center">Aucune facture trouvée.</td></tr>');
            }
        } catch (error) {
            console.error("Erreur:", error);
            $('#facture-table-body').html('<tr><td colspan="7" class="text-center">Erreur lors du chargement des factures.</td></tr>');
        }
    }

    displayFactures(factures) {
        const html = factures.length > 0 
            ? factures.map(facture => this.createFactureRow(facture)).join('')
            : '<tr><td colspan="7" class="text-center">Aucune facture trouvée avec ces critères.</td></tr>';
        
        $('#facture-table-body').html(html);
    }

    createFactureRow(facture) {
        const isPaid = facture.Statut_paiement === 'paye';
        const statusHtml = isPaid 
            ? '<span class="status-payed">Payé</span>'
            : `<button class="btn-payer" data-id="${facture.ID_Facture}">Payer</button>`;
        
        const dateFacturation = facture.Date_émission 
            ? new Date(facture.Date_émission).toLocaleDateString('fr-FR') 
            : 'N/A';
        
        return `
            <tr>
                <td>${facture.Mois}/${facture.Annee}</td>
                <td>${facture.ID_Compteur || 'N/A'}</td>
                <td>${facture.Prenom || ''} ${facture.Nom || ''}</td>
                <td>${dateFacturation}</td>
                <td>${facture.Qté_consommé || '0'} kWh</td>
                <td>${parseFloat(facture.Prix_TTC || 0).toFixed(2)} DH</td>
                <td>${statusHtml}</td>
            </tr>
        `;
    }

    populateYearFilter(factures) {
        const years = [...new Set(factures.map(f => f.Annee))].sort((a, b) => b - a);
        const $yearSelect = $('#filterYear').empty().append('<option value="">Toutes les années</option>');
        years.forEach(year => {
            $yearSelect.append(`<option value="${year}">${year}</option>`);
        });
    }

    applyFilters() {
        const searchText = $('#globalSearch').val().toLowerCase();
        const yearFilter = $('#filterYear').val();
        const monthFilter = $('#filterMonth').val();
        const amountFilter = $('#filterAmount').val();

        const filtered = this.allFactures.filter(facture => {
            if (searchText) {
                const nomComplet = `${facture.Prénom || ''} ${facture.Nom || ''}`.toLowerCase();
                const compteur = facture.ID_Compteur ? facture.ID_Compteur.toString() : '';
                
                if (!nomComplet.includes(searchText) && !compteur.includes(searchText)) {
                    return false;
                }
            }

            if (yearFilter && facture.Annee && facture.Annee.toString() !== yearFilter) {
                return false;
            }

            if (monthFilter && facture.Mois && facture.Mois.toString() !== monthFilter) {
                return false;
            }

            if (amountFilter && facture.Prix_TTC) {
                const amount = parseFloat(facture.Prix_TTC);
                if (amountFilter === '0-500' && (amount < 0 || amount > 500)) return false;
                if (amountFilter === '500-1000' && (amount < 500 || amount > 1000)) return false;
                if (amountFilter === '1000-2000' && (amount < 1000 || amount > 2000)) return false;
                if (amountFilter === '2000+' && amount < 2000) return false;
            }

            return true;
        });

        this.displayFactures(filtered);
    }

    resetFilters() {
        $('#globalSearch').val('');
        $('#filterYear').val('');
        $('#filterMonth').val('');
        $('#filterAmount').val('');
        this.displayFactures(this.allFactures);
    }

    async payerFacture(event) {
        const factureID = $(event.currentTarget).data('id');
        if (!factureID || !confirm('Voulez-vous vraiment payer cette facture ?')) {
            return;
        }

        try {
            const response = await fetch(`../Traitement/FactureController.php?action=payerFacture&factureID=${factureID}`);
            
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            
            const result = await response.json();
            
            if (result.status === "success") {
                alert('Paiement effectué avec succès');
                this.loadFactures(); // Recharger les factures après paiement
            } else {
                throw new Error(result.message || 'Erreur lors du paiement');
            }
        } catch (error) {
            console.error("Erreur:", error);
            alert(error.message || "Une erreur s'est produite lors du paiement.");
        }
    }
}

// Initialisation du contrôleur lorsque le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    new FactureController();
});