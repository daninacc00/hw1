<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../assets/classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

// Validazione input
if (empty($_POST['username']) || empty($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Username/Email e password sono obbligatori']);
    exit;
}

// Login utente
$user = new User();
$result = $user->loginUtente(trim($_POST['username']), $_POST['password']);

if ($result['success']) {
    // Salva dati utente in sessione
    $_SESSION['utente_id'] = $result['utente']['id_utente'];
    $_SESSION['username'] = $result['utente']['username'];
    $_SESSION['nome_completo'] = $result['utente']['nome'] . ' ' . $result['utente']['cognome'];
}

echo json_encode($result);
?>