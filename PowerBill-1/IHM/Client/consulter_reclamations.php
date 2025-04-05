<?php 
session_start();
include('../Mise_en_page/header_client.php'); 

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Client/client_dashboard.php");
    exit();
}

// Récupérer l'ID du client depuis la session
$id_client = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter mes réclamations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

<style>
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

    .containerListe .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease-in-out;
    }

    .containerListe .btn-primary {
        background-color: rgb(96, 76, 251);
        color: white;
    }
    .containerListe .btn-primary:hover {
        background-color: rgb(71, 6, 184);
    }

    .containerListe .btn-success {
        background-color: #f5a729;
        color: white;
    }
    .containerListe .btn-success:hover {
        background-color: rgb(223, 137, 0);
    }

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
    <div class="btn-container">
        <form action="../../Traitement/traitement_reclamation.php" method="GET">
            <input type="hidden" name="action" value="creer_reclamation">
            <input type="hidden" name="id_client" value="<?php echo $id_client; ?>">
            <button type="submit" class="btn btn-primary">Nouvelle réclamation</button>
        </form>
    </div>


    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Réponse</th>
                </tr>
            </thead>
            <tbody id="reclamationsTable">
                <!-- Les réclamations seront insérées ici -->
            </tbody>
        </table>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const id_client = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

    if (!id_client) {
        alert("Utilisateur non connecté !");
        window.location.href = "../Client/client_dashboard.php";
        return;
    }

    fetch(`../../Traitement/traitement_reclamation.php?action=consulter_reclamations&id_client=${id_client}`)
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById("reclamationsTable");
        tableBody.innerHTML = ""; 

        if (data.error) {
            tableBody.innerHTML = `<tr><td colspan="5">${data.error}</td></tr>`;
            return;
        }

        if (data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5">Aucune réclamation trouvée.</td></tr>`;
            return;
        }

        data.forEach(reclamation => {
            const row = `<tr>
                <td>${reclamation.Type_Réclamation}</td>
                <td>${reclamation.Description}</td>
                <td>${reclamation.Date_Réclamation}</td>
                <td>${reclamation.Statut}</td>
                <td>${reclamation.Réponse_Fournisseur ? reclamation.Réponse_Fournisseur : "En attente"}</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    })
    .catch(error => {
        console.error("Erreur:", error);
    });
});
</script>



    <?php include('../Mise_en_page/footer.php'); ?>
</body>
</html>


