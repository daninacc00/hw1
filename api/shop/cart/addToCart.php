<?php

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../classes/Cart.php';
require_once __DIR__ . '/../../../classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!isset($userId)) {
    echo json_encode([
        'success' => false,
        'message' => "Devi essere loggato per accedere al tuo carrello",
        'error_type' => 'auth_required',
        'redirect_url' => '/pages/login/login.php'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'] ?? null;
$colorId = $data['colorId'] ?? null;
$sizeId = $data['sizeId'] ?? null;
$quantity = $data['quantity'] ?? null;

if ($productId <= 0 || $sizeId <= 0 || $quantity <= 0 || $colorId <= 0) {
    echo json_encode(['success' => false, 'message' => "Dati mancanti o non validi"]);
    exit;
}

$product = new Product();
$cart = new Cart($product);
$result = $cart->addProduct($userId, $productId, $colorId, $sizeId, $quantity);
echo json_encode($result);

exit;