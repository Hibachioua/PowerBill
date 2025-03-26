<?php
require_once 'connexion.php'; // Assurez-vous que cette connexion est correcte
function insererConsommation(
    PDO $pdo,
    int $ID_Compteur,
    int $Mois,
    int $Annee,
    float $Qté_consommé,
    string $Image_Compteur
): bool {
    $sql = "INSERT INTO consommation 
            (ID_Compteur, Mois, Annee, Qté_consommé, Image_Compteur) 
            VALUES (?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $ID_Compteur,
            $Mois,
            $Annee,
            $Qté_consommé,
            $Image_Compteur
        ]);
    } catch (PDOException $e) {
        error_log("Erreur insertion: " . $e->getMessage());
        return false;
    }
}
?>
