<?php
// logout.php - À placer dans le dossier Traitement
require_once "../BD/connexion.php";
require_once "../BD/LoginController.php";

// Créer l'instance du contrôleur
$db = DB::connect();
$loginController = new LoginController($db);

// Appeler la méthode de déconnexion du contrôleur
$logoutSuccess = $loginController->logout();

// Rediriger vers la page d'accueil
header("Location: ../IHM/index.php");
exit();
?>