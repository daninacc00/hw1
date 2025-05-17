<?php
require_once '../classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['username'])) {
    echo json_encode(['available' => true]);
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utenti WHERE username = ?");
    $stmt->execute([trim($_POST['username'])]);
    $count = $stmt->fetchColumn();
    
    echo json_encode(['available' => $count == 0]);
} catch (Exception $e) {
    echo json_encode(['available' => true]); // In caso di errore, assumiamo sia disponibile
}
?>