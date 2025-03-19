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
    <link rel="stylesheet" href="css/style.css">

    <style>
    /* 🌍 Global Styles */
    body {
        font-family: 'Poppins', 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f7f9;
        color: #333;
        line-height: 1.6;
    }

    .containerListe {
        max-width: 1100px;
        margin: 40px auto;
        padding: 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        text-align: center;
    }

    h2 {
        margin: 0;
        color: #2c3e50;
        font-size: 30px;
        font-weight: 600;
    }

    .title {
        margin-bottom: 60px;
    }

    .btn-container {
        text-align: right;
        margin-bottom: 30px;
    }

    /* Styles spécifiques aux boutons dans la section des factures */
    .containerListe .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease-in-out;
    }

    /* Bouton primaire spécifique dans la page */
    .containerListe .btn-primary {
        background-color: rgb(96, 76, 251);
        color: white;
    }
    .containerListe .btn-primary:hover {
        background-color: rgb(71, 6, 184);
    }

    /* Bouton succès spécifique dans la page */
    .containerListe .btn-success {
        background-color: #f5a729;
        color: white;
    }
    .containerListe .btn-success:hover {
        background-color: rgb(223, 137, 0);
    }

    /* Bouton danger spécifique dans la page */
    .containerListe .btn-danger {
        background-color: #3c83e7;
        color: white;
    }
    .containerListe .btn-danger:hover {
        background-color: #0c4da7;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        table-layout: fixed;
    }

    th, td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #e0e0e0;
        font-size: 14px;
    }

    th {
        background-color: #144f8a;
        color: white;
        font-weight: 600;
    }

    tr:hover {
        background-color: #f1f3f5;
        transition: background 0.2s ease-in-out;
    }

    .actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    /* Limite la largeur de la colonne "Période de Consommation" */
    th:nth-child(1), td:nth-child(1) {
        width: 120px;
    }

    th:nth-child(2), td:nth-child(2) {
        width: 120px;
    }

    th:nth-child(3), td:nth-child(3) {
        width: 120px;
    }

    th:nth-child(4), td:nth-child(4) {
        width: 120px;
    }

    th:nth-child(5), td:nth-child(5) {
        width: 120px;
    }

    th:nth-child(6), td:nth-child(6) {
        width: 120px;
    }

    th:nth-child(7), td:nth-child(7) {
        width: 120px;
    }

    th:nth-child(8), td:nth-child(8) {
        width: 180px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .containerListe {
            padding: 20px;
        }

        h2 {
            font-size: 24px;
        }

        .btn-container {
            text-align: center;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 8px;
        }

        .actions {
            flex-direction: column;
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .containerListe {
            margin: 20px;
            padding: 15px;
        }

        h2 {
            font-size: 20px;
        }

        table {
            font-size: 10px;
        }

        th, td {
            padding: 6px;
        }
    }
</style>

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
