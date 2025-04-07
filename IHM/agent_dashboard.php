<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
    <style>
        :root {
            
            --primary-color: #3498db;
            --secondary-color: #1e40af;
            --background: #f8fafc;
            --success: #22c55e;
            --error: #ef4444;
            --text-dark: #1e293b;
            --text-light: #64748b;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            padding: 2rem;
            background: linear-gradient(135deg, var(--background) 0%, #e2e8f0 100%);
            color: var(--text-dark);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            padding-bottom: 0.5rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .upload-section {
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            margin: 2rem 0;
            transition: all 0.3s ease;
        }

        .upload-section:hover {
            border-color: var(--primary-color);
            background: #f1f5f9;
        }

        .file-input {
            position: relative;
            margin: 1.5rem 0;
        }

        .file-input input[type="file"] {
            opacity: 0;
            position: absolute;
            width: 1px;
            height: 1px;
        }

        .file-input label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-input label:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .file-input label:active {
            transform: translateY(0);
        }

        button[type="submit"] {
            background: var(--success);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        button[type="submit"]:hover {
            background: #16a34a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .logout-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .logout-link:hover {
            color: var(--primary-color);
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fee2e2;
            color:rgb(37, 153, 27);
            border: 1px solid #fecaca;
        }

        .message::before {
            content: '⚠️';
            font-size: 1.2rem;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
                margin: 1rem;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
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

    <script>
        // Afficher le nom du fichier sélectionné
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Aucun fichier sélectionné';
            document.getElementById('file-name').textContent = fileName;
        });

        // Gestion des messages
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const messageDiv = document.getElementById('message');

        if (message) {
            messageDiv.style.display = 'flex';
            messageDiv.textContent = message;
        }
    </script>
</body>
</html>