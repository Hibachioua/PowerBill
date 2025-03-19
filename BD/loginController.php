<?php

class LoginController {
    private $connexion;
    
    // Constructeur - injecte la connexion à la base de données
    public function __construct($connexion) {
        $this->connexion = $connexion;
    }
    
    // Méthode pour authentifier un utilisateur
    public function authenticate($email, $password, $remember = false) {
        try {
            // Vérifier si l'email existe dans la base de données
            $stmt = $this->connexion->prepare("SELECT id, nom, prenom, password FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Vérifier le mot de passe
                if (password_verify($password, $user['password'])) {
                    // Initialiser la session
                    session_start();
                    
                    // Stocker les données utilisateur dans la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['user_email'] = $email;
                    
                    // Gérer "Se souvenir de moi"
                    if ($remember) {
                        $this->createRememberToken($user['id']);
                    }
                    
                    return true;
                }
            }
            
            return false;
        } catch (PDOException $e) {
            // Log l'erreur
            error_log("Erreur d'authentification: " . $e->getMessage());
            return false;
        }
    }
    
    // Créer un token de rappel pour la fonctionnalité "Se souvenir de moi"
    private function createRememberToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 jours
        
        $stmt = $this->connexion->prepare("UPDATE utilisateurs SET remember_token = :token, token_expiry = :expiry WHERE id = :id");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        // Définir le cookie
        setcookie("remember_token", $token, time() + (86400 * 30), "/");
    }
    
    // Vérifier si un utilisateur est déjà connecté par token
    public function checkRememberToken() {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            $stmt = $this->connexion->prepare("
                SELECT id, nom, prenom, email 
                FROM utilisateurs 
                WHERE remember_token = :token 
                AND token_expiry > NOW()
            ");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Initialiser la session
                session_start();
                
                // Stocker les données utilisateur dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                
                // Renouveler le token
                $this->createRememberToken($user['id']);
                
                return true;
            }
        }
        
        return false;
    }
    
    // Déconnecter l'utilisateur
    public function logout() {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Supprimer le token de la base de données si l'utilisateur est connecté
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->connexion->prepare("UPDATE utilisateurs SET remember_token = NULL, token_expiry = NULL WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
        }
        
        // Supprimer le cookie de rappel
        if (isset($_COOKIE['remember_token'])) {
            setcookie("remember_token", "", time() - 3600, "/");
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
    }
}
?>