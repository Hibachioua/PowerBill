<?php
function connectDB() {
    $host = 'localhost';
    $dbname = 'powerbill';
    $username = 'root'; 
    $password = ''; 

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);        $pdo->exec("set names utf8");
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