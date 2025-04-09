<?php
require_once '../BD/consommation_annuelle_queries.php'; 
require_once '../BD/connexion.php';
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: ../IHM/Agent/agent_dashboard.php?message=Accès non autorisé - Session invalide");
    exit();
}

// Vérification du rôle client (ID_Role = 1)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    header("Location: ../IHM/Agent/agent_dashboard.php?message=Accès réservé aux agents");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../IHM/Agent/agent_dashboard.php?message=Identifiant client manquant");
    exit();
}

$UserId = $_SESSION['user_id'];
$pdo = connectDB();  // Connexion à la base de données avec PDO

if (!$pdo) {
    header("Location: ../IHM/Agent/agent_dashboard.php?message=Connexion DB impossible");
    exit();
}

// Récupérer l'ID_Agent depuis l'ID_Utilisateur
$AgentId = getAgentIdByUserId($pdo, $UserId);

if (!$AgentId) {
    header("Location: ../IHM/Agent/agent_dashboard.php?message=Agent avec l'ID_Utilisateur $UserId n'existe pas.");
    exit();
}

// Vérifier si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Récupération du fichier téléchargé
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];
    
    // Vérification du type de fichier
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileExt !== 'txt') {
        header("Location: ../IHM/Agent/agent_dashboard.php?message=Erreur: Veuillez télécharger un fichier de type .txt.");
        exit();
    } 

    // Vérification du format du nom du fichier
    preg_match('/^consommation_annuelle_(\d{4})\.txt$/', $fileName, $matches);
    if (empty($matches)) {
        header("Location: ../IHM/Agent/agent_dashboard.php?message=Erreur: Le nom du fichier doit suivre le format 'consommation_annuelle_(annee).txt'.");
        exit();
    }

    // Extraction de l'année à partir du nom du fichier
    $annee = $matches[1];

    // Ouverture du fichier et lecture de son contenu
    $fileContent = file_get_contents($fileTmpName);
    
    // Diviser le contenu en lignes
    $lines = explode("\n", $fileContent);
    
    // Parcours des lignes et insertion des données dans la base
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            preg_match('/Compteur ID: (\d+) \| Consommation: (\d+(\.\d+)?) kWh \| Année: (\d+)/', $line, $data);
            if (!empty($data)) {
                $compteurId = $data[1];
                $consommation = $data[2];
                $annee = $data[4];

                if (insertConsommation($pdo, $compteurId, $consommation, $annee, $AgentId, $fileName)) {
                    // Message de succès pour chaque insertion
                    continue;
                } else {
                    // Message d'erreur pour chaque échec d'insertion
                    continue;
                }
            }
        }
    }

    header("Location: ../IHM/Agent/agent_dashboard.php?message=Fichier traité avec succès!");
    exit();
}
?>