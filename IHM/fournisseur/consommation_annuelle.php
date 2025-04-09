<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Consommations</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/consommations_annuelles_fournisseur.css">
</head>
<body>
<?php include "../Mise_en_page/sidebar.php"; ?>
<div class="main-content">
    <div class="content-header">
        <div>
            <div class="breadcrumb">PowerBill / Suivi énergétique</div>
            <h2>Suivi des Consommations Annuelles</h2>
        </div>
    </div>
    
    <div class="content-container">
        <div class="filters-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="clientSearch" placeholder="Rechercher ..." class="form-control">
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label for="filterYear">Année</label>
                    <select id="filterYear" class="form-select">
                        <option value="all">Toutes</option>
                        <!-- Options will be added dynamically by JavaScript -->
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button id="resetFilters" class="btn btn-outline-secondary">Réinitialiser</button>
                </div>
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>N° Client</th>
                        <th>N° Compteur</th>
                        <th>Nom Complet</th>
                        <th>Consommation Annuelle (kWh)</th>
                        <th>Consommation Saisie par l'agent (kWh)</th>
                        <th>Différence</th>
                        <th>Remarque</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="consommationTable">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/consommation_annuelle.js"></script>
</body>
</html>


