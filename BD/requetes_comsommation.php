<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get the PDO connection from connexion.php
$pdo = require_once "../BD/connexion.php";
require_once "../BD/requetes_comsommation.php";

// Verify we have a valid connection
if (!$pdo instanceof PDO) {
    die("Database connection failed");
}

$clientID = $_POST['clientID'] ?? null;
$meterValue = $_POST['meterValue'] ?? null;
$picture = $_FILES['counterPicture'] ?? null;

if ($clientID !== null && $meterValue !== null && isset($picture["error"]) && $picture["error"] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            die("Could not create upload directory");
        }
    }
    
    // Verify directory is writable
    if (!is_writable($uploadDir)) {
        die("Upload directory is not writable");
    }
    
    // Sanitize filename
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', basename($picture["name"]));
    $targetFilePath = $uploadDir . $filename;

    if (move_uploaded_file($picture["tmp_name"], $targetFilePath)) {
        $relativePath = 'uploads/' . $filename;
        
        try {
            if (insererConsommation($pdo, $clientID, $meterValue, $relativePath)) {
                header("Location: ../Vue/success.php");
                exit();
            } else {
                unlink($targetFilePath);
                header("Location: ../Vue/error.php?message=Insert failed");
                exit();
            }
        } catch (PDOException $e) {
            unlink($targetFilePath);
            header("Location: ../Vue/error.php?message=Database error");
            exit();
        }
    } else {
        header("Location: ../Vue/error.php?message=File upload failed");
        exit();
    }
} else {
    $errorCode = $picture["error"] ?? 'unknown';
    header("Location: ../Vue/error.php?message=Invalid data&code=$errorCode");
    exit();
}
?>