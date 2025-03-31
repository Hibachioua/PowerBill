<?php
session_start();
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
    
    $connexion = connectDB();
    
    if ($connexion === null) {
        header("Location: ../IHM/login.php");
        exit();
    }
    
    try {
        $stmt = $connexion->prepare("SELECT ID_Utilisateur, Email, Mot_de_passe, ID_Role FROM utilisateur WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($password == $user['Mot_de_passe']) {
                $_SESSION['user_id'] = $user['ID_Utilisateur'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $user['ID_Role'];
                $_SESSION['loggedIn'] = true;
                
                if ($remember) {
                    setcookie("remember_user", $email, time() + (86400 * 30), "/");
                }
                
                // Redirection selon le rôle
                header("Location: " . getRedirectPath($user['ID_Role']));
                exit();
            } else {
                $_SESSION['login_error'] = "Email ou mot de passe incorrect";
            }
        } else {
            $_SESSION['login_error'] = "Email ou mot de passe incorrect";
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Erreur : " . $e->getMessage();
    }
    
    header("Location: ../IHM/login.php");
    exit();
} else {
    header("Location: ../IHM/login.php");
    exit();
}

// Fonction pour déterminer la redirection selon le rôle
function getRedirectPath($roleId) {
    switch ($roleId) {
        case 1: return "../IHM/client_dashboard.php";
        case 2: return "../IHM/agent_dashboard.php";
        case 3: return "../IHM/fournisseur_dashboard.php";
        default: return "../IHM/dashboard.php";
    }
}
?>