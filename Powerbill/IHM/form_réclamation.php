<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Factures</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .containerform {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: 500;
            text-align: left;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 10px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px; 
            white-space: nowrap; 
            margin-top: 5px;
        }


        .radio-group input {
            margin-right: 10px;
        }

        button {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

    </style>

</head>
<body>

    <div class="containerform">
        <h2>Votre Réclamation</h2>
        
        <form action="../Traitement/traitement_reclamation.php?action=envoyer_reclamation" method="POST">
            <label>ID CLIENT</label>
            <input type="text" name="id_client" required>

            <label>Type de réclamation</label>
            <div class="radio-group">
                <label><input type="radio" name="type_reclamation" value="Fuite interne" checked> Fuite interne</label>
                <label><input type="radio" name="type_reclamation" value="Fuite externe"> Fuite externe</label>
                <label><input type="radio" name="type_reclamation" value="Facture"> Facture</label>
                <label><input type="radio" name="type_reclamation" value="Autre"> Autre...</label>
            </div>
            
            <label>Description:</label>
            <textarea name="description" rows="3" required></textarea>
            
            <button type="submit">Envoyer</button>
        </form>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>
