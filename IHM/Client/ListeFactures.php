<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Factures</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/styleListeFacture.css?v=1.0">
    <link rel="stylesheet" href="../assets/css/filter-styles.css">
</head>
<body>
<?php include __DIR__ . "/../Mise_en_page/header_client.php"; ?>

<div class="containerListe">
        <div class="title">
            <h2>Vos Factures</h2>
        </div>
        <div class="btn-container">
            <button id="consulterAnciennesFactures" class="btn btn-primary"><i class="fas fa-folder-open"></i> Consulter les anciennes factures</button>
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


        <!-- Tableau des factures -->
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
                    <!-- Dynamically filled by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../Mise_en_page/footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/listeFacture.js"></script>
</body>
</html>