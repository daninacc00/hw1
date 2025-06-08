<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/helpers/ShopUtils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$filters = [];

if (isset($_GET['gender']) && is_array($_GET['gender'])) {
    $filters['gender'] = array_map('intval', $_GET['gender']);
}

if (isset($_GET['section'])) $filters['section'] = $_GET['section'];
if (isset($_GET['sort'])) $filters['sort'] = $_GET['sort'];

if (isset($_GET['sport'])) {
    $filters['sport'] = is_array($_GET['sport']) ? $_GET['sport'] : [$_GET['sport']];
}

if (isset($_GET['colors'])) {
    $filters['colors'] = is_array($_GET['colors']) ? $_GET['colors'] : explode(',', $_GET['colors']);
}

if (isset($_GET['sizes'])) {
    $filters['sizes'] = is_array($_GET['sizes']) ? $_GET['sizes'] : explode(',', $_GET['sizes']);
}

if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $filters['min_price'] = floatval($_GET['min_price']);
}
if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $filters['max_price'] = floatval($_GET['max_price']);
}

if (isset($_GET['shoe_height'])) $filters['shoe_height'] = $_GET['shoe_height'];

if (isset($_GET['is_on_sale']) && $_GET['is_on_sale'] === 'true') {
    $filters['is_on_sale'] = true;
}
if (isset($_GET['is_bestseller']) && $_GET['is_bestseller'] === 'true') {
    $filters['is_bestseller'] = true;
}
if (isset($_GET['is_new_arrival']) && $_GET['is_new_arrival'] === 'true') {
    $filters['is_new_arrival'] = true;
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 20;

$errors = ShopUtils::validateFilters($filters);
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Parametri non validi',
        'errors' => $errors
    ]);
    exit;
}

$product = new Product();
$result = $product->getProducts($filters, $page, $limit);

if (isset($result['error'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Errore database: ' . $result['error'],
        'errors' => null,
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Prodotti recuperati con successo',
    'data' => $result,
]);
