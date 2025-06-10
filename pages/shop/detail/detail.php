<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovi arrivi</title>
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="detail.css" />
    <script src="detail.js" defer></script>
</head>

<body>
    <?php include '../../../components/header/header.php'; ?>

    <div class="container">
        <div id="loading" class="loading">
            Caricamento prodotto...
        </div>

        <div id="error" class="error" style="display: none;">
            <h2>Errore nel caricamento</h2>
        </div>

        <div id="product-detail" class="product-detail" style="display: none;">
        </div>
    </div>

    <?php include '../../../components/footer/footer.php'; ?>
</body>