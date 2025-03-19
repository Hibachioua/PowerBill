<?php
class DB {
    static public function connect() {
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
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }
}
?>