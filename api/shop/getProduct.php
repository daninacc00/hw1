<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$productId = $_GET['id'] ?? null;

if (!isset($productId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID prodotto mancante o non valido',
    ]);
    exit;
}

$product = new Product();
$result = $product->getProductById($productId, $userId);
echo json_encode($result);

exit;
?>
