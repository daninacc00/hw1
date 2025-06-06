<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../classes/User.php';

$userManager = new User();

$user_id = $_SESSION['user_id'] ?? 1;

$user = null;
if (isLoggedIn()) {
    $user = $userManager->getUtenteById($user_id);
}

function getUserFavorites($user_id) {
    return [
        [
            'name' => 'Air Max 90',
            'category' => 'Scarpe da uomo',
            'price' => '€119,99',
            'image' => '/assets/images/products/air-max-90.jpg'
        ],
        [
            'name' => 'React Infinity Run',
            'category' => 'Scarpe da running',
            'price' => '€159,99',
            'image' => '/assets/images/products/react-infinity.jpg'
        ],
        [
            'name' => 'Dri-FIT T-Shirt',
            'category' => 'Abbigliamento sportivo',
            'price' => '€34,99',
            'image' => '/assets/images/products/dri-fit-shirt.jpg'
        ]
    ];
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I miei preferiti - Nike</title>
    <link rel="stylesheet" href="/pages/account/favorites/favorites.css" />
</head>

<div id="tab-favorites" class="tab-content">
    <section class="favorites-section">
        <div class="section-header">
            <h2>I miei Preferiti</h2>
            <p>I prodotti che hai salvato per dopo</p>
        </div>
        
        <div class="favorites-content">
            <div class="favorites-grid">
                <?php
                $favorites = getUserFavorites($user_id);
                foreach ($favorites as $favorite): ?>
                    <div class="favorite-item">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($favorite['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($favorite['name']); ?>" />
                            <button class="remove-favorite">❤️</button>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($favorite['name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($favorite['category']); ?></p>
                            <p class="product-price"><?php echo htmlspecialchars($favorite['price']); ?></p>
                            <button class="add-to-cart-btn">Aggiungi al carrello</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>