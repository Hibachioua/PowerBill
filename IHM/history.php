<?php
require_once "../Traitement/history_traitement.php";
require_once "../Traitement/sidebar_controller.php";



// Vérifier les droits d'accès (facultatif - à ajuster selon vos besoins)
// checkUserAccess(); // Décommentez si vous voulez restreindre l'accès

// Préparer les données
$viewData = prepareHistoryData();
$consommations = $viewData['consommations'];
$filtres = $viewData['filtres'];
$years = $viewData['years'];
$clients = $viewData['clients'];
$mois = $viewData['mois'];
$hasFilters = hasActiveFilters($filtres);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Historique des Consommations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/four_dashboard.css">
    <style>
        .filters-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .filters-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .filter-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .filter-badge {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border-radius: 30px;
            padding: 5px 12px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .filter-badge i {
            margin-left: 6px;
            cursor: pointer;
        }
        
        .filter-badge i:hover {
            color: #e74c3c;
        }
        
        .consumption-table th {
            background-color: #f5f9fc;
            color: #3498db;
            font-weight: 600;
        }
        
        .consumption-table tbody tr:hover {
            background-color: #f5f9fc;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-normal {
            background-color: #2ecc71;
            color: white;
        }
        
        .status-anomalie {
            background-color: #e74c3c;
            color: white;
        }
        
        .image-preview {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .export-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        .btn-export {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <div class="breadcrumb">PowerBill / Historique</div>
                <h2>Historique des Consommations</h2>
            </div>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="filterTable()">
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="filters-container">
            <div class="filters-title">
                <i class="fas fa-filter"></i> Filtres
            </div>
            
            <form action="" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="annee" class="form-label">Année</label>
                    <select name="annee" id="annee" class="form-select">
                        <option value="">Toutes les années</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo ($filtres['annee'] == $year) ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="mois" class="form-label">Mois</label>
                    <select name="mois" id="mois" class="form-select">
                        <option value="">Tous les mois</option>
                        <?php foreach ($mois as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($filtres['mois'] == $key) ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="client" class="form-label">Client</label>
                    <select name="client" id="client" class="form-select">
                        <option value="">Tous les clients</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['ID_Client']; ?>" <?php echo ($filtres['client'] == $client['ID_Client']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client['Nom'] . ' ' . $client['Prenom'] . ' (' . $client['CIN'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <?php if ($hasFilters): ?>
                        <a href="history.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Réinitialiser
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            
            <?php if ($hasFilters): ?>
                <div class="filter-badges mt-3">
                    <?php if (!empty($filtres['annee'])): ?>
                        <div class="filter-badge">
                            Année: <?php echo $filtres['annee']; ?>
                            <a href="history.php?<?php echo http_build_query(array_merge($filtres, ['annee' => ''])); ?>">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtres['mois'])): ?>
                        <div class="filter-badge">
                            Mois: <?php echo $mois[$filtres['mois']]; ?>
                            <a href="history.php?<?php echo http_build_query(array_merge($filtres, ['mois' => ''])); ?>">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtres['client'])): ?>
                        <?php 
                        $clientName = '';
                        foreach ($clients as $client) {
                            if ($client['ID_Client'] == $filtres['client']) {
                                $clientName = $client['Nom'] . ' ' . $client['Prenom'];
                                break;
                            }
                        }
                        ?>
                        <div class="filter-badge">
                            Client: <?php echo htmlspecialchars($clientName); ?>
                            <a href="history.php?<?php echo http_build_query(array_merge($filtres, ['client' => ''])); ?>">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- <?php if (!empty($consommations)): ?>
                <div class="export-buttons">
                    <button class="btn btn-sm btn-outline-success btn-export">
                        <i class="fas fa-file-excel"></i> Exporter en Excel
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-export">
                        <i class="fas fa-file-pdf"></i> Exporter en PDF
                    </button>
                    <button class="btn btn-sm btn-outline-primary btn-export">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
            <?php endif; ?> -->
        </div>
        
        <!-- Tableau des consommations -->
        <div class="content-container">
            <?php if (empty($consommations)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucune consommation trouvée.
                    <?php if ($hasFilters): ?>
                        Essayez de modifier vos filtres ou <a href="history.php">réinitialisez-les</a>.
                    <?php endif; ?>
                </div>
            <?php else: ?>
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
                        <tbody>
                            <?php foreach ($consommations as $conso): ?>
                                <tr>
                                    <td><?php echo $conso['ID_Consommation']; ?></td>
                                    <td><?php echo htmlspecialchars($conso['Nom'] . ' ' . $conso['Prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($conso['CIN']); ?></td>
                                    <td><?php echo htmlspecialchars($conso['Email']); ?></td>
                                    <td><?php echo $mois[$conso['Mois']] . ' ' . $conso['Annee']; ?></td>
                                    <td><?php echo $conso['ID_Compteur']; ?></td>
                                    <td><?php echo $conso['Qte_consomme']; ?> kWh</td>
                                    <td>
                                        <?php if (!empty($conso['Image_Compteur'])): ?>
                                            <img src="assets/images/meter.jpg" alt="Compteur" class="image-preview" data-bs-toggle="modal" data-bs-target="#imageModal" data-title="Compteur #<?php echo $conso['ID_Compteur']; ?>" data-image="assets/images/meter.jpg">
                                        <?php else: ?>
                                            <span class="text-muted">Non disponible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($conso['status'] === 'pas d\'anomalie'): ?>
                                            <span class="status-badge status-normal">Normal</span>
                                        <?php else: ?>
                                            <span class="status-badge status-anomalie">Anomalie</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-container">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Précédent</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
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
    <script>
        // Script pour la prévisualisation des images
        document.addEventListener('DOMContentLoaded', function() {
            var imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var title = button.getAttribute('data-title');
                var imageSrc = button.getAttribute('data-image');
                
                var modalTitle = imageModal.querySelector('.modal-title');
                var modalImage = document.getElementById('modalImage');
                
                modalTitle.textContent = title;
                modalImage.src = imageSrc;
            });
        });
        
        // Fonction de recherche dans le tableau
        function filterTable() {
            var input, filter, table, tr, td, i, j, txtValue, found;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("dataTable");
            tr = table.getElementsByTagName("tr");
            
            for (i = 1; i < tr.length; i++) {
                found = false;
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                if (found) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>