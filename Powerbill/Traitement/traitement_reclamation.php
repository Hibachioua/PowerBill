<?php 

define('ROOT', str_replace("Traitement/traitement_reclamation.php", "", $_SERVER['SCRIPT_FILENAME']));
include_once(__DIR__ . '/../BD/requetes_reclamation.php');

if (!isset($_GET['action'])) {
    header("Location: ../IHM/index.php");
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'envoyer_reclamation':
        insererReclamation();
        exit;
        break;
    default:
        echo json_encode(["error" => "Action non reconnue"]);
        exit;
}

function insererReclamation() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_client = $_POST['id_client'];
        $type_reclamation = $_POST['type_reclamation'];
        $description = $_POST['description'];

        if (!empty($id_client) && !empty($type_reclamation) && !empty($description)) {
            if (ajouterReclamation($id_client, $type_reclamation, $description)) {
                echo "Réclamation envoyée avec succès !";
            } else {
                echo "Erreur lors de l’enregistrement.";
            }
        } else {
            echo "Tous les champs sont requis.";
        }
    }
}
?>
