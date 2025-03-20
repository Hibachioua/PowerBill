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
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/styleListeFacture.css">
</head>
<body>
    <div class="containerListe">
        <div class="title">
            <h2>Vos Factures</h2>
        </div>
        <div class="btn-container">
            <button class="btn btn-primary"><i class="fas fa-folder-open"></i> Consulter les anciennes factures</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Période de Consommation</th>
                        <th>Numéro du Compteur</th>
                        <th>Nom Complet</th>
                        <th>Date de Facturation</th>
                        <th>Consommation</th>
                        <th>Montant</th>
                        <th>État Facture</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="facture-table-body">
                    <!-- Les données seront ajoutées ici dynamiquement avec JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    <?php include('footer.php'); ?>
    <script> 
    // Fonction pour récupérer les factures depuis l'URL
    function getFacturesFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const facturesJson = urlParams.get('factures');
        return facturesJson ? JSON.parse(decodeURIComponent(facturesJson)) : [];
    }

    // Fonction pour afficher les factures dans le tableau
// Fonction pour afficher les factures dans le tableau
function afficherFactures() {
    const factures = getFacturesFromURL();
    const tbody = document.getElementById('facture-table-body');
    tbody.innerHTML = '';  // Vider le tableau avant de le remplir à nouveau

    if (factures.length > 0) {
        factures.forEach(facture => {
            const row = document.createElement('tr');
            row.id = `facture-${facture.ID_Facture}`;
            
            // Vérification du statut de paiement
            let etatFacture = facture.Statut_paiement === "non paye"
                ? `<button onclick="payerFacture(${facture.ID_Facture})" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Payé
                   </button>`
                : `<span class="badge bg-success">Payé</span>`;

            row.innerHTML = `
                <td>${facture.Mois}/${facture.Annee}</td>
                <td>${facture.ID_Compteur}</td>
                <td>${facture.Nom} ${facture.Prenom}</td>
                <td>${facture.Date_émission}</td>
                <td>${facture.Consommation}</td>
                <td>${facture.Prix_TTC} €</td>
                <td>${etatFacture}</td>
                <td>
                    <button class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Télécharger
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="8">Aucune facture non payée trouvée.</td></tr>';
    }
}


    // Fonction pour payer une facture
    function payerFacture(factureID) {
        // Assurez-vous que le chemin est correct
        window.location.href = `http://localhost/Powerbill/Traitement/traitement_listefacture.php?facture_id=${factureID}`;
    }
    function appelerPageTraitement() {
    fetch('http://localhost/Powerbill/Traitement/traitement_listefacture.php', {
        method: 'POST',  // Utiliser POST pour envoyer des données
        body: JSON.stringify({ action: 'traiter' }),  // Optionnel, envoyer des données si nécessaire
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.text())
    .then(data => {
        console.log('Traitement terminé:', data);
    })
    .catch(error => {
        console.error('Erreur lors du traitement:', error);
    });
}

// Appel au traitement sans redirection
window.onload = function() {
    afficherFactures();
    appelerPageTraitement();  // Appel du traitement sans rediriger
};



</script>
</body>
</html>
