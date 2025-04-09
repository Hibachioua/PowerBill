$(document).ready(function () {
    let allFactures = []; // Stocker toutes les factures pour le filtrage

    function chargerFactures() {
    $.ajax({
        url: '../../Traitement/traitement_listefacture.php',
        type: 'GET',
        data: { action: 'getFacturesPayees' },
        dataType: 'json',
        success: function (data) {
            console.log("Données reçues : ", data);
            
            if (data.status === 'success' && data.data && data.data.length > 0) {
                allFactures = data.data;
                afficherFactures(data.data);
                populateYearFilter(data.data);
            } else {
                $('#facture-table-body').html(
                    '<tr><td colspan="8">Aucune facture trouvée.</td></tr>'
                );
            }
        },
        error: function (xhr, status, error) {
            console.error("Erreur AJAX:", error);
            $('#facture-table-body').html(
                '<tr><td colspan="8">Erreur lors du chargement des factures</td></tr>'
            );
        }
    });
}
    // Remplir le filtre d'année
    function populateYearFilter(factures) {
        const years = [...new Set(factures.map(f => f.Annee))].sort((a, b) => b - a);
        const $yearSelect = $('#filterYear').empty().append('<option value="">Toutes les années</option>');
        years.forEach(year => {
            $yearSelect.append(`<option value="${year}">${year}</option>`);
        });
    }

    // Afficher les factures
    function afficherFactures(factures) {
        let html = factures.length > 0 ? factures.map(facture => `
            <tr>
                <td>${facture.Mois} / ${facture.Annee}</td>
                <td>${facture.ID_Compteur}</td>
                <td>${facture.Nom} ${facture.Prenom}</td>
                <td>${facture.Date_émission}</td>
                <td>${facture.Qté_consommé} kWh</td>
                <td>${facture.Prix_TTC} DH</td>
            
                <td>
                    <button class="download-btn" data-id="${facture.ID_Facture}">Télécharger</button>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="8">Aucune facture trouvée.</td></tr>';
        
        $('#facture-table-body').html(html);
    }

    // Appliquer les filtres
    function applyFilters() {
        const searchText = $('#globalSearch').val().toLowerCase();
        const yearFilter = $('#filterYear').val();
        const monthFilter = $('#filterMonth').val();
        const amountFilter = $('#filterAmount').val();

        const filtered = allFactures.filter(facture => {
            // Filtre de recherche globale
            if (searchText && !(
                facture.Nom.toLowerCase().includes(searchText) ||
                facture.Prénom.toLowerCase().includes(searchText) ||
                facture.ID_Compteur.toString().includes(searchText))
            ) {
                return false;
            }

            // Filtre par année (exact match)
            if (yearFilter && facture.Annee.toString() !== yearFilter) {
                return false;
            }

            // Filtre par mois (exact match)
            if (monthFilter && facture.Mois.toString() !== monthFilter) {
                return false;
            }

            // Filtre par montant
            if (amountFilter) {
                const amount = parseFloat(facture.Prix_TTC);
                if (amountFilter === '0-500' && (amount < 0 || amount > 500)) return false;
                if (amountFilter === '500-1000' && (amount < 500 || amount > 1000)) return false;
                if (amountFilter === '1000-2000' && (amount < 1000 || amount > 2000)) return false;
                if (amountFilter === '2000+' && amount < 2000) return false;
            }

            return true;
        });

        afficherFactures(filtered);
    }

    // Écouteurs d'événements
    $('#globalSearch').on('keyup', applyFilters);
    $('#filterYear, #filterMonth, #filterAmount').on('change', applyFilters);

    $('#resetFilters').on('click', function() {
        $('#globalSearch').val('');
        $('#filterYear').val('');
        $('#filterMonth').val('');
        $('#filterAmount').val('');
        afficherFactures(allFactures);
    });

    // Chargement initial
    chargerFactures();

    // Rafraîchir toutes les 5 secondes
    setInterval(chargerFactures, 5000);

            // Gérer le téléchargement de la facture
            $(document).on('click', '.download-btn', function () {
                let factureID = $(this).data('id');
                window.location.href = `../../Traitement/telecharger_facture.php?factureID=${factureID}`;  // Redirige pour télécharger la facture
            });

            // Gérer le bouton Retour
            $(document).on('click', '#btnRetour', function () {
                window.location.href = '../../Traitement/traitement_listefacture.php?action=retour';
            });
        });