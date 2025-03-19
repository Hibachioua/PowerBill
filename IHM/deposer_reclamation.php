<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamation - PowerBill</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <header>
        <h1>PowerBill</h1>
        <nav>
            <a href="#">Enter bill</a>
            <a href="#">Home</a>
            <a href="#">Profile</a>
            <a href="#">View Bill</a>
            <a href="#" class="active">Complaint</a>
            <a href="#">Logout</a>
        </nav>
    </header>
    
    <main>
        <div class="complaint-container">
            <h2>Votre réclamation</h2>
            <form id="complaintForm" action="../Traitement/traitement_reclamation.php" method="POST">
                <label for="clientID">ID CLIENT</label>
                <input type="text" id="clientID" name="clientID" required> <!-- Ajout du name -->

                <label>Type de réclamation :</label>
                <div class="radio-group">
                    <input type="radio" name="complaintType" value="fuite interne" checked> Fuite interne
                    <input type="radio" name="complaintType" value="fuite externe"> Fuite externe
                    <input type="radio" name="complaintType" value="facture"> Facture
                    <input type="radio" name="complaintType" value="autre"> Autre
                </div>
                
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="4" required></textarea> <!-- Ajout du name -->
                
                <button type="submit">Envoyer</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 PowerBill. Tous droits réservés.</p>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>
