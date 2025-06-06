<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nike</title>
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="index.css">
    <script src="index.js" defer></script>
</head>

<body>
    <?php include 'components/header/header.php'; ?>

    <div id="experience-wrapper">

        <div class="banner">
            <img class="banner-img" src="assets/images/landingpage/air-max-dn8-background.jpg" />
            <img class="banner-img" src="assets/images/landingpage/air-max-dn8.png" />
            <div class="banner-content">
                <h3 class="banner-title">
                    AIR MAX DN8
                </h3>
                <p class="banner-caption">
                    Prova la sensazione dell'unità Dynamic Air a tutta lunghezza
                </p>
                <a class="button" href="shop.html?section=news">Scopri</a>
            </div>
        </div>

        <div id="weather-container"></div>

        <section>
            <div class="section-container">
                <div class="section-card">
                    <img class="section-img" src="assets/images/landingpage/men-section.png" />
                    <div class="card-overlay">
                        <a class="button" href="men-category.html">Uomo</a>
                    </div>
                </div>
                <div class="section-card">
                    <img class="section-img" src="assets/images/landingpage/woman-section.png" />
                    <div class="card-overlay">
                        <a class="button" href="women-category.html">Donna</a>
                    </div>
                </div>
                <div class="section-card">
                    <img class="section-img" src="assets/images/landingpage/kid-section.png" />
                    <div class="card-overlay">
                        <a class="button" href="kids-category.html">Teenager</a>
                        <a class="button" href="kids-category.html">Kids</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="article-container-wrapper">
            <div class="article-container">
                <article class="article-card">
                    <img class="article-img" src="assets/images/landingpage/article-1.jpg" />
                    <div class="article-caption">
                        <p>Novità per il running</p>
                        <h3>Vomero 18</h3>
                        <a class="button" href="shop.html?cat=men&section=shoes">Acquista</a>
                    </div>
                </article>

                <article class="article-card">
                    <div class="article-card">
                        <img class="article-img" src="assets/images/landingpage/article-2.jpg" />
                        <div class="article-caption">
                            <p>Nike Football</p>
                            <h3>Mad Energy Pack</h3>
                            <a class="button" href="shop.html?cat=men&section=shoes">Acquista</a>
                        </div>
                </article>
            </div>

            <div class="article-container">
                <article class="article-card">
                    <img class="article-img" src="assets/images/landingpage/article-3.jpg" />
                    <div class="article-caption">
                        <p>Nike Style By</p>
                        <h3>Field General</h3>
                        <a class="button" href="shop.html?cat=men&section=shoes">Acquista</a>
                    </div>
                </article>

                <article class="article-card">
                    <div class="article-card">
                        <img class="article-img" src="assets/images/landingpage/article-4.jpg" />
                        <div class="article-caption">
                            <p>Per un comfort che dura tutto il giorno</p>
                            <h3>Collezione Nike 24.7</h3>
                            <a class="button" href="shop.html?cat=men&section=shoes">Acquista</a>
                        </div>
                </article>
            </div>

            <div class="article-container">
                <article class="article-card">
                    <img class="article-img" src="assets/images/landingpage/athlete-section.jpg" />
                    <div class="article-caption athlete-caption">
                        <p>La sezione dell'atleta</p>
                        <h3>Sha'Carri Richardson</h3>
                        <a class="button" href="shop.html?cat=men&section=shoes">Acquista</a>
                    </div>
                </article>
            </div>
        </div>

        <div id="slider-container">
            <div class="slider-header">
                <h2>Acquista le nostre icone</h2>
                <div class="slider-controls">
                    <button class="slider-btn prev"></button>
                    <button class="slider-btn next"></button>
                </div>
            </div>
        </div>

        <?php include 'components/menu/menu.php'; ?>
    </div>
    <?php include 'components/footer/footer.php'; ?>
</body>

</html>