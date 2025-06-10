<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../classes/Cart.php';
require_once __DIR__ . '/../../../classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!isset($userId)) {
    echo json_encode(['success' => false, 'message' => "Devi essere loggato per accedere al tuo carrello"]);
    exit;
}

$product = new Product();
$cart = new Cart($product);
$result = $cart->getUserCart($userId);
echo json_encode($result);

exit;
?>