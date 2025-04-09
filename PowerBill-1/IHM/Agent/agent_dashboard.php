<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
    <link rel="stylesheet" href="../assets/css/agent_cons_annuelle.css?v=1.0">
</head>
<body>
    <div class="container">
        <h1>Tableau de bord Agent</h1>
        <p>Veuillez télécharger le fichier de consommation annuelle des clients :</p>
        <p><small>Format attendu : consommation_annuelle_(annee).txt</small></p>

        <div id="message" class="message" style="display: none;"></div>

        <form action="../Traitement/traitement_consommation_annuelle.php" method="POST" enctype="multipart/form-data">
            <div class="upload-section">
                <div class="file-input">
                    <input type="file" name="file" id="file" accept=".txt" required>
                    <label for="file">📁 Choisir un fichier</label>
                </div>
                <p id="file-name" class="text-muted">Aucun fichier sélectionné</p>
                <button type="submit" name="submit">📤 Envoyer le fichier</button>
            </div>
        </form>

        <a href="logout.php" class="logout-link">→ Déconnexion</a>

        
    </div>

    <script src="../assets/js/agent_cons_annuelle.js"></script>
</body>
</html>