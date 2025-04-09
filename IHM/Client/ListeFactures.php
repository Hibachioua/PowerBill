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
    <script>
    $(document).ready(function() {
        let allFactures = [];
        
        function chargerFactures() {
            $.ajax({
                url: '../../Traitement/traitement_listefacture.php',
                type: 'GET',
                data: { action: 'getFactures' },
                dataType: 'json',
                success: function(response) {
                    console.log(response);  // Affiche la réponse complète du serveur
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
        // Vérification du type de facture et ajustement de l'affichage de la période
        let periode = '';
        if (facture.type === 'complementaire') {
            // Afficher uniquement l'année pour les factures complémentaires
            periode = facture.Annee;
        } else {
            // Afficher le mois et l'année pour les autres types
            periode = `${facture.Mois}/${facture.Annee}`;
        }

        // Affichage des données dans le tableau
        const row = `
        <tr>
            <td>${periode}</td>
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

    // Trouver la facture correspondante
    const facture = allFactures.find(f => f.ID_Facture === factureID);
    
    // Vérifier le type de la facture et rediriger en conséquence
    if (facture) {
        if (facture.type === 'complementaire') {
            window.location.href = `../../Traitement/telecharger_facture_complementaire.php?factureID=${factureID}`;
        } else {
            window.location.href = `../../Traitement/telecharger_facture.php?factureID=${factureID}`;
        }
    } else {
        alert("Facture non trouvée !");
    }
});


        // Redirection pour consulter les anciennes factures
        $('#consulterAnciennesFactures').click(function() {
            window.location.href = 'ListeFacturesPayees.php';
        });

        // Charger les factures au démarrage et toutes les 30 secondes
        chargerFactures();
        setInterval(chargerFactures, 5000);
    });
    </script>
</body>
</html>