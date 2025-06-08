<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../classes/Product.php';

class Cart
{
    private $conn;
    private $product;

    public function __construct($product = null)
    {
        global $conn;
        $this->conn = $conn;

        $this->product = $product;
    }

    public function addProduct($userId, $productId, $colorId, $sizeId, $quantity = 1)
    {

        if (!$userId || !$productId || !$sizeId || !$colorId || $quantity <= 0) {
            return [
                'success' => false,
                'message' => 'Dati mancanti o non validi'
            ];
        }

        $productData = $this->product->getProductById($productId, $userId);
        if (!$productData['success']) {
            return $productData;
        }

        $sizeData = $this->product->checkSizeAvailability($productId, $sizeId, $quantity);
        if (!$sizeData['success']) {
            return $sizeData;
        }

        $existingItem = $this->getExistingCartItem($userId, $productId, $sizeId, $colorId);

        if ($existingItem) {
            return $this->updateCartItemQuantity($existingItem, $quantity, $sizeData['data'], $productData['data']);
        } else {
            return $this->insertNewCartItem($userId, $productId, $colorId, $sizeId, $quantity, $sizeData['data'], $productData['data']);
        }
    }

    public function getNumOfProductInCart($userId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);

        $sql = "SELECT COUNT(id) AS num_product FROM cart WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return $row["num_product"];
    }

    public function removeProduct($userId, $productId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $productId = mysqli_real_escape_string($this->conn, $productId);

        if (!$userId || !$productId) {
            return [
                'success' => false,
                'message' => 'ID utente o prodotto non valido'
            ];
        }

        $sql = "DELETE FROM cart WHERE user_id = '$userId' AND product_id = '$productId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $affectedRows = mysqli_affected_rows($this->conn);

        if ($affectedRows === 0) {
            return [
                'success' => false,
                'message' => 'Prodotto non trovato nel carrello'
            ];
        }

        return [
            'success' => true,
            'message' => 'Prodotto rimosso dal carrello'
        ];
    }

    public function removeAllProductsById($userId, $productId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $productId = mysqli_real_escape_string($this->conn, $productId);

        if (!$userId || !$productId) {
            return [
                'success' => false,
                'message' => 'ID utente o prodotto non valido'
            ];
        }

        $countSql = "SELECT COUNT(*) as count FROM cart WHERE user_id = '$userId' AND product_id = '$productId'";
        $countResult = mysqli_query($this->conn, $countSql) or die("Errore: " . mysqli_error($this->conn));

        $countRow = mysqli_fetch_assoc($countResult);
        $deletedCount = $countRow['count'];

        $sql = "DELETE FROM cart WHERE user_id = '$userId' AND product_id = '$productId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $affectedRows = mysqli_affected_rows($this->conn);

        if ($affectedRows === 0) {
            return [
                'success' => false,
                'message' => 'Prodotto non trovato nel carrello'
            ];
        }

        return [
            'success' => true,
            'message' => "Prodotto rimosso dal carrello",
            'deleted_count' => $deletedCount
        ];
    }

    public function getUserCart($userId)
    {
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'ID utente non valido'
            ];
        }

        $userId = mysqli_real_escape_string($this->conn, $userId);

        $sql = "SELECT 
                c.id as cart_item_id,
                c.quantity,
                c.created_at as added_date,
                p.id as product_id,
                p.name as product_name,
                p.price,
                p.original_price,
                p.discount_percentage,
                s.display_name AS section_name,
                cat.display_name AS category_name,
                col.name as color_name,
                col.hex_code as color_hex,
                sz.value as size_value,
                sz.type as size_type,
                pi.image_url as product_image,
                ps.stock_quantity as available_stock
            FROM cart AS c 
            LEFT JOIN products AS p ON c.product_id = p.id 
            LEFT JOIN product_images AS pi ON p.id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN sections AS s ON p.section_id = s.id
            LEFT JOIN categories AS cat ON p.category_id = cat.id
            LEFT JOIN colors AS col ON c.color_id = col.id
            LEFT JOIN sizes AS sz ON c.size_id = sz.id
            LEFT JOIN product_sizes AS ps ON p.id = ps.product_id AND c.size_id = ps.size_id
            WHERE c.user_id = '$userId'
            ORDER BY c.created_at DESC";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $cartItems = [];
        $subtotal = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $itemTotal = $row['price'] * $row['quantity'];
            $subtotal += $itemTotal;

            $cartItems[] = [
                'cart_item_id' => (int)$row['cart_item_id'],
                'product_id' => (int)$row['product_id'],
                'product_name' => $row['product_name'],
                'section_name' => $row['section_name'],
                'category_name' => $row['category_name'],
                'color_name' => $row['color_name'],
                'color_hex' => $row['color_hex'],
                'size_value' => $row['size_value'],
                'size_type' => (int)$row['size_type'],
                'quantity' => (int)$row['quantity'],
                'price' => (float)$row['price'],
                'original_price' => $row['original_price'] ? (float)$row['original_price'] : null,
                'discount_percentage' => (int)$row['discount_percentage'],
                'item_total' => $itemTotal,
                'product_image' => $row['product_image'],
                'available_stock' => (int)$row['available_stock'],
                'added_date' => $row['added_date']
            ];
        }

        $shippingCost = $subtotal >= 50 ? 0 : 5.99;
        $total = $subtotal + $shippingCost;

        return [
            'success' => true,
            'message' => 'Carrello recuperato con successo',
            'data' => [
                'items' => $cartItems,
                'summary' => [
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'items_count' => count($cartItems),
                    'free_shipping_eligible' => $subtotal >= 50
                ]
            ]
        ];
    }

    public function updateCartItemQuantity($userId, $cartItemId, $newQuantity)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $cartItemId = mysqli_real_escape_string($this->conn, $cartItemId);
        $newQuantity = mysqli_real_escape_string($this->conn, $newQuantity);

        if (!$userId || !$cartItemId || $newQuantity <= 0) {
            return [
                'success' => false,
                'message' => 'Dati non validi'
            ];
        }

        $checkSql = "SELECT c.product_id, c.size_id, c.quantity, ps.stock_quantity 
                    FROM cart c
                    LEFT JOIN product_sizes ps ON c.product_id = ps.product_id AND c.size_id = ps.size_id
                    WHERE c.id = '$cartItemId' AND c.user_id = '$userId'";

        $checkResult = mysqli_query($this->conn, $checkSql) or die("Errore: " . mysqli_error($this->conn));
        $cartItem = mysqli_fetch_assoc($checkResult);

        if (!$cartItem) {
            return [
                'success' => false,
                'message' => 'Elemento carrello non trovato'
            ];
        }

        if ($newQuantity > $cartItem['stock_quantity']) {
            return [
                'success' => false,
                'message' => 'Quantità richiesta non disponibile in magazzino'
            ];
        }

        $updateSql = "UPDATE cart SET quantity = '$newQuantity', updated_at = NOW() WHERE id = '$cartItemId'";
        $result = mysqli_query($this->conn, $updateSql) or die("Errore: " . mysqli_error($this->conn));

        if ($result) {
            return [
                'success' => true,
                'message' => 'Quantità aggiornata con successo',
                'data' => [
                    'new_quantity' => (int)$newQuantity,
                    'old_quantity' => (int)$cartItem['quantity']
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Errore durante l\'aggiornamento'
        ];
    }

    public function removeCartItem($userId, $cartItemId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $cartItemId = mysqli_real_escape_string($this->conn, $cartItemId);

        if (!$userId || !$cartItemId) {
            return [
                'success' => false,
                'message' => 'ID utente o elemento carrello non valido'
            ];
        }

        $checkSql = "SELECT id FROM cart WHERE id = '$cartItemId' AND user_id = '$userId'";
        $checkResult = mysqli_query($this->conn, $checkSql) or die("Errore: " . mysqli_error($this->conn));

        if (mysqli_num_rows($checkResult) === 0) {
            return [
                'success' => false,
                'message' => 'Elemento carrello non trovato'
            ];
        }

        $sql = "DELETE FROM cart WHERE id = '$cartItemId' AND user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Errore nella query: ' . mysqli_error($this->conn)
            ];
        }

        $affectedRows = mysqli_affected_rows($this->conn);

        if ($affectedRows === 0) {
            return [
                'success' => false,
                'message' => 'Elemento carrello non trovato'
            ];
        }

        return [
            'success' => true,
            'message' => 'Elemento rimosso dal carrello'
        ];
    }

    private function getExistingCartItem($userId, $productId, $sizeId, $colorId)
    {
        $sql = "SELECT id, quantity 
            FROM cart 
            WHERE user_id = '$userId' 
                AND product_id = '$productId' 
                AND size_id = '$sizeId' 
                AND color_id = '$colorId'";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return $row ?? null;
    }

    private function insertNewCartItem($userId, $productId, $colorId, $sizeId, $quantity, $sizeData, $productData)
    {
        $sql = "INSERT INTO cart (user_id, product_id, color_id, size_id, quantity) 
            VALUES ('$userId','$productId', '$colorId', '$sizeId', '$quantity')";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        return [
            'success' => true,
            'message' => 'Prodotto aggiunto al carrello con successo',
            'data' => [
                'product_name' => $productData['name'],
                'size' => $sizeData['size_value'],
                'quantity' => $quantity
            ]
        ];
    }
}
