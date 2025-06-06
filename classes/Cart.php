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

    /**
     * Aggiunge un prodotto al carrello con supporto per colore, taglia e quantità
     */
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

    /**
     * Restituisce il numero dei preferiti dell'utente
     */
    public function getNumOfProductInCart($userId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);

        $sql = "SELECT COUNT(id) AS num_product FROM cart WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return $row["num_product"];
    }

     /**
     * Rimuove un prodotto dal carrello
     */
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
                'message' => 'Prodotto non trovato nel carrello'
            ];
        }

        return [
            'success' => true,
            'message' => 'Prodotto rimosso dal carrello'
        ];
    }


    /**
     * Cerca un elemento esistente nel carrello
     */
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

    /**
     * Aggiorna la quantità di un elemento esistente nel carrello
     */
    private function updateCartItemQuantity($existingItem, $additionalQuantity, $sizeData, $productData)
    {
        $newQuantity = $existingItem['quantity'] + $additionalQuantity;

        if ($newQuantity > $sizeData['stock_quantity']) {
            return [
                'success' => false,
                'message' => 'Quantità totale supera la disponibilità in magazzino'
            ];
        }

        $sql = "UPDATE cart SET quantity = $newQuantity, updated_at = NOW() WHERE id = '" . $existingItem['id'] . "'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        return [
            'success' => true,
            'message' => 'Quantità prodotto aggiornata nel carrello',
            'data' => [
                'product_name' => $productData['name'],
                'size' => $sizeData['size_value'],
                'quantity' => $newQuantity
            ]
        ];
    }

    /**
     * Inserisce un nuovo elemento nel carrello
     */
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
