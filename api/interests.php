<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Interest.php';

header('Content-Type: application/json');

// Simulazione utente (sostituisci con autenticazione reale)
$user_id = $_SESSION['user_id'] ?? 1;

$manager = new Interest($user_id);

// Gestione API
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (isset($_POST['action']) && $_POST['action'] === 'toggle_interest') {
            $interest_id = (int)$_POST['interest_id'];
            echo json_encode($manager->toggleInterest($interest_id));
            exit;
        }
        break;

    case 'GET':
        $category = $_GET['category'] ?? 'all';
        $categories = $manager->getCategories();
        $interests = $manager->getInterests($category);
        $userInterests = $manager->getUserInterests($user_id, $category);
        $count = $manager->countUserInterests();

        echo json_encode([
            'success' => true,
            'categories' => $categories,
            'interests' => $interests,
            'user_interests' => $userInterests,
            'user_interest_count' => $count
        ]);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
        exit;
}
