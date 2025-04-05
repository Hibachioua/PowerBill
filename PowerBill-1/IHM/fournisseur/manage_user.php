<?php
// IHM/manage_user.php - Vue pour la gestion des utilisateurs
require_once "../Traitement/auth_check.php";
require_once "../Traitement/user_traitement.php";


$current_page = basename($_SERVER['PHP_SELF']); // ex : "client_dashboard.php"
$user_role = $_SESSION['user_role'];            // récupéré après login

$sidebar_data = getSidebarData($current_page, $user_role); // 💡 essentiel ici





// Vérifier que l'utilisateur a le rôle fournisseur
checkUserAccess(3);

// Traiter les actions (ajout, modification, suppression)
$actionResult = processUserAction();
$message = $actionResult['message'];
$messageType = $actionResult['messageType'];

// Préparer les données pour la vue
$viewData = prepareUserData();
$users = $viewData['users'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerBill - Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/manage_user.css">

    
</head>
<body>
    <?php include "sidebar.php"; ?>
    
    <div class="main-content">
        <div class="content-header">
            <div>
                <div class="breadcrumb">PowerBill / Gestion des utilisateurs</div>
                <h2>Gestion des Utilisateurs</h2>
            </div>
            <button type="button" class="btn btn-primary" id="addUserBtn">
                <i class="fas fa-plus"></i> Ajouter un client
            </button>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="content-container">
            <div class="table-container">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Adresse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['ID_Utilisateur']; ?></td>
                                <td><?php echo htmlspecialchars($user['Nom']); ?></td>
                                <td><?php echo htmlspecialchars($user['Prenom']); ?></td>
                                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                <td><?php echo htmlspecialchars($user['Adresse']); ?></td>
                                <td class="actions">
                                    <button type="button" class="btn-action btn-edit edit-user-btn" 
                                            data-id="<?php echo $user['ID_Utilisateur']; ?>"
                                            data-nom="<?php echo htmlspecialchars($user['Nom']); ?>"
                                            data-prenom="<?php echo htmlspecialchars($user['Prenom']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['Email']); ?>"
                                            data-adresse="<?php echo htmlspecialchars($user['Adresse']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button type="button" class="btn-action btn-delete delete-user-btn"
                                            data-id="<?php echo $user['ID_Utilisateur']; ?>"
                                            data-nom="<?php echo htmlspecialchars($user['Nom']); ?>"
                                            data-prenom="<?php echo htmlspecialchars($user['Prenom']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Aucun client trouvé</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Ajout Utilisateur -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Ajouter un client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="add">
                        <!-- Rôle fixé à Client (1) -->
                        <input type="hidden" name="role" value="1">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" name="adresse" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Modification Utilisateur -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Modifier un client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <!-- Maintenir le rôle client -->
                        <input type="hidden" name="role" value="1">
                        
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="edit_prenom" name="prenom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="edit_adresse" name="adresse" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Mot de passe (laisser vide pour ne pas modifier)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Suppression Utilisateur -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Supprimer un client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le client <strong><span id="delete_user_name"></span></strong> ?</p>
                    <p class="text-danger">Cette action est irréversible.</p>
                    
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="user_id" id="delete_user_id">
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chargement des scripts dans le bon ordre -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="assets/js/manage_user.js"></script>

</body>
</html>