<?php
// BD/user_model.php - Fonctions d'accès aux données pour les utilisateurs
require_once "connexion.php";

/**
 * Récupère tous les utilisateurs (clients)
 */
function getAllUsers() {
    $connexion = connectDB();
    $users = [];
    
    if ($connexion) {
        try {
            // Ajout du champ CIN à la requête
            $stmt = $connexion->query("
                SELECT u.ID_Utilisateur, c.Nom, c.Prenom, c.Adresse, c.CIN, u.Email
                FROM utilisateur u
                INNER JOIN client c ON u.ID_Utilisateur = c.ID_Utilisateur
                WHERE u.ID_Role = 1
                ORDER BY u.ID_Utilisateur DESC
            ");
            
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
        }
    }
    
    return $users;
}
 
function getAllRoles() {
    $connexion = connectDB();
    $roles = [];
    
    if ($connexion) {
        try {
            $stmt = $connexion->query("SELECT ID_Role, Libelle FROM role ORDER BY ID_Role");
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des rôles: " . $e->getMessage());
        }
    }
    
    return $roles;
}

/**
 * Vérifie si un email existe déjà
 */
function emailExists($email, $excludeUserId = null) {
    $connexion = connectDB();
    
    if ($connexion) {
        try {
            if ($excludeUserId) {
                $stmt = $connexion->prepare("SELECT COUNT(*) FROM utilisateur WHERE Email = :email AND ID_Utilisateur != :id");
                $stmt->bindParam(':id', $excludeUserId);
            } else {
                $stmt = $connexion->prepare("SELECT COUNT(*) FROM utilisateur WHERE Email = :email");
            }
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'email: " . $e->getMessage());
        }
    }
    
    return false;
}
/**
 * Vérifie si un CIN existe déjà
 */
function cinExists($cin, $excludeUserId = null) {
    $connexion = connectDB();
    
    if ($connexion) {
        try {
            if ($excludeUserId) {
                $stmt = $connexion->prepare("
                    SELECT COUNT(*) FROM client c
                    WHERE c.CIN = :cin AND c.ID_Utilisateur != :id
                ");
                $stmt->bindParam(':id', $excludeUserId);
            } else {
                $stmt = $connexion->prepare("
                    SELECT COUNT(*) FROM client c
                    WHERE c.CIN = :cin
                ");
            }
            
            $stmt->bindParam(':cin', $cin);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du CIN: " . $e->getMessage());
        }
    }
    
    return false;
}

/**
 * Ajoute un nouvel utilisateur (client)
 */
function addUser($nom, $prenom, $email, $password, $adresse, $cin) {
    $connexion = connectDB();
    
    if (!$connexion) {
        return [
            'success' => false,
            'message' => 'Erreur de connexion à la base de données'
        ];
    }
    
    try {
        if (emailExists($email)) {
            return [
                'success' => false,
                'message' => 'Cet email est déjà utilisé'
            ];
        }
        
        if (cinExists($cin)) {
            return [
                'success' => false,
                'message' => 'Ce CIN est déjà utilisé'
            ];
        }
        
        $connexion->beginTransaction();
        
        // Table utilisateur inchangée
        $stmtUser = $connexion->prepare("
            INSERT INTO utilisateur (Email, Mot_de_passe, ID_Role) 
            VALUES (:email, :password, 1)
        ");
        $stmtUser->bindParam(':email', $email);
        $stmtUser->bindParam(':password', $password);
        $stmtUser->execute();
        
        $userId = $connexion->lastInsertId();
        
        // Ajout du CIN dans la table client
        $stmtClient = $connexion->prepare("
            INSERT INTO client (Nom, Prenom, Adresse, CIN, ID_Utilisateur) 
            VALUES (:nom, :prenom, :adresse, :cin, :userId)
        ");
        $stmtClient->bindParam(':nom', $nom);
        $stmtClient->bindParam(':prenom', $prenom);
        $stmtClient->bindParam(':adresse', $adresse);
        $stmtClient->bindParam(':cin', $cin);
        $stmtClient->bindParam(':userId', $userId);
        $stmtClient->execute();
        
        $connexion->commit();
        
        return [
            'success' => true,
            'message' => 'Client ajouté avec succès'
        ];
    } catch (PDOException $e) {
        $connexion->rollBack();
        error_log("Erreur lors de l'ajout d'un client: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
}

function updateUser($userId, $nom, $prenom, $email, $password, $adresse, $cin) {
    $connexion = connectDB();
    
    if (!$connexion) {
        return [
            'success' => false,
            'message' => 'Erreur de connexion à la base de données'
        ];
    }
    
    try {
        if (emailExists($email, $userId)) {
            return [
                'success' => false,
                'message' => 'Cet email est déjà utilisé par un autre utilisateur'
            ];
        }
        
        // Vérifier si le CIN existe déjà (pour un autre utilisateur)
        if (cinExists($cin, $userId)) {
            return [
                'success' => false,
                'message' => 'Utilisez votre CIN !'
            ];
        }
        
        $connexion->beginTransaction();
        
        // Table utilisateur inchangée (avec ou sans mot de passe)
        
        // Ajout du CIN dans la mise à jour de la table client
        $stmtClient = $connexion->prepare("
            UPDATE client 
            SET Nom = :nom, Prenom = :prenom, Adresse = :adresse, CIN = :cin
            WHERE ID_Utilisateur = :id
        ");
        $stmtClient->bindParam(':nom', $nom);
        $stmtClient->bindParam(':prenom', $prenom);
        $stmtClient->bindParam(':adresse', $adresse);
        $stmtClient->bindParam(':cin', $cin);
        $stmtClient->bindParam(':id', $userId);
        $stmtClient->execute();
        
        $connexion->commit();
        
        return [
            'success' => true,
            'message' => 'Client modifié avec succès'
        ];
    } catch (PDOException $e) {
        $connexion->rollBack();
        error_log("Erreur lors de la modification d'un client: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
}



function deleteUser($userId, $currentUserId) {
    $connexion = connectDB();
    
    if (!$connexion) {
        return [
            'success' => false,
            'message' => 'Erreur de connexion à la base de données'
        ];
    }
    
    try {
        // Vérifier que l'utilisateur ne se supprime pas lui-même
        if ($userId == $currentUserId) {
            return [
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte'
            ];
        }
        
        // Vérifier que l'utilisateur est bien un client
        $stmt = $connexion->prepare("SELECT ID_Role FROM utilisateur WHERE ID_Utilisateur = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || $user['ID_Role'] != 1) {
            return [
                'success' => false,
                'message' => 'Vous ne pouvez supprimer que des comptes clients'
            ];
        }
        
        // Démarrer une transaction
        $connexion->beginTransaction();
        
        // 1. Supprimer les enregistrements dans la table client
        $stmtClient = $connexion->prepare("DELETE FROM client WHERE ID_Utilisateur = :id");
        $stmtClient->bindParam(':id', $userId);
        $stmtClient->execute();
        
        // 2. Supprimer l'utilisateur
        $stmtUser = $connexion->prepare("DELETE FROM utilisateur WHERE ID_Utilisateur = :id");
        $stmtUser->bindParam(':id', $userId);
        $stmtUser->execute();
        
        // Valider la transaction
        $connexion->commit();
        
        return [
            'success' => true,
            'message' => 'Client supprimé avec succès'
        ];
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $connexion->rollBack();
        error_log("Erreur lors de la suppression d'un client: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
}

/**
 * Récupère les détails d'un utilisateur (client) par son ID
 */
function getUserById($userId) {
    $connexion = connectDB();
    
    if (!$connexion) {
        return null;
    }
    
    try {
        $stmt = $connexion->prepare("
            SELECT u.ID_Utilisateur, c.Nom, c.Prenom, u.Email, u.ID_Role, r.Libelle as Role 
            FROM utilisateur u
            LEFT JOIN client c ON u.ID_Utilisateur = c.ID_Utilisateur
            LEFT JOIN role r ON u.ID_Role = r.ID_Role
            WHERE u.ID_Utilisateur = :id
        ");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération d'un utilisateur: " . $e->getMessage());
        return null;
    }
}
?>