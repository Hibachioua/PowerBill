<?php 
// D'abord, on inclut auth_check.php qui va gérer la session et vérifier l'authentification
require_once "../../Traitement/auth_check.php";

// ID client déjà disponible dans la session
$id_client = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Réclamation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
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
        .message {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style></head>
<body>
    <?php include('../Mise_en_page/header_client.php'); ?>

    <div class="containerform">
        <h2>Votre Réclamation</h2>

        <!-- Affichage du message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo $_SESSION['type']; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']); // Supprimer le message après affichage
                    unset($_SESSION['type']);
                ?>
            </div>
        <?php endif; ?>

        <form action="../../Traitement/traitement_reclamation.php" method="POST">
            <input type="hidden" name="action" value="envoyer_reclamation">
            <!-- Plus besoin de passer l'ID client, il est déjà dans la session -->

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

    <?php include('../Mise_en_page/footer.php'); ?>
</body>
</html>

    
