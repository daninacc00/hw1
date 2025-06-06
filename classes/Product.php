<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../classes/Favorites.php';

class Product
{
    private $conn;
    private $favorites;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;

        $this->favorites = new Favorites();
    }

    private function escape($value)
    {
        if (is_array($value)) {
            return array_map(function ($v) {
                return mysqli_real_escape_string($this->conn, $v);
            }, $value);
        }
        return mysqli_real_escape_string($this->conn, $value);
    }

    public function getProducts($filters = [], $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;

        $select = "
        SELECT DISTINCT
            p.id, p.name, p.slug, p.short_description, p.price, p.original_price,
            p.discount_percentage, p.gender, p.shoe_height,
            p.is_bestseller, p.is_new_arrival, p.is_on_sale,
            p.rating, p.rating_count,
            c.display_name as category_name,
            s.display_name as section_name,
            sp.display_name as sport_name,
            pi.image_url as primary_image,
            COUNT(DISTINCT pc.color_id) as color_count
    ";

        $from = "
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN sections s ON p.section_id = s.id
        LEFT JOIN sports sp ON p.sport_id = sp.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN product_colors pc ON p.id = pc.product_id
    ";

        $joins = [];
        $conditions = ["p.status = 'active'"];

        // Gender
        if (!empty($filters['gender'])) {
            $genders = array_filter($filters['gender'], 'is_numeric');
            if (!empty($genders)) {
                $genderList = implode(',', array_map('intval', $genders));
                $conditions[] = "p.gender IN ($genderList)";
            }
        }

        // Section
        if (!empty($filters['section'])) {
            $section = $this->escape($filters['section']);
            $conditions[] = "s.slug = '$section'";
        }

        // Sport
        if (!empty($filters['sport'])) {
            $sports = is_array($filters['sport']) ? $filters['sport'] : [$filters['sport']];
            $escaped = array_map([$this, 'escape'], $sports);
            $in = "'" . implode("','", $escaped) . "'";
            $conditions[] = "sp.slug IN ($in)";
        }

        // Colors
        if (!empty($filters['colors'])) {
            $joins[] = "INNER JOIN product_colors pc2 ON p.id = pc2.product_id";
            $joins[] = "INNER JOIN colors col ON pc2.color_id = col.id";
            $escaped = array_map([$this, 'escape'], $filters['colors']);
            $in = "'" . implode("','", $escaped) . "'";
            $conditions[] = "col.slug IN ($in)";
        }

        // Sizes
        if (!empty($filters['sizes'])) {
            $joins[] = "INNER JOIN product_sizes ps ON p.id = ps.product_id";
            $joins[] = "INNER JOIN sizes sz ON ps.size_id = sz.id";
            $escaped = array_map([$this, 'escape'], $filters['sizes']);
            $in = "'" . implode("','", $escaped) . "'";
            $conditions[] = "sz.value IN ($in)";
            $conditions[] = "ps.stock_quantity > 0";
        }

        // Price range
        if (!empty($filters['min_price'])) {
            $conditions[] = "p.price >= " . floatval($filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $conditions[] = "p.price <= " . floatval($filters['max_price']);
        }

        // Shoe height
        if (!empty($filters['shoe_height'])) {
            $height = $this->escape($filters['shoe_height']);
            $conditions[] = "p.shoe_height = '$height'";
        }

        // Boolean flags
        if (!empty($filters['is_on_sale'])) $conditions[] = "p.is_on_sale = 1";
        if (!empty($filters['is_bestseller'])) $conditions[] = "p.is_bestseller = 1";
        if (!empty($filters['is_new_arrival'])) $conditions[] = "p.is_new_arrival = 1";

        // JOIN
        $joinClause = implode(" ", array_unique($joins));

        // WHERE
        $whereClause = empty($conditions) ? "" : "WHERE " . implode(" AND ", $conditions);

        // ORDER & PAGINATION
        $orderClause = "ORDER BY " . $this->buildOrderBy($filters['sort'] ?? 'newest');
        $limitClause = "LIMIT $limit OFFSET $offset";

        // Final query
        $query = "$select $from $joinClause $whereClause GROUP BY p.id $orderClause $limitClause";

        $result = mysqli_query($this->conn, $query);
        if (!$result) {
            return ['error' => mysqli_error($this->conn)];
        }

        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Count query
        $countQuery = "
        SELECT COUNT(DISTINCT p.id) as total
        $from $joinClause $whereClause
    ";

        $countResult = mysqli_query($this->conn, $countQuery);
        $total = 0;
        if ($countResult) {
            $row = mysqli_fetch_assoc($countResult);
            $total = $row['total'] ?? 0;
        }

        return [
            'products' => $products,
            'pagination' => [
                'total' => $total,
                'current_page' => $page,
                'per_page' => $limit,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }

    public function getProductById($productId, $userId)
    {
        $productId = mysqli_real_escape_string($this->conn, $productId);

        $sql = " SELECT 
                p.*,
                c.display_name as category_name,
                s.display_name as section_name,
                sp.display_name as sport_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN sections s ON p.section_id = s.id
            LEFT JOIN sports sp ON p.sport_id = sp.id
            WHERE p.id = '$productId' AND p.status = 'active'";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $product = mysqli_fetch_assoc($result);

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Prodotto non trovato'
            ];
        }

        $product['images'] = $this->getProductImages($productId);
        $product['colors'] = $this->getProductColors($productId);
        $product['sizes'] = $this->getProductSizes($productId);
        $product['is_favorite'] = $this->favorites->isProductInUserFavorites( $userId, $productId);

        return [
            'success' => true,
            'data' => $product
        ];
    }


    /**
     * Verifica la disponibilità della taglia
     */
    public function checkSizeAvailability($productId, $sizeId, $requestedQuantity)
    {
        $productId = mysqli_real_escape_string($this->conn, $productId);
        $sizeId = mysqli_real_escape_string($this->conn, $sizeId);
        $requestedQuantity = mysqli_real_escape_string($this->conn, $requestedQuantity);

        $sql = "SELECT ps.stock_quantity, s.value AS size_value
            FROM product_sizes AS ps
            LEFT JOIN sizes AS s ON ps.size_id = s.id
            WHERE ps.product_id = '$productId' AND ps.size_id = '$sizeId'";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);
        
        if (!$row) {
            return [
                'success' => false,
                'message' => 'Taglia non disponibile per questo prodotto'
            ];
        }

        if ($row['stock_quantity'] < $requestedQuantity) {
            return [
                'success' => false,
                'message' => 'Quantità non disponibile in magazzino'
            ];
        }

        return [
            'success' => true,
            'data' => $row
        ];
    }










    private function buildOrderBy($sort)
    {
        switch ($sort) {
            case 'price_asc':
                return 'p.price ASC';
            case 'price_desc':
                return 'p.price DESC';
            case 'name_asc':
                return 'p.name ASC';
            case 'name_desc':
                return 'p.name DESC';
            case 'rating':
                return 'p.rating DESC, p.rating_count DESC';
            case 'newest':
            default:
                return 'p.is_new_arrival DESC, p.created_at DESC';
        }
    }

    private function getProductImages($productId)
    {
        $productId = intval($productId);
        $query = "SELECT image_url, alt_text, is_primary, sort_order 
                  FROM product_images 
                  WHERE product_id = $productId 
                  ORDER BY is_primary DESC, sort_order ASC";

        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    private function getProductColors($productId)
    {
        $productId = intval($productId);
        $query = "SELECT c.id, c.name, c.hex_code 
                  FROM product_colors pc
                  JOIN colors c ON pc.color_id = c.id
                  WHERE pc.product_id = $productId
                  ORDER BY c.name";

        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    private function getProductSizes($productId)
    {
        $productId = intval($productId);
        $query = "SELECT s.id, s.value, s.type, ps.stock_quantity
                  FROM product_sizes ps
                  JOIN sizes s ON ps.size_id = s.id
                  WHERE ps.product_id = $productId
                  ORDER BY s.sort_order";

        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
