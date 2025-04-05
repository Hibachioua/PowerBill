<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "../BD/connexion.php";
require_once '../BD/requetes_reclamation.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['clientID'], $_POST['complaintType'], $_POST['description']) &&
        !empty($_POST['clientID']) && !empty($_POST['complaintType']) && !empty($_POST['description'])) {
        
        $id_client = intval($_POST['clientID']); 
        $type_reclamation = htmlspecialchars($_POST['complaintType']); 
        $description = htmlspecialchars($_POST['description']); 

        echo "ID Client: $id_client<br>";
        echo "Type de réclamation: $type_reclamation<br>";
        echo "Description: $description<br>";

        // Ajout du bloc try
        try {
            $message = insererReclamation($conn, $id_client, $type_reclamation, $description);
            echo $message;
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }

    } else {
        echo "Veuillez remplir tous les champs.";
    }

} else {
    echo "Méthode non autorisée.";
}
?>
