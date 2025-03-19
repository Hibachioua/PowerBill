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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleListeFacture.css">
   
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
                        <th> Période de Consommation</th>
                        <th>Numéro du Compteur</th>
                        <th>Nom Complet</th>
                        <th>Date de Facturation</th>
                        <th>Consommation</th>
                        <th>Montant</th>
                        <th>État Facture</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>03/2025</td>
                        <td>123456</td>
                        <td>Jean Dupont</td>
                        <td>10/03/2025</td>
                        <td>250 kWh</td>
                        <td>75 €</td>
                        <td><button class="btn btn-success"><i class="fas fa-check-circle"></i> Payé</button></td>
                        <td class="actions">
                            <button class="btn btn-danger"><i class="fas fa-file-pdf"></i> Télécharger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>02/2025</td>
                        <td>789012</td>
                        <td>Marie Curie</td>
                        <td>12/02/2025</td>
                        <td>300 kWh</td>
                        <td>90 €</td>
                        <td><button class="btn btn-success"><i class="fas fa-check-circle"></i> Payé</button></td>
                        <td class="actions">
                            <button class="btn btn-danger"><i class="fas fa-file-pdf"></i> Télécharger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>03/2025</td>
                        <td>123456</td>
                        <td>Jean Dupont</td>
                        <td>10/03/2025</td>
                        <td>250 kWh</td>
                        <td>75 €</td>
                        <td><button class="btn btn-success"><i class="fas fa-check-circle"></i> Payé</button></td>
                        <td class="actions">
                            <button class="btn btn-danger"><i class="fas fa-file-pdf"></i> Télécharger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>03/2025</td>
                        <td>123456</td>
                        <td>Jean Dupont</td>
                        <td>10/03/2025</td>
                        <td>250 kWh</td>
                        <td>75 €</td>
                        <td><button class="btn btn-success"><i class="fas fa-check-circle"></i> Payé</button></td>
                        <td class="actions">
                            <button class="btn btn-danger"><i class="fas fa-file-pdf"></i> Télécharger</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>
