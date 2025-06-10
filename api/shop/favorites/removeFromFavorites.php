<?php

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../classes/Favorites.php';
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
        'message' => "Devi essere loggato per accedere ai tuoi preferiti",
        'error_type' => 'auth_required',
        'redirect_url' => '/pages/login/login.php'
    ]);
    exit;
}

$productId = $_POST['productId'] ?? null;

if (!isset($productId)) {
    echo json_encode(['success' => false, 'message' => "ID prodotto mancante"]);
    exit;
}

$product = new Product();
$favorites = new Favorites($product);
$result = $favorites->removeProduct($userId, $productId);
echo json_encode($result);

exit;
?>