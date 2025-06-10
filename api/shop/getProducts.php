<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$filters = [];

if (isset($_GET['section'])) $filters['section'] = $_GET['section'];
if (isset($_GET['sort'])) $filters['sort'] = $_GET['sort'];
if (isset($_GET['shoe_height'])) $filters['shoe_height'] = $_GET['shoe_height'];

if (isset($_GET['gender'])) {
    $filters['gender'] = $_GET['gender'];
}

if (isset($_GET['sport'])) {
    $filters['sport'] = $_GET['sport'];
}

if (isset($_GET['colors'])) {
    $filters['colors'] = $_GET['colors'];
}

if (isset($_GET['sizes'])) {
    $filters['sizes'] = $_GET['sizes'];
}

if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $filters['min_price'] = (float)$_GET['min_price'];
}
if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $filters['max_price'] = (float)$_GET['max_price'];
}

if (isset($_GET['is_on_sale'])) {
    $filters['is_on_sale'] = ($_GET['is_on_sale'] === 'true' || $_GET['is_on_sale'] === '1');
}
if (isset($_GET['is_bestseller'])) {
    $filters['is_bestseller'] = ($_GET['is_bestseller'] === 'true' || $_GET['is_bestseller'] === '1');
}
if (isset($_GET['is_new_arrival'])) {
    $filters['is_new_arrival'] = ($_GET['is_new_arrival'] === 'true' || $_GET['is_new_arrival'] === '1');
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 20;

$product = new Product();
$result = $product->getProducts($filters, $page, $limit);

if (isset($result['error'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Errore nel caricamento dei prodotti'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $result
]);