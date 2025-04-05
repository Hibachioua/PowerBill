<?php 

session_start();


define('ROOT', str_replace("Traitement/traitement_reclamation.php", "", $_SERVER['SCRIPT_FILENAME']));
include_once(__DIR__ . '/../BD/RéclamationModel.php');



if (!isset($_GET['action'])) {
    header("Location: ../index.php");
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'creer_reclamation':
        inserer_Reclamation();
        exit;
        break;
    case 'consulter_reclamations':
        consulterReclamations();
        exit;
        break;
   
    default:
        $_SESSION['message'] = "Action non reconnue";
        $_SESSION['type'] = "error";
        exit;
}

function inserer_Reclamation() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {  // Changer à POST si le formulaire utilise POST
        $user_id = $_SESSION['user_id'];  // Récupérer l'ID de l'utilisateur depuis la session
        $type_reclamation = $_POST['type_reclamation'];
        $description = $_POST['description'];

        if (!empty($user_id) && !empty($type_reclamation) && !empty($description)) {
            if (ajouterReclamation($user_id, $type_reclamation, $description)) {
                $_SESSION['message'] = "Réclamation envoyée avec succès !";
                $_SESSION['type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de l’enregistrement.";
                $_SESSION['type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Tous les champs sont requis.";
            $_SESSION['type'] = "error";
        }
        // Redirige vers la page des réclamations après soumission
        header("Location: ../IHM/Client/form_réclamation.php");
        exit;
    }
}



function consulterReclamations() {
    header('Content-Type: application/json'); 

    if (!isset($_GET['id_client'])) {
        echo json_encode(["error" => "ID client manquant"]);
        exit;
    }

    $id_client = $_GET['id_client'];
    $reclamations = getReclamationsByClient($id_client);

    echo json_encode($reclamations);
    exit;
}

?>
