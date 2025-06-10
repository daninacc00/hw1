<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../classes/Slider.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

$slider = new Slider();
$result = $slider->getSliderImages();

echo json_encode($result);
?>