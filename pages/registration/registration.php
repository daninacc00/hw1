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
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="registration.css">
    <script src="/utils/validation.js" defer></script>
    <script src="registration.js" defer></script>
</head>

<div class="container">
    <div class="auth-container">
        <div class="auth-logo">
            <img class="swoosh-icon" src="/assets/icons/icon.svg" alt="Nike" />

            <img class="air-jordan-icon" src="/assets/icons/air-jordan-icon.svg" />
        </div>

        <h1>Diventa un membro di Nike per ottenere i prodotti, l'ispirazione e la storia migliori dello sport</h1>

        <div class="auth-links">
            <p>Hai già un account? <a href="/pages/login/login.php">Accedi</a></p>
        </div>
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
                        <li id="length"><i class="fa-solid fa-xmark"></i> 8 caratteri</li>
                        <li id="uppercase"><i class="fa-solid fa-xmark"></i> una lettera maiuscola</li>
                        <li id="lowercase"><i class="fa-solid fa-xmark"></i> una lettera minuscola</li>
                        <li id="number"><i class="fa-solid fa-xmark"></i> un numero</li>
                        <li id="special"><i class="fa-solid fa-xmark"></i> un carattere speciale</li>
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

            <div class="terms-text">
                Registrandoti, accetti le <a href="#">condizioni d'uso</a> di Nike<br>
                e confermi di aver letto l'<a href="#">informativa sulla privacy</a><br>
                di Nike.
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Registrati</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>