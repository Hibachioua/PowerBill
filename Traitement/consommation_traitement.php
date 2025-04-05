<?php
require_once __DIR__.'/../BD/requetes_consommation.php';

// Configuration et vérifications initiales
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/error.log');
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

// Traitement des requêtes
try {
    $pdo = connectDB();
    if (!$pdo) {
        throw new Exception("Connexion DB échouée");
    }

    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_compteurs':
                $compteurs = getCompteursClient($userId);
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'compteurs' => $compteurs]);
                exit;

            case 'get_last_image':
                try {
                    if (!isset($_GET['compteur_id'])) {
                        throw new Exception('Paramètre manquant');
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
            
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ]);
                    exit;
                }

            default:
                throw new Exception("Action non reconnue");
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['ID_Compteur', 'Qté_consommé'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est requis");
            }
        }

        $compteurId = (int)$_POST['ID_Compteur'];
        if (!verifierCompteurClient($userId, $compteurId)) {
            throw new Exception("Accès non autorisé à ce compteur");
        }

        // Validation fichier image
        if (empty($_FILES['counterPicture']['tmp_name'])) {
            throw new Exception("Photo du compteur requise");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['counterPicture']['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            throw new Exception("Format d'image non supporté");
        }

        // Enregistrement fichier
        $uploadDir = __DIR__.'/../uploads/compteurs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($_FILES['counterPicture']['name'], PATHINFO_EXTENSION);
        $fileName = 'compteur_'.$compteurId.'_'.date('m_Y').'_'.uniqid().'.'.$extension;
        $destination = $uploadDir.$fileName;

        if (!move_uploaded_file($_FILES['counterPicture']['tmp_name'], $destination)) {
            throw new Exception("Échec d'enregistrement de l'image");
        }

        // Préparation données pour insertion
        $data = [
            'compteurId' => $compteurId,
            'mois' => date('n'),
            'annee' => date('Y'),
            'quantite' => (float)$_POST['Qté_consommé'],
            'imagePath' => 'uploads/compteurs/'.$fileName,
            'status' => ($_POST['Qté_consommé'] > 200) ? 'anomalie' : 'pas d\'anomalie',
            'prixHT' => (float)$_POST['Qté_consommé'] * 0.9,
            'prixTTC' => (float)$_POST['Qté_consommé'] * 0.9 * 1.1
        ];

        $result = insererConsommation($data);
        
        $_SESSION['success'] = "Consommation enregistrée! Facture #".$result['factureID'];
        header("Location: ../IHM/ListeFactures.php");
        exit;
    }

    throw new Exception("Méthode non supportée");

} catch (Exception $e) {
    // Gestion des erreurs
    if (isset($destination) && file_exists($destination)) {
        unlink($destination);
    }

    $_SESSION['error'] = $e->getMessage();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../IHM/saisie_consommation.php");
    exit;
}