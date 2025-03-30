<?php
session_start();
require_once "../BD/connexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Format d'email invalide";
        header("Location: ../IHM/login.php");
        exit();
    }

    $connexion = connectDB(); // Initialisation correcte

    if (!$connexion) {
        $_SESSION['login_error'] = "Erreur de connexion à la base de données.";
        header("Location: ../IHM/login.php");
        exit();
    }

    try {
        // Vérifier les identifiants dans la base de données
        $stmt = $connexion->prepare("SELECT id, email, password, role FROM Utilisateur WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
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
                    $_SESSION['role_id'] = $roleData['id'];

                    if ($user['role'] == 'client' || $user['role'] == 'agent') {
                        $_SESSION['user_nom'] = $roleData['nom'];
                        $_SESSION['user_prenom'] = $roleData['prenom'];
                    } elseif ($user['role'] == 'fournisseur') {
                        $_SESSION['user_nom_entreprise'] = $roleData['nom_entreprise'];
                        $_SESSION['user_contact'] = $roleData['contact_principal'];
                    }
                }

                // Gestion du "Se souvenir de moi"
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $stmt = $connexion->prepare("UPDATE Utilisateur SET remember_token = :token, token_expiry = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = :id");
                    $stmt->bindParam(':token', $token);
                    $stmt->bindParam(':id', $user['id']);
                    $stmt->execute();
                    setcookie("remember_token", $token, time() + (86400 * 30), "/"); // Cookie valide 30 jours
                }

                // Redirection selon le rôle
                $redirectPath = getRedirectPath($user['role']);
                header("Location: $redirectPath");
                exit();
            } else {
                $_SESSION['login_error'] = "Email ou mot de passe incorrect";
            }
        } else {
            $_SESSION['login_error'] = "Email ou mot de passe incorrect";
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Erreur de connexion. Veuillez réessayer plus tard.";
    }

    header("Location: ../IHM/login.php");
    exit();
}

// Fonction pour obtenir le chemin de redirection en fonction du rôle
function getRedirectPath($role) {
    switch ($role) {
        case 'client': return "../IHM/client_dashboard.php";
        case 'agent': return "../IHM/agent_dashboard.php";
        case 'fournisseur': return "../IHM/fournisseur_dashboard.php";
        default: return "../IHM/dashboard.php";
    }
}
