<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="registration.css">
    <script src="/utils/validation.js" defer></script>
    <script src="registration.js" defer></script>
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

            <div class="error-message hidden"></div>

            <form id="registerForm" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required>
                </div>

                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        required>
                </div>

                <div class="form-group">
                    <label for="cognome">Cognome</label>
                    <input
                        type="text"
                        id="cognome"
                        name="cognome"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required>
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
                        required>

                </div>

                <button type="submit" class="btn btn-primary">REGISTRATI</button>
            </form>

            <div class="auth-links">
                <p>Hai già un account? <a href="/pages/login/login.php">Accedi</a></p>
            </div>
        </div>
    </div>

</body>

</html>