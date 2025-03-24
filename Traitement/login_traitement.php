<?php
// Démarrer la session
session_start();

// Inclure le fichier de connexion à la base de données
require_once "../BD/connexion.php"; 

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]) ? true : false;
    
   
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Format d'email invalide";
        header("Location: ../IHM/login.php");
        exit();
    }
    
    
    $connexion = DB::connect();
    
    
    if ($connexion === null) {
        
        header("Location: ../IHM/login.php");
        exit();
    }
    
    try {
        
        
        $stmt = $connexion->prepare("SELECT ID_Utilisateur, Email, Mot_de_passe, ID_Role FROM utilisateur WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // Vérifier si l'utilisateur existe
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Pour la phase de test, comparer les mots de passe directement
            if ($password == $user['Mot_de_passe']) {
                
                // Stocker les informations de base de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['ID_Utilisateur'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $user['ID_Role'];
                $_SESSION['loggedIn'] = true;
                
                // Si "Se souvenir de moi" est coché
                if ($remember) {
                    // Créer un cookie
                    setcookie("remember_user", $email, time() + (86400 * 30), "/");
                }
                
                switch ($user['ID_Role']) {
                    case 1: // Client
                        header("Location: ../IHM/client_dashboard.php");
                        break;
                    case 2: // Agent
                        header("Location: ../IHM/agent_dashboard.php");
                        break;
                    case 3: // Fournisseur
                        header("Location: ../IHM/fournisseur_dashboard.php");
                        break;
                    default:
                        header("Location: ../IHM/dashboard.php");
                }
                
                exit();
            } else {
                // Mot de passe incorrect
                $_SESSION['login_error'] = "Email ou mot de passe incorrect";
            }
        } else {
            // Utilisateur non trouvé
            $_SESSION['login_error'] = "Email ou mot de passe incorrect";
        }
    } catch (PDOException $e) {
        // Erreur de base de données
        $_SESSION['login_error'] = "Erreur : " . $e->getMessage();
    }
    
    // Si on arrive ici, l'authentification a échoué
    header("Location: ../IHM/login.php");
    exit();
} else {
    // Si quelqu'un tente d'accéder directement à ce script
    header("Location: ../IHM/login.php");
    exit();
}
?>