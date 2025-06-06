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

function getUserOrders($user_id) {
    return [
        [
            'number' => '#NK2024001',
            'date' => '15 Maggio 2024',
            'status' => 'Consegnato',
            'status_class' => 'status-delivered',
            'product' => 'Air Max 270 - Nero/Bianco',
            'total' => '€129,99'
        ],
        [
            'number' => '#NK2024002',
            'date' => '20 Maggio 2024',
            'status' => 'In spedizione',
            'status_class' => 'status-shipping',
            'product' => 'Felpa con cappuccio Nike Sportswear',
            'total' => '€79,99'
        ]
    ];
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I miei ordini - Nike</title>
    <link rel="stylesheet" href="/pages/account/orders/orders.css" />
</head>

<div id="tab-orders" class="tab-content">
    <section class="orders-section">
        <div class="section-header">
            <h2>I miei Ordini</h2>
            <p>Traccia i tuoi ordini e visualizza la cronologia degli acquisti</p>
        </div>
        
        <div class="orders-content">
            <div class="order-filters">
                <button class="filter-btn active">Tutti</button>
                <button class="filter-btn">In corso</button>
                <button class="filter-btn">Consegnati</button>
                <button class="filter-btn">Annullati</button>
            </div>
            
            <?php
            $orders = getUserOrders($user_id);
            foreach ($orders as $order): ?>
                <div class="order-item">
                    <div class="order-info">
                        <div class="order-number"><?php echo htmlspecialchars($order['number']); ?></div>
                        <div class="order-date">Ordinato il <?php echo htmlspecialchars($order['date']); ?></div>
                        <div class="order-status <?php echo htmlspecialchars($order['status_class']); ?>">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </div>
                    </div>
                    <div class="order-details">
                        <p><?php echo htmlspecialchars($order['product']); ?></p>
                        <p class="order-total"><?php echo htmlspecialchars($order['total']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>