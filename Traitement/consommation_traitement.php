<?php
require_once __DIR__ . '/../BD/requetes_consommation.php';

// Configuration et vérifications initiales
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

session_start();

// Middleware d'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: ../IHM/login.php");
    exit;
}

if ($_SESSION['user_role'] != 1) {
    $_SESSION['error'] = "Accès non autorisé";
    header("Location: ../IHM/login.php");
    exit;
}

try {
    $pdo = connectDB();
    if (!$pdo) {
        throw new Exception("Connexion DB échouée");
    }

    $userId = $_SESSION['user_id'];

    // Gestion des requêtes GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_compteurs':
                $compteurs = getCompteursClient($userId);
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'compteurs' => $compteurs]);
                exit;

            case 'get_last_image':
                if (!isset($_GET['compteur_id'])) {
                    throw new Exception('Paramètre compteur_id manquant');
                }
        
                $compteurId = (int)$_GET['compteur_id'];
                if ($compteurId <= 0) {
                    throw new Exception('ID invalide');
                }
        
                $imageData = getLastCounterImage($compteurId);
                if (!$imageData['success']) {
                    throw new Exception($imageData['error']);
                }
        
                echo json_encode([
                    'success' => true,
                    'image_url' => $imageData['image_url'],
                    'date' => $imageData['date']
                ]);
                exit;
        }
    }

    // Gestion des requêtes POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            /*$today = date('j'); 
            if ($today != 18) {
                throw new Exception("L'insertion n'est autorisée que le 18 de chaque mois.");
            }*/

            $ID_Compteur = filter_input(INPUT_POST, 'ID_Compteur', FILTER_VALIDATE_INT);
            $Mois = date('n');
            $Annee = date('Y');
            $Qte = filter_input(INPUT_POST, 'Qté_consommé', FILTER_VALIDATE_FLOAT);

            if (!$ID_Compteur || !$Qte) {
                throw new Exception("Données invalides");
            }

            if (!isset($_FILES['counterPicture'])) {
                throw new Exception("Aucun fichier uploadé");
            }

            $file = $_FILES['counterPicture'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur upload: " . $file['error']);
            }

            $typeMime = mime_content_type($file['tmp_name']);
            if (!in_array($typeMime, ['image/jpeg', 'image/png', 'image/gif'])) {
                throw new Exception("Type de fichier non supporté");
            }

            $pdo = connectDB();
            if (!$pdo) {
                throw new Exception("Connexion DB échouée");
            }

            $resultat = insererConsommation(
                $ID_Compteur,
                $Mois,
                $Annee,
                $Qte,
                $file['tmp_name'],
                $pdo
            );

            if ($resultat['success']) {
                if (isset($resultat['factureID'])) {
                    header("Location: ../IHM/ListeFactures.php?message=" . urlencode("Consommation ajoutée avec succès"));
                    exit;
                } else {
                    header("Location: ../IHM/ListeFactures.php?message=" . urlencode("Consommation ajoutée avec succès mais à vérifier par le fournisseur"));
                    exit;
                }
            } else {
                throw new Exception($resultat['message']);
            }
        }catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../IHM/Client/saisie_consommation.php?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
} catch (Exception $e) {
    if (isset($destination) && file_exists($destination)) {
        unlink($destination);
    }
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../IHM/Client/saisie_consommation.php");
    exit;
}