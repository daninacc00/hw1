<?php
// Includi la configurazione
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Reindirizza l'utente alla pagina di login se non è loggato
if (!isLoggedIn()) {
    redirect('login.php');
}

// Ottieni le informazioni dell'utente
$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="22" viewBox="0 0 60 22">
                    <path d="M60 4.6c0-.7-.6-1.3-1.3-1.3H41.6c-.4 0-.7.2-1 .4-.2.2-.4.5-.4 1v12.6c0 .7.6 1.3 1.3 1.3h17.1c.7 0 1.3-.6 1.3-1.3V4.6zm-3.9 10H43.9V7.2h12.2v7.4zm-15.8-10c0-.7-.6-1.3-1.3-1.3H22c-.7 0-1.3.6-1.3 1.3v12.6c0 .7.6 1.3 1.3 1.3h17.1c.7 0 1.3-.6 1.3-1.3V4.6zm-3.9 10H24.2V7.2h12.2v7.4zM19.5 3.3h-17C1.9 3.3 1.2 4 1.2 4.7v12.6c0 .7.6 1.3 1.3 1.3h17.1c.7 0 1.3-.6 1.3-1.3V4.6c0-.7-.6-1.3-1.3-1.3zm-2.6 10.2H4.7V7.2h12.2v6.3z" fill="currentColor"/>
                </svg>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main class="dashboard">
            <h1>Benvenuto, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Sei loggato con successo nell'area protetta.</p>
            <div class="card">
                <h2>Hello World!</h2>
                <p>Questa è la pagina principale dopo il login.</p>
                <p>Ora puoi iniziare a sviluppare le funzionalità della tua applicazione!</p>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tutti i diritti riservati.</p>
        </footer>
    </div>
</body>
</html>