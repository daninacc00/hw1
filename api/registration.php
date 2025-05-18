<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

// Validazione input
$requiredFields = ['username', 'email', 'password', 'nome', 'cognome'];
$errors = [];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Il campo $field è obbligatorio";
    }
}

// Validazioni specifiche
if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Formato email non valido";
}

if (!empty($_POST['password']) && strlen($_POST['password']) < 8) {
    $errors[] = "La password deve essere di almeno 8 caratteri";
}

if (!empty($_POST['username']) && strlen($_POST['username']) < 3) {
    $errors[] = "L'username deve essere di almeno 3 caratteri";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Registrazione utente
$user = new User();

$result = $user->register(
    trim($_POST['username']),
    trim($_POST['email']),
    $_POST['password'],
    trim($_POST['nome']),
    trim($_POST['cognome'])
);

echo json_encode($result);
exit;

?>