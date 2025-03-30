<?php
function connectDB() {
    $serveur       = "localhost";
    $port          = "3307"; 
    $utilisateur   = "root";
    $motdepasse    = "";
    $basededonnees = "powerbill";

    try {
        $pdo = new PDO("mysql:host=$serveur;port=$port;dbname=$basededonnees", $utilisateur, $motdepasse);
        $pdo->exec("set names utf8");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Au lieu d'afficher l'erreur, stockez-la dans une variable de session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['login_error'] = "Erreur de connexion à la base de données: " . $e->getMessage();
        
        // Log l'erreur pour le débogage
        error_log("Erreur de connexion BD: " . $e->getMessage());
        
        return null;
    }
}
?>