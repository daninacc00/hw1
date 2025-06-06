<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Favorites.php';
require_once __DIR__ . '/../../classes/Cart.php';

$userManager = new User();
$favorites = new Favorites();
$cart = new Cart();

$user_id = $_SESSION['user_id'] ?? 1;

$num_favorites = null;
$num_product_in_cart = null;
$user = null;
if (isLoggedIn()) {
    $user = $userManager->getUtenteById($user_id);
    $num_favorites = $favorites->getNumOfFavorites($user_id);
    $num_product_in_cart = $cart->getNumOfProductInCart($user_id);
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/components/header/header.css" />
    <script src="/components/header/header.js" defer></script>
</head>

<body>
    <div id="commerce-header">

        <div id="topbar">
            <nav id="brand-navbar">
                <ul class="desktop-navbar-list desktop-brand-list">
                    <li class="list-item">
                        <a class="link-item" href="https://www.nike.com/it/jordan">
                            <img class="link-img air-jordan-icon" src="/assets/icons/air-jordan-icon.svg" />
                        </a>
                    </li>
                    <li class="list-item">
                        <a class="link-item" href="https://www.converse.com/it/it/go">
                            <img class="link-img converse-icon" src="/assets/icons/converse-icon.svg" />
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="user-menu-navbar">
                <ul class="desktop-navbar-list desktop-user-menu-list">
                    <li class="list-item">
                        <a class="link-item" href="https://www.nike.com/it/retail">
                            <p class="link-text">Trova un negozio</p>
                        </a>
                        <div class="vertical-line"></div>
                    </li>
                    <li class="list-item">
                        <a class="link-item" href="https://www.nike.com/it/help">
                            <p class="link-text">Aiuto</p>
                        </a>
                        <div class="vertical-line"></div>
                    </li>
                    <li class="list-item">
                        <a class="link-item" href="https://www.nike.com/it/membership">
                            <p class="link-text">Unisciti a noi</p>
                        </a>
                        <div class="vertical-line"></div>
                    </li>
                    <li class="list-item">
                        <?php if ($user): ?>
                            <div class="tooltip-container">
                                <p class="link-text">
                                    <?php echo "Ciao, " . htmlspecialchars($user["nome"]); ?>
                                </p>
                                <div class="tooltip">
                                    <h3 class="tooltip-title">Account</h3>
                                    <ul class="action-list">
                                        <li class="action-item" data-action="profile">
                                            <span class="action-text">Profilo</span>
                                        </li>
                                        <li class="action-item" data-action="orders">
                                            <span class="action-text">Ordini</span>
                                        </li>
                                        <li class="action-item" data-action="favorites">
                                            <span class="action-text">Preferiti</span>
                                        </li>
                                        <li class="action-item" data-action="newsletter">
                                            <span class="action-text">Posta in arrivo</span>
                                        </li>
                                        <li class="action-item" data-action="experience">
                                            <span class="action-text">Esperienze</span>
                                        </li>
                                        <li class="action-item" data-action="settings">
                                            <span class="action-text">Impostazioni account</span>
                                        </li>
                                        <li class="action-item" data-action="logout">
                                            <span class="action-text">Esci</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <a class='link-item' href='/pages/login/login.php'>
                                <p class='link-text'>Accedi</p>
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>

        <header id="shopping-header">

            <div class="swoosh">
                <a class="link-item swoosh-link" href="/">
                    <img class="link-img swoosh-icon" src="/assets/icons/icon.svg" alt="Nike" />
                </a>
            </div>

            <nav id="shopping-navbar">
                <ul class="desktop-shopping-category">
                    <li class="list-item">
                        <a class="menu-hover-trigger-link" href="/pages/shop/shop.php">Novità e in evidenza</a>
                    </li>
                    <li class="list-item">
                        <a class="menu-hover-trigger-link" href="men-category.php">Uomo</a>
                    </li>
                    <li class="list-item">
                        <a class="menu-hover-trigger-link" href="women-category.php">Donna</a>
                    </li>
                    <li class="list-item">
                        <a class="menu-hover-trigger-link" href="kids-category.php">Kids</a>
                    </li>
                    <li class="list-item">
                        <a class="menu-hover-trigger-link" href="shop.php?section=outlet">Outlet</a>
                    </li>
                </ul>
            </nav>

            <div class="user-tools-container">

                <div id="search-bar-container">
                    <div class="search-header">
                        <div class="swoosh">
                            <a class="link-item swoosh-link" href="/index.php">
                                <img class="link-img swoosh-icon" src="/assets/icons/icon.svg" alt="Nike" />
                            </a>
                        </div>

                        <div class="desktop-search-bar">
                            <img src="/assets/icons/search-icon.svg" alt="Cerca" class="search-icon">
                            <input type="text" class="search-bar" placeholder="Cerca">
                        </div>

                        <div class="close-search">
                            <button class="close-search-btn">
                                Annulla
                            </button>
                        </div>
                    </div>

                    <div class="search-results">
                        <div class="search-content">
                            <p>I termini più ricercati</p>
                            <div class="search-tags">
                                <span class="search-tag button">air force 1</span>
                                <span class="search-tag button ">jordan 4</span>
                                <span class="search-tag button">dunk</span>
                                <span class="search-tag button">jordan</span>
                                <span class="search-tag button">dunk low</span>
                                <span class="search-tag button">scarpe da calcio</span>
                                <span class="search-tag button">tn</span>
                                <span class="search-tag button">field general</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="icon-button favorites-button">
                    <a href="/pages/shop/favorites/favorites.php" class="link-item favorites-link">
                        <img src="/assets/icons/hearth-icon.svg" alt="Preferiti">
                        <span
                            class="counter-badge"
                            id="favorites-counter"
                            style="display: <?php echo isset($num_favorites) && $num_favorites > 0
                                                ? "flex;" : "none;" ?>">
                            <?php echo $num_favorites ?>
                        </span>
                    </a>
                </div>

                <div class="icon-button cart-button">
                    <a href="/pages/shop/cart/cart.php" class="link-item cart-link">
                        <img src="/assets/icons/cart-icon.svg" alt="Carrello">
                        <span
                            class="counter-badge"
                            id="cart-counter"
                            style="display: <?php echo isset($num_product_in_cart) && $num_product_in_cart > 0
                                                ? "flex;" : "none;" ?>">
                            <?php echo $num_product_in_cart ?>
                        </span>
                    </a>
                </div>

                <div class="icon-button hamburger-button">
                    <img src="/assets/icons/hamburger-icon.svg" alt="Menu">
                </div>
            </div>

        </header>
    </div>