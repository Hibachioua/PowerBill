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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/styleListeFacture.css?v=1.0">
    <link rel="stylesheet" href="assets/css/filter-styles.css">
    <style>
.notification {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    margin: 0;
    border-radius: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
}
    </style>
</head>
<body>
<div id="notification" class="notification" style="display:none;">
    <span id="notification-message"></span>
    <button id="notification-close" onclick="closeNotification()">×</button>
</div>
    <div class="containerListe">
        <div class="title">
            <h2>Vos Factures</h2>
        </div>
        <div class="btn-container">
            <button id="consulterAnciennesFactures" class="btn btn-primary">
                <i class="fas fa-folder-open"></i> Consulter les anciennes factures
            </button>
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

    <?php include('footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        let allFactures = [];
        
      function chargerFactures() {
    $.ajax({
        url: '../Traitement/traitement_listefacture.php',
        type: 'GET',
        data: { action: 'getFactures' }, // L'ID utilisateur est récupéré côté serveur
        dataType: 'json',
        success: function(response) {
            console.log(response);  // Vérifie la réponse du serveur
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
                // Affichage des données dans le tableau
                const row = `
                <tr>
                    <td>${facture.Mois}/${facture.Annee}</td>
                    <td>${facture.ID_Compteur}</td>
                    <td>${facture.Nom || 'Nom non disponible'} ${facture.Prenom || 'Prénom non disponible'}</td>
                    <td>${facture.Date_émission}</td>
                    <td>${facture.Qté_consommé || 'Consommation non disponible'} kWh</td>
                    <td>${facture.Prix_TTC} DH</td>
                    <td>
                        <span class="badge ${facture.Statut_paiement === 'paye' ? 'bg-success' : 'bg-warning'}">
                            ${facture.Statut_paiement}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary download-btn" data-id="${facture.ID_Facture}">
                            <i class="fas fa-download"></i> PDF
                        </button>
                    </td>
                </tr>`;
                tbody.append(row);
            });
        }

        // Fonction pour télécharger la facture au format PDF
        $(document).on('click', '.download-btn', function() {
            const factureID = $(this).data('id');
            if (!factureID || isNaN(factureID)) {
                alert("Facture invalide !");
                return;
            }
            window.location.href = `../Traitement/telecharger_facture.php?factureID=${factureID}`;
        });

        // Redirection pour consulter les anciennes factures
        $('#consulterAnciennesFactures').click(function() {
            window.location.href = 'ListeFacturesPayees.php';
        });

        // Charger les factures au démarrage et toutes les 30 secondes
        chargerFactures();
        setInterval(chargerFactures, 30000);
    });
    function getURLParameter(name) {
            return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
        }

        // Vérifier s'il y a un message dans l'URL
        const message = getURLParameter('message');
        if (message) {
            const notificationDiv = document.createElement('div');
            notificationDiv.className = 'notification';
            notificationDiv.textContent = message;
            document.body.insertBefore(notificationDiv, document.body.firstChild);
        }

        function showNotification(message) {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
    
    notificationMessage.textContent = message;
    notification.style.display = 'flex';

    // Cacher la notification après 10 secondes
    setTimeout(() => {
        notification.style.display = 'none';
    }, 10000);
}

function closeNotification() {
    const notification = document.getElementById('notification');
    notification.style.display = 'none';
}

window.onload = () => {
    const message = "Consommation ajoutée avec succès mais à vérifier par le fournisseur";
    showNotification(message);
};

    </script>
</body>
</html>
