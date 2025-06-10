<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovi arrivi</title>
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="shop.css" />
    <script src="shop.js" defer></script>
</head>

<body>
    <?php include '../../components/header/header.php'; ?>

    <div id="experience-wrapper">
        <header id="shop-header">
            <div class="category" id="categoryTitle">Sneakers e scarpe da uomo</div>
            <div class="header-controls">
                <button class="btn filter-btn">
                    Nascondi filtri
                </button>
                <button class="btn sort-btn">
                    Ordina per
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>
        </header>

        <div class="container">
            <div class="filters">
                <div class="filter-section" data-section="gender">
                    <div class="filter-title">
                        <h3>Genere</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-section" data-section="price">
                    <div class="filter-title">
                        <h3>Acquista per prezzo</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-section" data-section="discount">
                    <div class="filter-title">
                        <h3>Sconti e offerte</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-section" data-section="size">
                    <div class="filter-title">
                        <h3>Taglia/Misura</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-section" data-section="color">
                    <div class="filter-title">
                        <h3>Colore</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-section" data-section="height">
                    <div class="filter-title">
                        <h3>Altezza scarpa</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div id="product-grid" class="products">
            </div>
        </div>
    </div>

    <?php include '../../components/footer/footer.php'; ?>

</body>

</html>