<?php
session_start();
require_once "../BD/connexion.php"; 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) { 
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 
$user_email = htmlspecialchars($_SESSION['user_email']); 

try {
    $pdo = connectDB(); 
    $stmt = $pdo->prepare("SELECT ID_Client FROM client WHERE ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur est bien un client
    if (!$client) {
        die("Aucun client trouvé pour cet utilisateur.");
    }

    $ID_Client = $client['ID_Client'];
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

try {
    $stmt = $pdo->prepare("SELECT ID_Compteur FROM compteur WHERE ID_Client = ?");
    $stmt->execute([$ID_Client]);
    $compteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consommation - PowerBill</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

<div class="consumption-container">
    <h2>Enregistrez votre consommation du mois</h2>

    <!-- Affichage des messages d'erreur/succès -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert <?= isset($_GET['success']) ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars(urldecode($_GET['message'])) ?>
        </div>
    <?php endif; ?>

    <form id="consumptionForm" action="../Traitement/consommation_traitement.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    
    <!-- Sélection de l'ID Compteur -->
    <div class="form-group">
        <label for="ID_Compteur">ID Compteur :</label>
        <select id="ID_Compteur" name="ID_Compteur" required onchange="fetchLastCounterImage(this.value)">
            <option value="" disabled selected>Choisissez votre compteur</option>
            <?php foreach ($compteurs as $compteur): ?>
                <option value="<?= htmlspecialchars($compteur['ID_Compteur']); ?>">Compteur <?= htmlspecialchars($compteur['ID_Compteur']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <input type="hidden" id="Mois" name="Mois" min="1" max="12" required>
    <input type="hidden" id="Annee" name="Annee" min="2020" max="2030" required>

    <div class="form-group">
        <label for="Qté_consommé">Quantité Consommée (kWh):</label>
        <input type="number" id="Qté_consommé" name="Qté_consommé" step="0.01" min="0" required>
    </div>
    
    <div class="form-group">
        <label for="counterPicture">Photo du compteur:</label>
        <input type="file" id="counterPicture" name="counterPicture" accept="image/*" required>
    </div>
    
    <button type="submit" id="submitBtn" class="btn-dark">Enregistrer</button>
</form>

</div>

<!-- Pop-up pour afficher l'image du compteur -->
<div id="imagePopup" class="popup-container" style="display: none;">
    <div id="lastCounterImage" class="popup-content"></div>
</div>

<script>
// Validation du formulaire pour éviter la double soumission
function validateForm() {
    document.getElementById('submitBtn').disabled = true;
    return true;
}

async function fetchLastCounterImage(compteurId) {
    const popup = document.getElementById('imagePopup');
    const imageContainer = document.getElementById('lastCounterImage');
    
    popup.style.display = 'flex';
    imageContainer.innerHTML = '<div class="loader">Chargement...</div>';

    try {
        const response = await fetch(`../Traitement/consommation_traitement.php?action=get_last_image&compteur_id=${compteurId}`);
        
        if (!response.ok) {
            throw new Error(`Échec de la requête : ${response.status}`);
        }
        
        const data = await response.json();
        
        // Log the data to debug
        console.log(data);

        if (!data.success) {
            throw new Error(data.error || 'Erreur de récupération');
        }

        if (data.image_data) {
            const imageSrc = 'data:' + data.content_type + ';base64,' + data.image_data;
            imageContainer.innerHTML = `
                <img src="${imageSrc}" alt="Photo du compteur" style="max-width:100%; max-height:70vh;">
                <button onclick="closePopup()" class="close-btn">Fermer</button>
            `;
        } else {
            throw new Error('Aucune donnée image disponible');
        }

    } catch (error) {
        imageContainer.innerHTML = `
            <div class="error-container">
                <p class="error-title">Erreur de chargement</p>
                <p class="error-message">${error.message}</p>
                <button onclick="closePopup()" class="close-btn">Fermer</button>
            </div>
        `;
    }
}

function closePopup() {
    document.getElementById('imagePopup').style.display = 'none';
}

document.getElementById('imagePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup();
    }
});
</script>

</body>
</html>

<?php include "footer.php"; ?>


