<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

if (empty($_POST['username']) || empty($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Username/Email e password sono obbligatori']);
    exit;
}

$user = new User();
$result = $user->login(trim($_POST['username']), $_POST['password']);

if ($result['success']) {
    $_SESSION['user_id'] = $result['utente']['id_utente'];
    $_SESSION['username'] = $result['utente']['username'];
    $_SESSION['nome_completo'] = $result['utente']['nome'] . ' ' . $result['utente']['cognome'];
}

echo json_encode($result);
?>