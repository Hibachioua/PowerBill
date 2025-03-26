<?php
class DB {
    static public function connect() {
        $serveur       = "localhost";
        $port          = "3307"; // Assure-toi que c'est bien le bon port MySQL
        $utilisateur   = "root";
        $motdepasse    = "";
        $basededonnees = "powerbill";

        try {
            $pdo = new PDO("mysql:host=$serveur;port=$port;dbname=$basededonnees;charset=utf8", $utilisateur, $motdepasse);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Erreur de connexion BD: " . $e->getMessage()); // Log de l'erreur
            return null;
        }
    }
}
