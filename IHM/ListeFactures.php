<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Factures</title>
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
            <h2>Vos Factures</h2>
        </div>
        <div class="btn-container">
            <button id="consulterAnciennesFactures" class="btn btn-primary">
                <i class="fas fa-folder-open"></i> Consulter les anciennes factures
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
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filterMonth">Mois</label>
                    <select id="filterMonth" class="form-select">
                        <option value="">Tous les mois</option>
                        <option value="1">Janvier</option>
                        <option value="2">Février</option>
                        <!-- ... autres mois ... -->
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
                        <th>Période</th>
                        <th>Compteur</th>
                        <th>Client</th>
                        <th>Date Facture</th>
                        <th>Consommation</th>
                        <th>Montant</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="facture-table-body">
                    <!-- Rempli dynamiquement -->
                </tbody>
            </table>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        let allFactures = [];
        
        function chargerFactures() {
            $.ajax({
                url: '../Traitement/traitement_listefacture.php',
                type: 'GET',
                data: { action: 'getFactures' },
                dataType: 'json',
                success: function(response) {
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
                const row = `
                <tr>
                    <td>${facture.Mois}/${facture.Annee}</td>
                    <td>${facture.ID_Compteur}</td>
                    <td>${facture.Nom} ${facture.Prénom}</td>
                    <td>${facture.Date_émission}</td>
                    <td>${facture.Qté_consommé} kWh</td>
                    <td>${facture.Prix_TTC} DH</td>
                    <td>
                        <span class="badge ${facture.Statut_paiement === 'paye' ? 'bg-success' : 'bg-warning'}">
                            ${facture.Statut_paiement}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary payer-btn" data-id="${facture.ID_Facture}">
                            <i class="fas fa-money-bill-wave"></i> Payer
                        </button>
                        <button class="btn btn-sm btn-secondary download-btn" data-id="${facture.ID_Facture}">
                            <i class="fas fa-download"></i> PDF
                        </button>
                    </td>
                </tr>`;
                tbody.append(row);
            });
        }

        // Initialisation
        chargerFactures();
        setInterval(chargerFactures, 30000); // Rafraîchissement toutes les 30s

        // Gestion des événements
        $(document).on('click', '.payer-btn', function() {
            const factureID = $(this).data('id');
            payerFacture(factureID);
        });

        $(document).on('click', '.download-btn', function() {
            const factureID = $(this).data('id');
            window.location.href = `../Traitement/telecharger_facture.php?factureID=${factureID}`;
        });

        $('#consulterAnciennesFactures').click(function() {
            window.location.href = 'ListeFacturesPayees.php';
        });
    });
    </script>
</body>
</html>