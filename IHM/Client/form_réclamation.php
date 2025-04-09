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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form_réclamation.css">
    </head>
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form_réclamation.css">
    </head>
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

    
