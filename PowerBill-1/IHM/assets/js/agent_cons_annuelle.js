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