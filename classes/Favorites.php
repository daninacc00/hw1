<?php
require_once __DIR__ . '/../includes/database.php';

class Favorites
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
     * Aggiunge un prodotto ai preferiti
     */
    public function addProduct($userId, $productId)
    {
        if (!$userId || !$productId) {
            return [
                'success' => false,
                'message' => 'ID utente o prodotto non valido'
            ];
        }

        $productData = $this->product->getProductById($productId, $userId);
        if (!$productData['success']) {
            return $productData;
        }

        if ($this->isProductInUserFavorites($userId, $productId)) {
            return [
                'success' => false,
                'message' => 'Prodotto già presente nei preferiti'
            ];
        }

        return $this->insertFavorite($userId, $productId, $productData['data']);
    }

    /**
     * Rimuove un prodotto dai preferiti
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

        $sql = "DELETE FROM favorites WHERE user_id = '$userId' AND product_id = '$productId'";
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
                'message' => 'Prodotto non trovato nei preferiti'
            ];
        }

        return [
            'success' => true,
            'message' => 'Prodotto rimosso dai preferiti'
        ];
    }

    /**
     * Ottiene la lista dei prodotti preferiti di un utente
     */
    public function getUserFavorites($userId)
    {
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'ID utente non valido'
            ];
        }

        $userId = mysqli_real_escape_string($this->conn, $userId);

        $sql = "SELECT 
                p.id, 
                p.name,
                s.display_name AS section_name,
                cat.display_name AS category_name,
                p.price,
                pi.image_url,
                EXISTS (
                    SELECT * FROM cart c WHERE c.product_id = p.id AND c.user_id = f.user_id
                ) AS isInCart,
                f.created_at as added_date
            FROM favorites AS f 
            LEFT JOIN products AS p ON f.product_id = p.id 
            LEFT JOIN product_images AS pi ON p.id = pi.product_id
            LEFT JOIN sections AS s ON p.section_id = s.id
            LEFT JOIN categories AS cat ON p.category_id = cat.id
            WHERE f.user_id = '$userId'
            ORDER BY f.created_at DESC";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $favorites = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $favorites[] = $row;
        }

        return [
            'success' => true,
            'message' => 'Preferiti recuperati con successo',
            'data' => $favorites
        ];
    }

    /**
     * Verifica se un prodotto è nei preferiti dell'utente
     */
    public function isProductFavorite($userId, $productId)
    {
        if (!$userId || !$productId) {
            return ['success' => false, 'is_favorite' => false];
        }

        $isFavorite = $this->isProductInUserFavorites($userId, $productId);

        return [
            'success' => true,
            'is_favorite' => $isFavorite
        ];
    }


    /**
     * Verifica se il prodotto è già nei preferiti
     */
    public function isProductInUserFavorites($userId, $productId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $productId = mysqli_real_escape_string($this->conn, $productId);

        $sql = "SELECT id FROM favorites WHERE user_id = '$userId' AND product_id = '$productId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return isset($row);
    }

    /**
     * Restituisce il numero dei preferiti dell'utente
     */
    public function getNumOfFavorites($userId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);

        $sql = "SELECT COUNT(id) AS num_favorites FROM favorites WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return $row["num_favorites"];
    }

    /**
     * Inserisce un nuovo preferito
     */
    private function insertFavorite($userId, $productId, $productData)
    {
        $sql = "INSERT INTO favorites (user_id, product_id) VALUES ('$userId', '$productId')";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        if ($result) {
            return [
                'success' => true,
                'message' => 'Prodotto aggiunto ai preferiti',
                'data' => [
                    'product_name' => $productData['name']
                ]
            ];
        }
        return [
            'success' => false,
            'message' => "Errore durante l'inserimento del prodotto nei preferiti",
        ];
    }
}
