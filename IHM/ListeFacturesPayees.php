<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factures Payées</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/styleListeFacture.css?v=1.0">
    <link rel="stylesheet" href="assets/filter-styles.css"> 
</head>
<body>
    <div class="containerListe">
        <div class="title">
            <h2>Factures Payées</h2>
        </div>
        <div class="btn-container">
            <button id="btnRetour" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </button>
        </div>

                <!-- Barre de recherche et filtres -->
<div class="filters-container">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="globalSearch" placeholder="Rechercher par nom, numéro de compteur, etc..." class="form-control">
    </div>
    
    <div class="filter-row">

        
        <div class="filter-group">
            <label for="filterYear">Année</label>
            <select id="filterYear" class="form-select">
                <option value="">Toutes les années</option>
                <!-- Les options seront ajoutées dynamiquement -->
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filterMonth">Mois</label>
            <select id="filterMonth" class="form-select">
                <option value="">Tous les mois</option>
                <option value="1">Janvier</option>
                <option value="2">Février</option>
                <option value="3">Mars</option>
                <option value="4">Avril</option>
                <option value="5">Mai</option>
                <option value="6">Juin</option>
                <option value="7">Juillet</option>
                <option value="8">Août</option>
                <option value="9">Septembre</option>
                <option value="10">Octobre</option>
                <option value="11">Novembre</option>
                <option value="12">Décembre</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filterAmount">Montant</label>
            <select id="filterAmount" class="form-select">
                <option value="">Tous les montants</option>
                <option value="0-500">Moins de 500 DH</option>
                <option value="500-1000">500 à 1000 DH</option>
                <option value="1000-2000">1000 à 2000 DH</option>
                <option value="2000+">Plus de 2000 DH</option>
            </select>
        </div>
    </div>
    
    <div class="filter-actions">
        <button id="resetFilters" class="btn btn-outline-secondary">Réinitialiser</button>
        
    </div>
</div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Période de Consommation</th>
                        <th>Numéro du Compteur</th>
                        <th>Nom Complet</th>
                        <th>Date de Facturation</th>
                        <th>Consommation</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="facture-table-body">
                    <!-- Les données seront ajoutées ici dynamiquement avec JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    <?php include('footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       $(document).ready(function () {
    let allFactures = []; // Stocker toutes les factures pour le filtrage

    // Fonction pour charger les factures
    function chargerFactures() {
        $.ajax({
            url: '../Traitement/traitement_listefacture.php',
            type: 'GET',
            data: { action: 'getFactures' },
            dataType: 'json',
            success: function (data) {
                console.log("Données reçues : ", data);
                if (data && data.length > 0) {
                    allFactures = data;
                    afficherFactures(data);
                    populateYearFilter(data);
                } else {
                    $('#facture-table-body').html('<tr><td colspan="8">Aucune facture trouvée.</td></tr>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Erreur AJAX:", error);
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
                <td>${facture.Nom} ${facture.Prénom}</td>
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
                window.location.href = `../Traitement/telecharger_facture.php?factureID=${factureID}`;  // Redirige pour télécharger la facture
            });

            // Gérer le bouton Retour
            $(document).on('click', '#btnRetour', function () {
                window.location.href = '../Traitement/traitement_listefacture.php?action=retour';
            });
        });
    </script>
</body>
</html>