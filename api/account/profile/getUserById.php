<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!isset($userId)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Accedi prima di continuare',
    ]);
    exit;
}

$userManager = new User();
$user = $userManager->getUserById($userId);

if (!$user) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Utente non trovato'
    ]);
    exit;
}

unset($user['password']);
unset($user['email']);

echo json_encode([
    'success' => true,
    'data' => $user
]);
exit;
