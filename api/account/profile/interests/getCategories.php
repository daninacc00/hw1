<?php
require_once __DIR__ . '/../../../../includes/config.php';
require_once __DIR__ . '/../../../../includes/functions.php';
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../classes/Interest.php';

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


$manager = new Interest($userId);

$result = $manager->getCategories();
echo json_encode($result);

exit;
?>