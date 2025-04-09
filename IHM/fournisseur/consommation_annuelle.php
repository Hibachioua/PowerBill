<?php
require_once "../../Traitement/auth_check.php";
checkUserAccess(3);
require_once "../../Traitement/sidebar_controller.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Consommations</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .btn-envoyer {
            background-color:rgb(246, 156, 54);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: all 0.3s;
        }

        .btn-envoyer:hover {
            background-color:rgb(247, 119, 7);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
        }

        .status-tolerated {
            background-color: #27ae60;
            color: white;
        }

        .status-not-tolerated {
            background-color: #e74c3c;
            color: white;
        }

        .difference-positive {
            color: #27ae60;
            font-weight: 500;
        }

        .difference-negative {
            color: #e74c3c;
            font-weight: 500;
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

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
                margin-left: 0;
                width: 100%;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
        }

        .facture-facturee {
    color:  #27ae60;  /* Change la couleur du texte en vert */
    font-weight: bold;  /* Optionnel : pour rendre le texte en gras */
}

    </style>
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
                        <!-- Les options seront ajoutées dynamiquement par JavaScript -->
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
                            <th>Consommation Saisie par l'agent(kWh)</th>
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



