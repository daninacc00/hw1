<?php
// Includi la classe User invece delle funzioni originali
require_once 'assets/classes/User.php';
require_once 'includes/config.php';

// Se l'utente è già loggato, reindirizza alla dashboard
if (isset($_SESSION['utente_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$username = '';
$email = '';
$nome = '';
$cognome = '';

// Processa il form di registrazione
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validazione lato server
    if (empty($username)) {
        $errors['username'] = 'Username è obbligatorio';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username deve contenere almeno 3 caratteri';
    }

    if (empty($email)) {
        $errors['email'] = 'Email è obbligatoria';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email non valida';
    }

    if (empty($nome)) {
        $errors['nome'] = 'Nome è obbligatorio';
    }

    if (empty($cognome)) {
        $errors['cognome'] = 'Cognome è obbligatorio';
    }

    if (empty($password)) {
        $errors['password'] = 'Password è obbligatoria';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password deve contenere almeno 8 caratteri';
    }

    if ($password !== $password_confirm) {
        $errors['password_confirm'] = 'Le password non corrispondono';
    }

    // Se non ci sono errori, registra l'utente
    if (empty($errors)) {
        $user = new User();
        $result = $user->registraUtente($username, $email, $password, $nome, $cognome);

        if ($result['success']) {
            // Registrazione riuscita, effettua il login automatico
            $loginResult = $user->loginUtente($username, $password);
            if ($loginResult['success']) {
                $_SESSION['utente_id'] = $loginResult['utente']['id_utente'];
                $_SESSION['username'] = $loginResult['utente']['username'];
                $_SESSION['nome_completo'] = $loginResult['utente']['nome'] . ' ' . $loginResult['utente']['cognome'];
                header('Location: index.php');
                exit;
            } else {
                header('Location: login.php');
                exit;
            }
        } else {
            $errors['registration'] = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js" defer></script>
    <script src="assets/js/registration.js" defer></script>
</head>

<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="22" viewBox="0 0 60 22">
                    <path d="M60 4.6c0-.7-.6-1.3-1.3-1.3H41.6c-.4 0-.7.2-1 .4-.2.2-.4.5-.4 1v12.6c0 .7.6 1.3 1.3 1.3h17.1c.7 0 1.3-.6 1.3-1.3V4.6zm-3.9 10H43.9V7.2h12.2v7.4zm-15.8-10c0-.7-.6-1.3-1.3-1.3H22c-.7 0-1.3.6-1.3 1.3v12.6c0 .7.6 1.3 1.3 1.3h17.1c.7 0 1.3-.6 1.3-1.3V4.6zm-3.9 10H24.2V7.2h12.2v7.4zM19.5 3.3h-17C1.9 3.3 1.2 4 1.2 4.7v12.6c0 .7.6 1.3 1.3 1.3h17.1c.7 0 1.3-.6 1.3-1.3V4.6c0-.7-.6-1.3-1.3-1.3zm-2.6 10.2H4.7V7.2h12.2v6.3z" fill="currentColor" />
                </svg>
            </div>

            <h1>DIVENTA UN MEMBRO</h1>

            <?php if (!empty($errors['registration'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($errors['registration']); ?></div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="register.php" novalidate>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo htmlspecialchars($username); ?>"
                        class="<?php echo !empty($errors['username']) ? 'error' : ''; ?>"
                        required>
                    <?php if (!empty($errors['username'])): ?>
                        <span class="error-text"><?php echo htmlspecialchars($errors['username']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email); ?>"
                        class="<?php echo !empty($errors['email']) ? 'error' : ''; ?>"
                        required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error-text"><?php echo htmlspecialchars($errors['email']); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Nuovi campi Nome e Cognome -->
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?php echo htmlspecialchars($nome); ?>"
                        class="<?php echo !empty($errors['nome']) ? 'error' : ''; ?>"
                        required>
                    <?php if (!empty($errors['nome'])): ?>
                        <span class="error-text"><?php echo htmlspecialchars($errors['nome']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="cognome">Cognome</label>
                    <input
                        type="text"
                        id="cognome"
                        name="cognome"
                        value="<?php echo htmlspecialchars($cognome); ?>"
                        class="<?php echo !empty($errors['cognome']) ? 'error' : ''; ?>"
                        required>
                    <?php if (!empty($errors['cognome'])): ?>
                        <span class="error-text"><?php echo htmlspecialchars($errors['cognome']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="<?php echo !empty($errors['password']) ? 'error' : ''; ?>"
                        required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error-text"><?php echo htmlspecialchars($errors['password']); ?></span>
                    <?php endif; ?>
                    <div class="password-requirements">
                        <p>La password deve contenere:</p>
                        <ul>
                            <li id="length">Almeno 8 caratteri</li>
                            <li id="uppercase">Almeno una lettera maiuscola</li>
                            <li id="lowercase">Almeno una lettera minuscola</li>
                            <li id="number">Almeno un numero</li>
                            <li id="special">Almeno un carattere speciale</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Conferma Password</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="<?php echo !empty($errors['password_confirm']) ? 'error' : ''; ?>"
                        required>
                    <?php if (!empty($errors['password_confirm'])): ?>
                        <span class="error-text"><?php echo htmlspecialchars($errors['password_confirm']); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">REGISTRATI</button>
            </form>

            <div class="auth-links">
                <p>Hai già un account? <a href="login.php">Accedi</a></p>
            </div>
        </div>
    </div>

</body>

</html>