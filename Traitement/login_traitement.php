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
    
    try {
        // Préparer la requête pour vérifier les identifiants dans la table Utilisateur
        $stmt = $connexion->prepare("SELECT id, email, password, role FROM Utilisateur WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // Vérifier si l'utilisateur existe
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Authentification réussie
                
                // Stocker les informations de base de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $user['role'];
                
                // Récupérer les informations spécifiques au rôle
                switch ($user['role']) {
                    case 'client':
                        $roleStmt = $connexion->prepare("SELECT id, nom, prenom FROM Client WHERE utilisateur_id = :id");
                        break;
                    case 'agent':
                        $roleStmt = $connexion->prepare("SELECT id, nom, prenom FROM Agent WHERE utilisateur_id = :id");
                        break;
                    case 'fournisseur':
                        $roleStmt = $connexion->prepare("SELECT id, nom_entreprise, contact_principal FROM Fournisseur WHERE utilisateur_id = :id");
                        break;
                    default:
                        $_SESSION['login_error'] = "Type d'utilisateur non reconnu";
                        header("Location: ../IHM/login.php");
                        exit();
                }
                
                $roleStmt->bindParam(':id', $user['id']);
                $roleStmt->execute();
                
                if ($roleStmt->rowCount() == 1) {
                    $roleData = $roleStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Stocker les données spécifiques au rôle
                    $_SESSION['role_id'] = $roleData['id'];
                    
                    if ($user['role'] == 'client' || $user['role'] == 'agent') {
                        $_SESSION['user_nom'] = $roleData['nom'];
                        $_SESSION['user_prenom'] = $roleData['prenom'];
                    } elseif ($user['role'] == 'fournisseur') {
                        $_SESSION['user_nom_entreprise'] = $roleData['nom_entreprise'];
                        $_SESSION['user_contact'] = $roleData['contact_principal'];
                    }
                }
                
                // Si "Se souvenir de moi" est coché
                if ($remember) {
                    // Générer un token unique
                    $token = bin2hex(random_bytes(32));
                    
                    // Stocker le token dans la base de données
                    $stmt = $connexion->prepare("UPDATE Utilisateur SET remember_token = :token, token_expiry = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = :id");
                    $stmt->bindParam(':token', $token);
                    $stmt->bindParam(':id', $user['id']);
                    $stmt->execute();
                    
                    // Définir un cookie qui expire dans 30 jours
                    setcookie("remember_token", $token, time() + (86400 * 30), "/");
                }
                
                // Rediriger vers le tableau de bord approprié selon le rôle
                switch ($user['role']) {
                    case 'client':
                        header("Location: ../IHM/client_dashboard.php");
                        break;
                    case 'agent':
                        header("Location: ../IHM/agent_dashboard.php");
                        break;
                    case 'fournisseur':
                        header("Location: ../IHM/fournisseur_dashboard.php");
                        break;
                    default:
                        header("Location: ../IHM/dashboard.php");
                }
                exit();
            } else {
                // Mot de passe incorrect
                $_SESSION['login_error'] = "Email ou mot de passe incorrect";
                header("Location: ../IHM/login.php");
                exit();
            }
        } else {
            // Utilisateur non trouvé
            $_SESSION['login_error'] = "Email ou mot de passe incorrect";
            header("Location: ../IHM/login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Erreur de base de données
        $_SESSION['login_error'] = "Erreur de connexion. Veuillez réessayer plus tard.";
        
        // En développement, vous pourriez vouloir voir l'erreur exacte
        // $_SESSION['login_error'] = "Erreur: " . $e->getMessage();
        
        header("Location: ../IHM/login.php");
        exit();
    }
} else {
    // Si quelqu'un tente d'accéder directement à ce script
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