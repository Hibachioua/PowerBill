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
    <link rel="stylesheet" href="assets/styleListeFacture.css?v=1.0">


</head>
<body>
    <div class="containerListe">
        <div class="title">
            <h2>Vos Factures</h2>
        </div>
        <div class="btn-container">
            <button id="consulterAnciennesFactures" class="btn btn-primary"><i class="fas fa-folder-open"></i> Consulter les anciennes factures</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function () {
    // Fonction pour charger les factures depuis le serveur
    function chargerFactures() {
        $.ajax({
            url: '../Traitement/traitement_listefacture.php',
            type: 'GET',
            data: { action: 'getFactures' },
            dataType: 'json',
            success: function (data) {
                console.log("Données reçues : ", data); 
                if (data && data.length > 0) {
                    let html = '';
                    data.forEach(facture => {
                        html += `<tr>
                            <td>${facture.Mois} / ${facture.Annee}</td>
                            <td>${facture.ID_Compteur}</td>
                            <td>${facture.Nom} ${facture.Prénom}</td>
                            <td>${facture.Date_émission}</td>
                            <td>${facture.Qté_consommé} kWh</td>
                            <td>${facture.Prix_TTC} DH</td>
                           
                         
                            <td>
                                <button class="payer-btn" data-id="${facture.ID_Facture}">Payer</button>
                            </td>
                            <td>
                                <button class="download-btn" data-id="${facture.ID_Facture}">Télécharger </button>
                            </td>


                        </tr>`;
                    });
                    console.log("HTML généré : ", html);
                    $('#facture-table-body').html(html);  // Ajoute les données dans le tableau
                } else {
                    $('#facture-table-body').html('<tr><td colspan="8">Aucune facture trouvée.</td></tr>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Erreur AJAX:", error);  // Affiche l'erreur si elle se produit
            }
        });
    }

    // Charger les factures au chargement de la page
    chargerFactures();

    // Rafraîchir toutes les 5 secondes pour récupérer les dernières données
    setInterval(chargerFactures, 5000);

    // Gérer le paiement d'une facture
    $(document).on('click', '.payer-btn', function () {
    console.log("Bouton Payer cliqué !");
    let factureID = $(this).data('id');
    console.log("ID de la facture : ", factureID);

    // Utiliser GET au lieu de POST
    $.ajax({
        url: '../Traitement/traitement_listefacture.php',
        type: 'GET',
        data: { action: 'payerFacture', factureID: factureID },
        dataType: 'json',
        success: function (response) {
            console.log("Réponse du serveur : ", response);
            if (response.status === "success") {
               
                chargerFactures(); // Rafraîchir la liste des factures
            } else {
                alert("Erreur lors du paiement de la facture.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Erreur AJAX : ", error);
            console.error("Statut de la requête : ", status);
            console.error("Réponse du serveur : ", xhr.responseText);
            alert("Une erreur s'est produite lors du paiement.");
        }
    });
});


    // Gérer le téléchargement de la facture
    $(document).on('click', '.download-btn', function () {
        let factureID = $(this).data('id');
        window.location.href = `../Traitement/telecharger_facture.php?factureID=${factureID}`;  // Redirige pour télécharger la facture
    });


    // Gérer le bouton "Consulter les anciennes factures"
    $('#consulterAnciennesFactures').on('click', function () {
        window.location.href = '../Traitement/traitement_listefacture.php?action=consulterAnciennesFactures';
    });
});

    </script>
</body>
</html>