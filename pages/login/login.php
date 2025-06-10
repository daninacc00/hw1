<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (isLoggedIn()) {
    redirect('/index.php');
}
?>

<!DOCTYPE html>
<html lang="it">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="login.css">
    <script src="/utils/validation.js" defer></script>
    <script src="login.js" defer></script>
</head>

<div class="container">
    <div class="auth-container">

        <div class="auth-logo">
            <img class="swoosh-icon" src="/assets/icons/icon.svg" alt="Nike" />

            <img class="air-jordan-icon" src="/assets/icons/air-jordan-icon.svg" />
        </div>

        <h1>Accedi con la tua e-mail</h1>
        <div class="auth-links">
            <p>Non hai un account? <a href="/pages/registration/registration.php">Registrati ora</a></p>
        </div>

        <div class="error-message hidden"></div>

        <form id="loginForm" method="POST">
            <div class="form-group">
            <label for="username">Email*</label>    
            <input
                    type="text"
                    id="username"
                    name="username">
            </div>

            <div class="form-group">
            <label for="username">Password*</label>    
                <input
                    type="password"
                    id="password"
                    name="password">
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Continua</button>
            </div>
        </form>


    </div>
</div>

</body>

</html>