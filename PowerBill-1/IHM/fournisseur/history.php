<?php require_once "../../Traitement/sidebar_controller.php"; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Historique des Consommations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/four_dashboard.css">
    <link rel="stylesheet" href="../assets/css/history.css">
</head>
<body>
<?php include __DIR__ . "/../Mise_en_page/sidebar.php"; ?>
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <div class="breadcrumb">PowerBill / Historique</div>
                <h2>Historique des Consommations</h2>
            </div>
            
        </div>
        
        <!-- Filtres -->
        <div class="filters-container">
            <div class="filters-title">
                <i class="fas fa-filter"></i> Filtres
            </div>
            
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="annee" class="form-label">Année</label>
                    <select name="annee" id="annee" class="form-select">
                        <option value="">Toutes les années</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="mois" class="form-label">Mois</label>
                    <select name="mois" id="mois" class="form-select">
                        <option value="">Tous les mois</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="client" class="form-label">Client</label>
                    <select name="client" id="client" class="form-select">
                        <option value="">Tous les clients</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <button type="button" id="resetFilters" class="btn btn-outline-secondary" style="display: none;">
                        <i class="fas fa-times"></i> Réinitialiser
                    </button>
                </div>
            </form>
            
            <div id="filterBadges" class="filter-badges mt-3">
            </div>
        </div>
        
        <!-- Tableau des consommations -->
        <div class="content-container">
            <div id="alert-container">
            </div>
            
            <div class="table-responsive">
                <table class="table consumption-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>CIN</th>
                            <th>Email</th>
                            <th>Période</th>
                            <th>Compteur</th>
                            <th>Consommation</th>
                            <th>Image</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm" id="pagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Modal pour afficher l'image -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image du compteur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" alt="Image du compteur">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="../assets/js/history.js"></script>
</body>
</html>