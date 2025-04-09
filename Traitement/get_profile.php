<?php
// Traitement/get_profile.php
header('Content-Type: application/json');
require_once "../BD/connexion.php";
session_start();

// Vérifier si l'utilisateur est connecté et est un fournisseur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non autorisé'
    ]);
    exit;
}

// Fonction pour récupérer les détails du fournisseur
function getFournisseurDetails($userId) {
    $connexion = connectDB();
    $userData = null;
    
    if ($connexion) {
        try {
            // Jointure entre la table utilisateur et fournisseur
            $stmt = $connexion->prepare("
                SELECT 
                    u.ID_Utilisateur, 
                    u.Email,
                    f.ID_Fournisseur,
                    f.Nom as NomFournisseur
                FROM utilisateur u
                INNER JOIN fournisseur f ON u.ID_Utilisateur = f.ID_Utilisateur
                WHERE u.ID_Utilisateur = :userId AND u.ID_Role = 3
            ");
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des détails du fournisseur: " . $e->getMessage());
        }
    }
    
    return $userData;
}

$userId = $_SESSION['user_id'];
$userData = getFournisseurDetails($userId);

if (!$userData) {
    echo json_encode([
        'success' => false,
        'message' => 'Impossible de récupérer les informations du profil'
    ]);
    exit;
}

// Renvoyer les données au format JSON
echo json_encode([
    'success' => true,
    'user' => [
        'id' => $userData['ID_Utilisateur'],
        'email' => $userData['Email'],
        'id_fournisseur' => $userData['ID_Fournisseur'],
        'nom_fournisseur' => $userData['NomFournisseur']
    ]
]);
?>