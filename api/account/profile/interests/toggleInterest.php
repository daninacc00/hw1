<?php
require_once __DIR__ . '/../../../../includes/config.php';
require_once __DIR__ . '/../../../../classes/Interest.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

if (!isset($_POST['interestId']) || empty($_POST['interestId']) || $_POST['interestId'] <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID interesse non valido o mancante',
    ]);
    exit;
}

$manager = new Interest($userId);

$interestId = (int)$_POST['interestId'];
$result = $manager->toggleInterest($interestId);
echo json_encode($result);

exit;
