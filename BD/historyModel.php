<?php
require_once "connexion.php";
//historyModel.php
/**
 * Récupère l'historique des consommations de tous les clients
 * 
 * @param array $filtres Filtres optionnels (année, mois, client)
 * @return array Tableau des consommations
 */
function getAllConsommations($filtres = []) {
    $connexion = connectDB();
    $consommations = [];
    
    if ($connexion) {
        try {
            // Construction de la requête avec jointures
            $sql = "
                SELECT 
                    conso.ID_Consommation,
                    conso.Mois,
                    conso.Annee,
                    conso.Qté_consommé,
                    conso.Image_Compteur,
                    conso.status,
                    compt.ID_Compteur,
                    cl.ID_Client,
                    cl.Nom,
                    cl.Prenom,
                    cl.CIN,
                    u.Email
                FROM consommation conso
                INNER JOIN compteur compt ON conso.ID_Compteur = compt.ID_Compteur
                INNER JOIN client cl ON compt.ID_Client = cl.ID_Client
                INNER JOIN utilisateur u ON cl.ID_Utilisateur = u.ID_Utilisateur
                WHERE 1=1
            ";
            
            $params = [];
            
            // Ajouter des conditions selon les filtres
            if (isset($filtres['annee']) && !empty($filtres['annee'])) {
                $sql .= " AND conso.Annee = :annee";
                $params[':annee'] = $filtres['annee'];
            }
            
            if (isset($filtres['mois']) && !empty($filtres['mois'])) {
                $sql .= " AND conso.Mois = :mois";
                $params[':mois'] = $filtres['mois'];
            }
            
            if (isset($filtres['client']) && !empty($filtres['client'])) {
                $sql .= " AND cl.ID_Client = :client";
                $params[':client'] = $filtres['client'];
            }
            
            // Trier par date (plus récent d'abord) puis par client
            $sql .= " ORDER BY conso.Annee DESC, conso.Mois DESC, cl.Nom ASC";
            
            $stmt = $connexion->prepare($sql);
            
            // Lier les paramètres si présents
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->execute();
            $consommations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des consommations: " . $e->getMessage());
        }
    }
    
    return $consommations;
}

/**
 * Récupère la liste des années disponibles dans les consommations
 * 
 * @return array Liste des années
 */
function getDistinctYears() {
    $connexion = connectDB();
    $years = [];
    
    if ($connexion) {
        try {
            $stmt = $connexion->query("SELECT DISTINCT Annee FROM consommation ORDER BY Annee DESC");
            $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des années: " . $e->getMessage());
        }
    }
    
    return $years;
}

/**
 * Récupère la liste des clients
 * 
 * @return array Liste des clients
 */
function getClientsForFilter() {
    $connexion = connectDB();
    $clients = [];
    
    if ($connexion) {
        try {
            $stmt = $connexion->query("
                SELECT 
                    cl.ID_Client, 
                    cl.Nom, 
                    cl.Prenom, 
                    cl.CIN 
                FROM client cl 
                ORDER BY cl.Nom ASC
            ");
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des clients: " . $e->getMessage());
        }
    }
    
    return $clients;
}
?>