<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../classes/User.php';

$userManager = new User();

$user_id = $_SESSION['user_id'] ?? 1;

$user = null;
if (isLoggedIn()) {
    $user = $userManager->getUserById($user_id);
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
        ],
        [
            'number' => '#NK2024003',
            'date' => '25 Maggio 2024',
            'status' => 'In preparazione',
            'status_class' => 'status-preparing',
            'product' => 'React Element 55 - Grigio/Blu',
            'total' => '€99,99'
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
    <div class="container">
        <h1 class="title">I miei Ordini</h1>
        <p class="subtitle">Traccia i tuoi ordini e visualizza la cronologia degli acquisti</p>
        
        <div class="order-filters">
            <button class="filter-btn active" data-filter="all">
                <span class="status-indicator active"></span>
                Tutti
            </button>
            <button class="filter-btn" data-filter="shipping">
                <span class="status-indicator shipping"></span>
                In corso
            </button>
            <button class="filter-btn" data-filter="delivered">
                <span class="status-indicator delivered"></span>
                Consegnati
            </button>
            <button class="filter-btn" data-filter="cancelled">
                <span class="status-indicator cancelled"></span>
                Annullati
            </button>
        </div>
        
        <div class="orders-grid">
            <?php
            $orders = getUserOrders($user_id);
            if (empty($orders)): ?>
                <div class="orders-empty-alert">
                    <p>Non hai ancora effettuato nessun ordine</p>
                    <a href="/products" class="btn-primary">Inizia a comprare</a>
                </div>
            <?php else:
                foreach ($orders as $order): ?>
                    <div class="order-card" data-status="<?php echo htmlspecialchars($order['status_class']); ?>">
                        <div class="order-header">
                            <div class="order-number"><?php echo htmlspecialchars($order['number']); ?></div>
                            <div class="order-status <?php echo htmlspecialchars($order['status_class']); ?>">
                                <span class="status-indicator <?php echo htmlspecialchars($order['status_class']); ?>"></span>
                                <?php echo htmlspecialchars($order['status']); ?>
                            </div>
                        </div>
                        
                        <div class="order-content">
                            <div class="order-product">
                                <h3 class="product-name"><?php echo htmlspecialchars($order['product']); ?></h3>
                                <p class="order-date">Ordinato il <?php echo htmlspecialchars($order['date']); ?></p>
                            </div>
                            
                            <div class="order-footer">
                                <div class="order-total"><?php echo htmlspecialchars($order['total']); ?></div>
                                <button class="btn-details">Dettagli</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>
</div>