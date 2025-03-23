<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factures Payées</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/styleListeFacture.css?v=1.0">
</head>
<body>
    <div class="containerListe">
        <div class="title">
            <h2>Factures Payées</h2>
        </div>
        <div class="btn-container">
            <button id="btnRetour" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </button>
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
            // Fonction pour charger les factures payées depuis le serveur
            function chargerFacturesPayees() {
                $.ajax({
                    url: '../Traitement/traitement_listefacture.php?action=getFacturesPayees',
                    type: 'GET',
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
                                        <button class="download-btn" data-id="${facture.ID_Facture}">Télécharger</button>
                                    </td>
                                </tr>`;
                            });
                            $('#facture-table-body').html(html);
                        } else {
                            $('#facture-table-body').html('<tr><td colspan="7">Aucune facture payée trouvée.</td></tr>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erreur AJAX:", error);
                        $('#facture-table-body').html('<tr><td colspan="7">Erreur lors du chargement des factures.</td></tr>');
                    }
                });
            }

            // Charger les factures payées au chargement de la page
            chargerFacturesPayees();

            // Gérer le téléchargement de la facture
            $(document).on('click', '.download-btn', function () {
                let factureID = $(this).data('id');
                window.location.href = `../Traitement/telecharger_facture.php?factureID=${factureID}`;  // Redirige pour télécharger la facture
            });

            // Gérer le bouton Retour
            $(document).on('click', '#btnRetour', function () {
                window.location.href = '../Traitement/traitement_listefacture.php?action=retour';
            });
        });
    </script>
</body>
</html>