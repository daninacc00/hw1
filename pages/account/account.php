<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/pages/account/account.css" />
    <script src="/pages/account/account.js" defer></script>
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
        <?php include 'favorites/favorites.php'; ?>
        <?php include 'settings/settings.php'; ?>
    </div>

    <?php include '../../components/footer/footer.php' ?>

</body>

</html>