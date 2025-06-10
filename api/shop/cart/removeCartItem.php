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
    echo json_encode(['success' => false, 'message' => "Devi essere loggato per accedere al tuo carrello"]);
    exit;
}

$cartItemId = $_POST['cartItemId'] ?? null;

if (!isset($cartItemId)) {
    echo json_encode(['success' => false, 'message' => "ID elemento carrello mancante"]);
    exit;
}

$product = new Product();
$cart = new Cart($product);
$result = $cart->removeCartItem($userId, $cartItemId);
echo json_encode($result);

exit;
?>