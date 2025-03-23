<?php
class Database {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo === null) {
            $serveur       = "localhost";
            $port          = "3306"; 
            $utilisateur   = "douae";
            $motdepasse    = "roujina25";
            $basededonnees = "powerbill";

            try {
                self::$pdo = new PDO(
                    "mysql:host=$serveur;port=$port;dbname=$basededonnees;charset=utf8", 
                    $utilisateur, 
                    $motdepasse,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
