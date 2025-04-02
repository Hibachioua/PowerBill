
    
   
        // Initialisation manuelle des modals au chargement du document
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser les objets Modal
            var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            var deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            
            // Gestionnaire pour le bouton d'ajout
            document.getElementById('addUserBtn').addEventListener('click', function() {
                addUserModal.show();
            });
            
            // Gestionnaires pour les boutons d'édition
            document.querySelectorAll('.edit-user-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    // Récupérer les données utilisateur
                    var userId = this.getAttribute('data-id');
                    var nom = this.getAttribute('data-nom');
                    var prenom = this.getAttribute('data-prenom');
                    var cin = this.getAttribute('data-cin');
                    var email = this.getAttribute('data-email');
                    var adresse = this.getAttribute('data-adresse');
                    
                    // Mettre à jour les champs du formulaire
                    document.getElementById('edit_user_id').value = userId;
                    document.getElementById('edit_nom').value = nom;
                    document.getElementById('edit_prenom').value = prenom;
                    document.getElementById('edit_cin').value = cin || '';
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_adresse').value = adresse || '';
                    
                    // Afficher le modal
                    editUserModal.show();
                });
            });
            
            // Gestionnaires pour les boutons de suppression
            document.querySelectorAll('.delete-user-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    // Récupérer les données utilisateur
                    var userId = this.getAttribute('data-id');
                    var nom = this.getAttribute('data-nom');
                    var prenom = this.getAttribute('data-prenom');
                    
                    // Mettre à jour les champs du formulaire
                    document.getElementById('delete_user_id').value = userId;
                    document.getElementById('delete_user_name').textContent = prenom + ' ' + nom;
                    
                    // Afficher le modal
                    deleteUserModal.show();
                });
            });
        });
  


    // Faire disparaître les alertes automatiquement après 5 secondes
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                // Utiliser Bootstrap pour fermer l'alerte avec animation
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000); // 5000 ms = 5 secondes
        });
    });
