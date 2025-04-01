<?php
// Traitement/login_traitement.php
session_start();
require_once "../BD/connexion.php";
require_once "../BD/LoginController.php"; // Utilisez le nom de fichier correct ici

// 🔁 Fonction de redirection selon le rôle utilisateur
function getRedirectPath($roleId) {
    switch ($roleId) {
        case 1: // Client
            return '../IHM/client_dashboard.php';
        case 2: // Agent
            return '../IHM/agent_dashboard.php';
        case 3: // Fournisseur
            return '../IHM/fournisseur_dashboard.php';
        default:
            return '../IHM/dashboard.php'; // Page par défaut
    }
}

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
    
    // Établir la connexion à la base de données
    $connexion = connectDB();
    
    if ($connexion === null) {
        $_SESSION['login_error'] = "Erreur de connexion à la base de données";
        header("Location: ../IHM/login.php");
        exit();
    }
    
    // Utiliser la fonction d'authentification du modèle
    $result = authenticateUser($connexion, $email, $password, $remember);
    
    if ($result['success']) {
        // Authentification réussie - rediriger vers la page appropriée
        header("Location: " . getRedirectPath($result['user']['role']));
        exit();
    } else {
        // Échec de l'authentification
        $_SESSION['login_error'] = $result['message'];
        header("Location: ../IHM/login.php");
        exit();
    }
} else {
    // Redirection si accès direct au script
    header("Location: ../IHM/login.php");
    exit();
}
?>
