<?php
require_once "../../Traitement/auth_check.php";
require_once "../../Traitement/dashboard_traitement.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Anomalies</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/liste_anomalies.css">
    
   
</head>
<body>
    <?php include "../Mise_en_page/sidebar.php"; ?>
    
    <div class="main-content">
        <div class="content-header">
            <div>
                <div class="breadcrumb">PowerBill / Gestion des anomalies</div>
                <h2>Liste des Anomalies</h2>
            </div>
        </div>
        
        <div class="content-container">
            <div class="filters-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="globalSearch" placeholder="Rechercher par numéro client ou compteur..." class="form-control">
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Compteur</th>
                            <th>Période actuelle</th>
                            <th>Consommation précédente</th>
                            <th>Photo précédente</th>
                            <th>Consommation actuelle</th>
                            <th>Photo actuelle</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="anomalie-table-body">
                        <!-- Les données seront ajoutées ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour la correction -->
<div class="modal fade" id="correctionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Corriger l'anomalie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="correctionForm">
                    <input type="hidden" id="anomalieId">
                    <input type="hidden" id="mois">
                    <input type="hidden" id="annee">
                    <input type="hidden" id="idCompteur">
                    <div class="mb-3">
                        <label for="nouvelleConsommation" class="form-label">Nouvelle consommation</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="nouvelleConsommation" required>
                            <span class="input-group-text">kWh</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmCorrection">Confirmer</button>
            </div>
        </div>
    </div>
</div>

    <!-- Modal pour afficher les images en grand -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Photo du compteur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" class="modal-img" id="modalImage">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/anomalie-controller.js"></script>
</body>
</html>




   