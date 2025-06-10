<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../classes/Favorites.php';
require_once __DIR__ . '/../../../classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!isset($userId)) {
    echo json_encode([
        'success' => false,
        'message' => "Devi essere loggato per accedere ai tuoi preferiti",
        'error_type' => 'auth_required',
        'redirect_url' => '/pages/login/login.php',
        'data' => []
    ]);
    exit;
}

$product = new Product();
$favorites = new Favorites($product);
$result = $favorites->getUserFavorites($userId);
echo json_encode($result);

exit;
?>