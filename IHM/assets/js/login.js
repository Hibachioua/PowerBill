
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour récupérer les paramètres URL
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
        
        // Vérifier si un message d'erreur est présent dans l'URL
        var errorMessage = getUrlParameter('error');
        if (errorMessage) {
            // Afficher le message d'erreur
            var errorContainer = document.getElementById('error-container');
            errorContainer.textContent = errorMessage;
            errorContainer.style.display = 'block';
        }
    });
    
    // Fonction pour basculer la visibilité du mot de passe
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.password-toggle i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

