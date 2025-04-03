<?php
require_once "../Traitement/auth_check.php";
checkUserAccess(3); 
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
            background: linear-gradient(135deg, rgb(205, 226, 246) 0%, #e9ecef 100%);
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
        
        .btn-corriger {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: background-color 0.3s;
        }
        
        .btn-corriger:hover {
            background-color: #c0392b;
        }
        
        .compteur-img {
            max-width: 100px;
            max-height: 100px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .compteur-img:hover {
            transform: scale(1.5);
        }
        
        .modal-img {
            max-width: 100%;
            max-height: 80vh;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            color: #495057;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #3498db;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        #confirmCorrection {
            background-color: #2ecc71;
            border-color: #2ecc71;
        }

        #confirmCorrection:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    
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
    <script>
    class AnomalieController {
        constructor() {
            this.allAnomalies = [];
            this.initEventListeners();
            this.loadAnomalies();
            
            // Rafraîchissement automatique toutes les 5 secondes
            setInterval(() => this.loadAnomalies(), 1000);
        }

        initEventListeners() {
            $('#globalSearch').on('keyup', () => this.applyFilters());
            $(document).on('click', '.btn-corriger', (e) => this.openCorrectionModal(e));
            $('#confirmCorrection').on('click', () => this.corrigerAnomalie());
            $(document).on('click', '.compteur-img', (e) => this.showImageModal(e));
        }

        async loadAnomalies() {
    console.log("Chargement des anomalies..."); // Log au début de la fonction
    try {
        const response = await fetch('../Traitement/AnomalieController.php?action=getAnomalies');
        
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        
        const data = await response.json();
        console.log("Réponse reçue:", data); // Log de la réponse de l'API
        
        if (data && data.length > 0) {
            this.allAnomalies = data;
            this.displayAnomalies(this.allAnomalies);
        } else {
            console.log("Aucune anomalie trouvée."); // Log si aucune anomalie n'est trouvée
            $('#anomalie-table-body').html('<tr><td colspan="8" class="text-center">Aucune anomalie trouvée.</td></tr>');
        }
    } catch (error) {
        console.error("Erreur:", error); // Log des erreurs
    }
}

        displayAnomalies(anomalies) {
            const html = anomalies.length > 0 
                ? anomalies.map(anomalie => this.createAnomalieRow(anomalie)).join('')
                : '<tr><td colspan="8" class="text-center">Aucune anomalie trouvée avec ces critères.</td></tr>';
            
            $('#anomalie-table-body').html(html);
        }

        createAnomalieRow(anomalie) {
            return `
                <tr>
                    <td>${anomalie.ID_Client}</td>
                    <td>${anomalie.ID_Compteur}</td>
                    <td>${anomalie.Mois_actuel}/${anomalie.Annee_actuel}</td>
                    <td>${anomalie.Consommation_precedent} kWh</td>
                    <td>
                        ${anomalie.Image_precedent 
                            ? `<img src="../uploads/${anomalie.Image_precedent}" class="compteur-img" alt="Compteur précédent">`
                            : 'N/A'}
                    </td>
                    <td>${anomalie.Consommation_actuelle} kWh</td>
                    <td>
                        ${anomalie.Image_actuelle 
                            ? `<img src="../uploads/${anomalie.Image_actuelle}" class="compteur-img" alt="Compteur actuel">`
                            : 'N/A'}
                    </td>
                    <td>
                        <button class="btn-corriger" data-id="${anomalie.ID_Consommation}">
                            Corriger
                        </button>
                    </td>
                </tr>
            `;
        }

        openCorrectionModal(event) {
    const button = $(event.currentTarget);
    const row = button.closest('tr');
    const anomalieId = button.data('id');
    const currentConsommation = row.find('td:eq(5)').text().replace(' kWh', '');
    const periode = row.find('td:eq(2)').text().split('/');
    const mois = periode[0];
    const annee = periode[1];
    const idCompteur = row.find('td:eq(1)').text();
    
    $('#anomalieId').val(anomalieId);
    $('#nouvelleConsommation').val(currentConsommation);
    $('#mois').val(mois);
    $('#annee').val(annee);
    $('#idCompteur').val(idCompteur);
    
    const modal = new bootstrap.Modal(document.getElementById('correctionModal'));
    modal.show();
}

async corrigerAnomalie() {
    const id = $('#anomalieId').val();
    const nouvelleConsommation = $('#nouvelleConsommation').val();
    const mois = $('#mois').val();
    const annee = $('#annee').val();
    const idCompteur = $('#idCompteur').val();
    
    if (!id || !nouvelleConsommation || !mois || !annee || !idCompteur) {
        alert('Veuillez vérifier tous les champs requis');
        return;
    }

    try {
        const response = await fetch('../Traitement/AnomalieController.php?action=corrigerAnomalie', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&nouvelleConsommation=${nouvelleConsommation}&mois=${mois}&annee=${annee}&idCompteur=${idCompteur}`
        });
        
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        
        const result = await response.json();
        
        if (result.status === "success") {
            alert('Anomalie corrigée avec succès');
            $('#correctionModal').modal('hide');
            this.loadAnomalies();
        } else {
            throw new Error(result.message || 'Échec de la correction');
        }
    } catch (error) {
        console.error("Erreur:", error);
        alert(error.message || "Une erreur s'est produite lors de la correction.");
    }
}
        showImageModal(event) {
            const imgSrc = $(event.currentTarget).attr('src');
            $('#modalImage').attr('src', imgSrc);
            
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }

        applyFilters() {
            const searchText = $('#globalSearch').val().toLowerCase();
            
            const filtered = this.allAnomalies.filter(anomalie => {
                if (searchText) {
                    const client = anomalie.ID_Client ? anomalie.ID_Client.toString() : '';
                    const compteur = anomalie.ID_Compteur ? anomalie.ID_Compteur.toString() : '';
                    
                    if (!client.includes(searchText) && !compteur.includes(searchText)) {
                        return false;
                    }
                }
                return true;
            });

            this.displayAnomalies(filtered);
        }
    }

    // Initialisation du contrôleur lorsque le DOM est prêt
    document.addEventListener('DOMContentLoaded', () => {
        new AnomalieController();
    });
    </script>
</body>
</html>