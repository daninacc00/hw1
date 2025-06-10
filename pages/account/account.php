<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/User.php';

if (!isLoggedIn()) {
    redirect('/pages/login/login.php');
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="account.css" />

    <script src="account.js" defer></script>
</head>

<body>
    <?php include '../../components/header/header.php'; ?>

    <header class="header">
        <nav>
            <a href="#" data-tab="profile" class="tab-link active">Profilo</a>
            <a href="#" data-tab="orders" class="tab-link">Ordini</a>
            <a href="#" data-tab="favorites" class="tab-link">Preferiti</a>
            <a href="#" data-tab="settings" class="tab-link">Impostazioni</a>
        </nav>
    </header>

    <div class="container">
        <?php include 'profile/profile.php'; ?>
        <?php include 'orders/orders.php'; ?>
        <?php include 'settings/settings.php'; ?>
    </div>

    <?php include '../../components/footer/footer.php' ?>

</body>

</html>