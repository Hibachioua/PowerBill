<?php 
// Traitement/traitement_reclamation.php

// Désactiver l'affichage des erreurs pour éviter de corrompre le JSON
ini_set('display_errors', 0);
error_reporting(0);

// Démarrer la session pour accéder à l'ID client
session_start();

// Inclure le modèle de réclamation
require_once "../BD/RéclamationModel.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] == 'consulter_reclamations') {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Utilisateur non connecté"]);
    } else {
        header("Location: ../IHM/login.php");
    }
    exit;
}

// Récupérer l'ID client de la session
$id_client = $_SESSION['user_id'];

// Déterminer l'action à effectuer
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if (empty($action)) {
    header("Location: ../IHM/Client/client_dashboard.php");
    exit;
}

switch ($action) {
    case 'creer_reclamation':
        // Redirection vers le formulaire de création
        header("Location: ../IHM/Client/form_réclamation.php");
        exit;
        break;
        
    case 'envoyer_reclamation':
        // Traitement du formulaire de réclamation (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type_reclamation = isset($_POST['type_reclamation']) ? htmlspecialchars($_POST['type_reclamation']) : '';
            $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';

            if (empty($type_reclamation) || empty($description)) {
                $_SESSION['message'] = "Tous les champs sont requis.";
                $_SESSION['type'] = "error";
            } else {
                if (ajouterReclamation($id_client, $type_reclamation, $description)) {
                    $_SESSION['message'] = "Réclamation envoyée avec succès !";
                    $_SESSION['type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de l'enregistrement.";
                    $_SESSION['type'] = "error";
                }
            }
            header("Location: ../IHM/Client/consulter_reclamations.php");
            exit;
        }
        break;
        
    case 'consulter_reclamations':
        try {
            // S'assurer qu'aucun texte ou erreur n'est envoyé avant le JSON
            ob_clean(); // Nettoyer le buffer de sortie
            
            // Définir le type de contenu
            header('Content-Type: application/json');
            
            // Récupérer les réclamations
            $reclamations = getReclamationsByClient($id_client);
            
            // S'assurer que le résultat est un tableau même s'il est vide
            if (!is_array($reclamations)) {
                $reclamations = [];
            }
            
            // Encoder proprement en JSON
            echo json_encode($reclamations);
        } catch (Exception $e) {
            // En cas d'erreur, renvoyer une réponse JSON
            header('Content-Type: application/json');
            echo json_encode(["error" => "Erreur serveur: " . $e->getMessage()]);
        }
        exit;
        break;
   
    default:
        $_SESSION['message'] = "Action non reconnue";
        $_SESSION['type'] = "error";
        header("Location: ../IHM/Client/consulter_reclamations.php");
        exit;
}
?>
<?php 
// Traitement/traitement_reclamation.php

// Désactiver l'affichage des erreurs pour éviter de corrompre le JSON
ini_set('display_errors', 0);
error_reporting(0);

// Démarrer la session pour accéder à l'ID client
session_start();

// Inclure le modèle de réclamation
require_once "../BD/RéclamationModel.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] == 'consulter_reclamations') {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Utilisateur non connecté"]);
    } else {
        header("Location: ../IHM/login.php");
    }
    exit;
}

// Récupérer l'ID client de la session
$id_client = $_SESSION['user_id'];

// Déterminer l'action à effectuer
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if (empty($action)) {
    header("Location: ../IHM/Client/client_dashboard.php");
    exit;
}

switch ($action) {
    case 'creer_reclamation':
        // Redirection vers le formulaire de création
        header("Location: ../IHM/Client/form_réclamation.php");
        exit;
        break;
        
    case 'envoyer_reclamation':
        // Traitement du formulaire de réclamation (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type_reclamation = isset($_POST['type_reclamation']) ? htmlspecialchars($_POST['type_reclamation']) : '';
            $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';

            if (empty($type_reclamation) || empty($description)) {
                $_SESSION['message'] = "Tous les champs sont requis.";
                $_SESSION['type'] = "error";
            } else {
                if (ajouterReclamation($id_client, $type_reclamation, $description)) {
                    $_SESSION['message'] = "Réclamation envoyée avec succès !";
                    $_SESSION['type'] = "success";
                } else {
                    $_SESSION['message'] = "Erreur lors de l'enregistrement.";
                    $_SESSION['type'] = "error";
                }
            }
            header("Location: ../IHM/Client/consulter_reclamations.php");
            exit;
        }
        break;
        
    case 'consulter_reclamations':
        try {
            // S'assurer qu'aucun texte ou erreur n'est envoyé avant le JSON
            ob_clean(); // Nettoyer le buffer de sortie
            
            // Définir le type de contenu
            header('Content-Type: application/json');
            
            // Récupérer les réclamations
            $reclamations = getReclamationsByClient($id_client);
            
            // S'assurer que le résultat est un tableau même s'il est vide
            if (!is_array($reclamations)) {
                $reclamations = [];
            }
            
            // Encoder proprement en JSON
            echo json_encode($reclamations);
        } catch (Exception $e) {
            // En cas d'erreur, renvoyer une réponse JSON
            header('Content-Type: application/json');
            echo json_encode(["error" => "Erreur serveur: " . $e->getMessage()]);
        }
        exit;
        break;
   
    default:
        $_SESSION['message'] = "Action non reconnue";
        $_SESSION['type'] = "error";
        header("Location: ../IHM/Client/consulter_reclamations.php");
        exit;
}
?>