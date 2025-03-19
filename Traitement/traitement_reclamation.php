<?php
// Inclure la connexion à la base de données et la fonction d'insertion
include '../BD/connexion.php';
include '../BD/requetes_reclamation.php';

// Vérifier si la méthode HTTP est bien POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérifier si tous les champs nécessaires sont envoyés via POST
    if (isset($_POST['clientID'], $_POST['complaintType'], $_POST['description']) &&
        !empty($_POST['clientID']) && !empty($_POST['complaintType']) && !empty($_POST['description'])) {

        // Récupérer et sécuriser les données du formulaire
        $id_client = intval($_POST['clientID']); // Assurez-vous que l'ID client est un entier
        $type_reclamation = htmlspecialchars($_POST['complaintType']); // Protéger contre les XSS
        $description = htmlspecialchars($_POST['description']); // Protéger contre les XSS

        // Connexion à la base de données via la classe DB
        try {
            $conn = DB::connect();
            if ($conn === null) {
                throw new Exception("Échec de la connexion à la base de données.");
            }

            // Appeler la fonction pour insérer la réclamation dans la base de données
            $message = insererReclamation($conn, $id_client, $type_reclamation, $description);

            // Afficher le message de confirmation ou d'erreur
            echo $message;

        } catch (Exception $e) {
            // Gestion d'erreurs générales, y compris la connexion à la base de données
            echo "Erreur : " . $e->getMessage();
        }

    } else {
        // Si l'un des champs est vide, afficher un message d'erreur
        echo "Veuillez remplir tous les champs.";
    }

} else {
    // Si la méthode n'est pas POST, afficher un message d'erreur
    echo "Méthode non autorisée.";
}
?>

