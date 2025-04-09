<?php
require_once __DIR__ . "/connexion.php";

/**
 * Authentifie un utilisateur
 * 
 * @param PDO $connexion Connexion à la base de données
 * @param string $email Email de l'utilisateur
 * @param string $password Mot de passe en clair
 * @param bool $remember Option "Se souvenir de moi"
 * @return array Résultat de l'authentification avec informations
 */
function authenticateUser($connexion, $email, $password, $remember = false) {
    // Vérifiez que la connexion existe
    if (!$connexion) {
        return [
            'success' => false,
            'message' => 'Erreur de connexion à la base de données'
        ];
    }
    
    try {
        // Vérifier si l'email existe dans la base de données
        $stmt = $connexion->prepare("SELECT ID_Utilisateur, Email, Mot_de_passe, ID_Role FROM utilisateur WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Pour le développement, comparer directement les mots de passe
            // En production, utilisez password_verify($password, $user['Mot_de_passe'])
            if ($password == $user['Mot_de_passe']) {
                // Authentification réussie - démarrer la session
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Stocker les informations de base dans la session
                $_SESSION['user_id'] = $user['ID_Utilisateur'];
                $_SESSION['user_email'] = $user['Email'];
                $_SESSION['user_role'] = $user['ID_Role'];
                $_SESSION['loggedIn'] = true;
                
                // Gérer l'option "Se souvenir de moi"
                if ($remember) {
                    createRememberToken($connexion, $user['ID_Utilisateur']);
                }
                
                // Retourner le résultat avec les informations de l'utilisateur
                return [
                    'success' => true,
                    'message' => 'Authentification réussie',
                    'user' => [
                        'id' => $user['ID_Utilisateur'],
                        'email' => $user['Email'],
                        'role' => $user['ID_Role']
                    ]
                ];
            } else {
                // Mot de passe incorrect
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }
        } else {
            // Utilisateur non trouvé
            return [
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ];
        }
    } catch (PDOException $e) {
        // Log l'erreur pour le débogage
        error_log("Erreur d'authentification: " . $e->getMessage());
        
        // Retourner le résultat avec l'erreur
        return [
            'success' => false,
            'message' => 'Erreur de connexion à la base de données',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Créer un token pour la fonctionnalité "Se souvenir de moi"
 * 
 * @param PDO $connexion Connexion à la base de données
 * @param int $userId ID de l'utilisateur
 * @return bool Succès ou échec
 */
function createRememberToken($connexion, $userId) {
    try {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        
        // Stocker le token dans la base de données (si les colonnes existent)
        $stmt = $connexion->prepare("
            UPDATE utilisateur 
            SET remember_token = :token, 
                token_expiry = DATE_ADD(NOW(), INTERVAL 30 DAY) 
            WHERE ID_Utilisateur = :id
        ");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        // Créer un cookie qui expire dans 30 jours
        setcookie("remember_token", $token, time() + (86400 * 30), "/");
        
        return true;
    } catch (PDOException $e) {
        // Log l'erreur et retourner false
        error_log("Erreur création token: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifier si un token "Se souvenir de moi" est valide
 * 
 * @param PDO $connexion Connexion à la base de données
 * @return bool|array Succès avec informations utilisateur ou false
 */
function checkRememberToken($connexion) {
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    try {
        $token = $_COOKIE['remember_token'];
        
        // Vérifier si le token existe et est valide
        $stmt = $connexion->prepare("
            SELECT ID_Utilisateur, Email, ID_Role 
            FROM utilisateur 
            WHERE remember_token = :token 
            AND token_expiry > NOW()
        ");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Démarrer la session
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Stocker les informations dans la session
            $_SESSION['user_id'] = $user['ID_Utilisateur'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_role'] = $user['ID_Role'];
            $_SESSION['loggedIn'] = true;
            
            // Renouveler le token
            createRememberToken($connexion, $user['ID_Utilisateur']);
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['ID_Utilisateur'],
                    'email' => $user['Email'],
                    'role' => $user['ID_Role']
                ]
            ];
        }
    } catch (PDOException $e) {
        error_log("Erreur vérification token: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Déconnecter un utilisateur
 * 
 * @param PDO $connexion Connexion à la base de données
 * @return bool Succès ou échec
 */
function logoutUser($connexion) {
    try {
        // Démarrer la session si nécessaire
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Supprimer le token de la base de données
        if (isset($_SESSION['user_id'])) {
            $stmt = $connexion->prepare("
                UPDATE utilisateur 
                SET remember_token = NULL, 
                    token_expiry = NULL 
                WHERE ID_Utilisateur = :id
            ");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
        }
        
        // Supprimer le cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie("remember_token", "", time() - 3600, "/");
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur déconnexion: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtenir le chemin de redirection selon le rôle
 * 
 * @param int $roleId ID du rôle de l'utilisateur
 * @return string Chemin de redirection
 */

?>