<?php
require_once "../Traitement/auth_check.php";
checkUserAccess(3);
?>
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
    
    <style>
        body {
            display: flex;
            background-color: #e9f3fc;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
        }
        
        .content-header {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-header h2 {
            color: #3498db;
            margin: 0;
        }
        
        .content-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .filters-container {
            background: linear-gradient(135deg,rgb(205, 226, 246) 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.08);
        }

        .search-box {
            position: relative;
            margin-bottom: 15px;
        }
        
        .search-box i {
            position: absolute;
            left: 10px;
            top: 10px;
            color: #777;
        }
        
        .search-box input {
            padding-left: 35px;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 150px;
            display: flex;
            justify-content: flex-end;
            flex-direction: column;
        }
        
        .filter-group label {
            margin-bottom: 8px;
            display: block;
            font-weight: 500;
            color:rgb(21, 79, 118);
            text-align: center;
        }
        
        .filter-actions {
            text-align: right;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        
        thead {
            background-color: #f5f9fc;
        }
        
        th {
            color: #3498db;
            text-align: center;
            padding: 12px;
            font-weight: 600;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background-color: #f5f9fc;
        }
        
        .btn-payer {
            background-color:rgb(246, 156, 54);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: background-color 0.3s;
        }
        
        .btn-payer:hover {
            background-color:rgb(247, 119, 7);
        }
        
        .status-payed {
            color: #27ae60;
            font-weight: 500;
        }
        
        .alert {
            margin-bottom: 20px;
        }

        #resetFilters {
            background-color:rgb(37, 82, 128);
            color:rgb(216, 229, 243);
            border: 1px solid #dee2e6;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        #resetFilters:hover {
            background-color:rgb(57, 154, 251);
            color:rgb(216, 229, 243);
            border-color: #ced4da;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        #resetFilters:active {
            transform: translateY(0);
            box-shadow: none;
        }
        
        #resetFilters i {
            margin-right: 6px;
        }

        .last-update {
            font-size: 0.8em;
            color: #7f8c8d;
            margin-left: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    
    <div class="main-content">
        <div class="content-header">
            <div>
                <div class="breadcrumb">PowerBill / Gestion des utilisateurs</div>
                <h2>Vos Factures <span class="last-update" id="lastUpdate"></span></h2>
            </div>
        </div>
        
        <div class="content-container">
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
                            <th>Période</th>
                            <th>Compteur</th>
                            <th>Client</th>
                            <th>Date Facturation</th>
                            <th>Consommation</th>
                            <th>Montant</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody id="facture-table-body">
                        <!-- Les données seront ajoutées ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    class FactureController {
        constructor() {
            this.allFactures = [];
            this.initEventListeners();
            this.loadFactures();
            
            // Rafraîchir toutes les 5 secondes
            setInterval(() => this.loadFactures(), 1000);
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
                    
                    // Mettre à jour l'heure de dernière actualisation
                    const now = new Date();
                    document.getElementById('lastUpdate').textContent = `Dernière actualisation: ${now.toLocaleTimeString()}`;
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
    </script>
</body>
</html>