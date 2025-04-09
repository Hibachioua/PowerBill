<?php
require_once "../../Traitement/auth_check.php";
require_once "../../Traitement/dashboard_traitement.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamations fournisseur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/reclamation_fournisseur.css">

</head>
<body>
<?php include "../Mise_en_page/sidebar.php"; ?>
<div class="main-content">
    <div class="dashboard-header">
        <div class="breadcrumb">PowerBill / Gestion des réclamations</div>
        <h2>Réclamations fournisseur</h2>
    </div>

    <div class="invoice-stats">
        <div class="invoice-stat">
            <h4>Traiter</h4>
            <div class="number" id="count-traite">0</div>
        </div>
        <div class="invoice-stat">
            <h4>En cours</h4>
            <div class="number" id="count-encours">0</div>
        </div>
    </div>

    <div class="table-container">
        <table class="table-custom">
            <thead>
                <tr>
                    <th data-filter="client">
                        Client <i class="bi bi-chevron-down sort-icon"></i>
                        <div class="filter-dropdown" id="client-filter">
                            <input type="text" class="form-control mb-2" placeholder="Rechercher...">
                            <div class="filter-option" data-value="">Tous</div>
                            <!-- Options will be populated by JavaScript -->
                        </div>
                    </th>
                    <th data-filter="statut">
                        Status <i class="bi bi-chevron-down sort-icon"></i>
                        <div class="filter-dropdown" id="statut-filter">
                            <div class="filter-option selected" data-value="">Tous</div>
                            <div class="filter-option" data-value="Traité">Traité</div>
                            <div class="filter-option" data-value="En cours">En cours</div>
                        </div>
                    </th>
                    <th data-filter="type">
                        Type of complaint <i class="bi bi-chevron-down sort-icon"></i>
                        <div class="filter-dropdown" id="type-filter">
                            <div class="filter-option selected" data-value="">Tous</div>
                            <div class="filter-option" data-value="Facture">Facture</div>
                            <div class="filter-option" data-value="Fulte interne">Fulte interne</div>
                            <div class="filter-option" data-value="Fulte externe">Fulte externe</div>
                        </div>
                    </th>
                    <th data-filter="date">
                        Date <i class="bi bi-chevron-down sort-icon"></i>
                        <div class="filter-dropdown" id="date-filter">
                            <div class="filter-option selected" data-value="">Tous</div>
                            <div class="filter-option" data-value="today">Aujourd'hui</div>
                            <div class="filter-option" data-value="week">Cette semaine</div>
                            <div class="filter-option" data-value="month">Ce mois</div>
                        </div>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal pour voir la description -->
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Description de la réclamation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modal-description-content"></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour traiter la réclamation -->
<div class="modal fade" id="traitementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Traiter la réclamation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-traitement">
                    <input type="hidden" id="modal-reclamation-id">
                    <div class="mb-3">
                        <label for="modal-description" class="form-label">Description</label>
                        <textarea class="form-control" id="modal-description" rows="3" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="modal-reponse" class="form-label">Votre réponse</label>
                        <textarea class="form-control" id="modal-reponse" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn">Envoyer la réponse</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/reclamation_fournisseur.js"></script>

</body>
</html>