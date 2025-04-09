<?php
require_once "../BD/connexion.php";
require_once "../BD/loginModel.php";

session_start();

function getRedirectPath($roleId) {
    switch ($roleId) {
        case 1: // Client
            return '../IHM/Client/client_dashboard.php';
        case 2: // Agent
            return '../IHM/agent/agent_dashboard.php';
        case 3: // Fournisseur
            return '../IHM/fournisseur/fournisseur_dashboard.php';
        default:
            return '../IHM/dashboard.php'; // Page par défaut
    }
}

// Si c'est une soumission de formulaire (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]) ? true : false;
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../IHM/login.php?error=" . urlencode("Format d'email invalide"));
        exit();
    }
    
    // Établir la connexion à la base de données
    $connexion = connectDB();
    
    if ($connexion === null) {
        header("Location: ../IHM/login.php?error=" . urlencode("Erreur de connexion à la base de données"));
        exit();
    }
    
    $result = authenticateUser($connexion, $email, $password, $remember);
    
    if ($result['success']) {
        // Authentification réussie - rediriger vers la page appropriée
        header("Location: " . getRedirectPath($result['user']['role']));
        exit();
    } else {
        // Échec de l'authentification
        header("Location: ../IHM/login.php?error=" . urlencode($result['message']));
        exit();
    }
} 
else {
    header("Location: ../IHM/login.php");
    exit();
}
?>