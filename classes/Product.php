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

    public function getProducts($filters = [], $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;

        $query = "SELECT DISTINCT
                p.id, p.name, p.slug, p.short_description, p.price, p.original_price,
                p.discount_percentage, p.gender, p.shoe_height,
                p.is_bestseller, p.is_new_arrival, p.is_on_sale,
                p.rating, p.rating_count,
                c.display_name as category_name,
                s.display_name as section_name,
                sp.display_name as sport_name,
                pi.image_url as primary_image,
                COUNT(DISTINCT pc.color_id) as color_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN sections s ON p.section_id = s.id
            LEFT JOIN sports sp ON p.sport_id = sp.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN product_colors pc ON p.id = pc.product_id";

        $where = " WHERE p.status = 'active'";

        if (!empty($filters['gender'])) {
            $genders = "";
            foreach ($filters['gender'] as $gender) {
                if (is_numeric($gender)) {
                    $genders .= intval($gender) . ",";
                }
            }
            if ($genders != "") {
                $genders = rtrim($genders, ",");
                $where .= " AND p.gender IN (" . $genders . ")";
            }
        }

        if (!empty($filters['section'])) {
            $section = mysqli_real_escape_string($this->conn, $filters['section']);
            $where .= " AND s.slug = '" . $section . "'";
        }

        if (!empty($filters['sport'])) {
            $sports = "";
            if (is_array($filters['sport'])) {
                foreach ($filters['sport'] as $sport) {
                    $sport = mysqli_real_escape_string($this->conn, $sport);
                    $sports .= "'" . $sport . "',";
                }
            } else {
                $sport = mysqli_real_escape_string($this->conn, $filters['sport']);
                $sports = "'" . $sport . "',";
            }
            if ($sports != "") {
                $sports = rtrim($sports, ",");
                $where .= " AND sp.slug IN (" . $sports . ")";
            }
        }

        if (!empty($filters['colors'])) {
            $query .= " INNER JOIN product_colors pc2 ON p.id = pc2.product_id";
            $query .= " INNER JOIN colors col ON pc2.color_id = col.id";

            $colors = "";
            foreach ($filters['colors'] as $color) {
                $color = mysqli_real_escape_string($this->conn, $color);
                $colors .= "'" . $color . "',";
            }
            if ($colors != "") {
                $colors = rtrim($colors, ",");
                $where .= " AND col.slug IN (" . $colors . ")";
            }
        }

        if (!empty($filters['sizes'])) {
            $query .= " INNER JOIN product_sizes ps ON p.id = ps.product_id";
            $query .= " INNER JOIN sizes sz ON ps.size_id = sz.id";

            $sizes = "";
            foreach ($filters['sizes'] as $size) {
                $size = mysqli_real_escape_string($this->conn, $size);
                $sizes .= "'" . $size . "',";
            }
            if ($sizes != "") {
                $sizes = rtrim($sizes, ",");
                $where .= " AND sz.value IN (" . $sizes . ")";
                $where .= " AND ps.stock_quantity > 0";
            }
        }

        if (!empty($filters['min_price'])) {
            $min_price = floatval($filters['min_price']);
            $where .= " AND p.price >= " . $min_price;
        }

        if (!empty($filters['max_price'])) {
            $max_price = floatval($filters['max_price']);
            $where .= " AND p.price <= " . $max_price;
        }

        if (!empty($filters['shoe_height'])) {
            $height = mysqli_real_escape_string($this->conn, $filters['shoe_height']);
            $where .= " AND p.shoe_height = '" . $height . "'";
        }

        if (!empty($filters['is_on_sale'])) {
            $where .= " AND p.is_on_sale = 1";
        }
        if (!empty($filters['is_bestseller'])) {
            $where .= " AND p.is_bestseller = 1";
        }
        if (!empty($filters['is_new_arrival'])) {
            $where .= " AND p.is_new_arrival = 1";
        }

        $orderBy = " ORDER BY ";
        $sort = isset($filters['sort']) ? $filters['sort'] : 'newest';

        if ($sort == 'price_low') {
            $orderBy .= "p.price ASC";
        } else if ($sort == 'price_high') {
            $orderBy .= "p.price DESC";
        } else if ($sort == 'rating') {
            $orderBy .= "p.rating DESC";
        } else if ($sort == 'bestseller') {
            $orderBy .= "p.is_bestseller DESC, p.rating DESC";
        } else {
            $orderBy .= "p.created_at DESC";
        }

        $query .= $where . " GROUP BY p.id" . $orderBy . " LIMIT " . $limit . " OFFSET " . $offset;

        $result = mysqli_query($this->conn, $query) or die("Errore: " . mysqli_error($this->conn));;

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        $countQuery = "SELECT COUNT(DISTINCT p.id) as total
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN sections s ON p.section_id = s.id
        LEFT JOIN sports sp ON p.sport_id = sp.id
        LEFT JOIN product_colors pc ON p.id = pc.product_id";

        if (!empty($filters['colors'])) {
            $countQuery .= " INNER JOIN product_colors pc2 ON p.id = pc2.product_id";
            $countQuery .= " INNER JOIN colors col ON pc2.color_id = col.id";
        }
        if (!empty($filters['sizes'])) {
            $countQuery .= " INNER JOIN product_sizes ps ON p.id = ps.product_id";
            $countQuery .= " INNER JOIN sizes sz ON ps.size_id = sz.id";
        }

        $countQuery .= $where;

        $countResult = mysqli_query($this->conn, $countQuery) or die("Errore: " . mysqli_error($this->conn));;
        $total = 0;
        if ($countResult) {
            $row = mysqli_fetch_assoc($countResult);
            $total = $row['total'];
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
        $product['is_favorite'] = $this->favorites->isProductInUserFavorites($userId, $productId);

        return [
            'success' => true,
            'data' => $product
        ];
    }


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

    private function getProductImages($productId)
    {
        $productId = intval($productId);
        $query = "SELECT image_url, alt_text, is_primary, sort_order 
                  FROM product_images 
                  WHERE product_id = $productId 
                  ORDER BY is_primary DESC, sort_order ASC";

        $result = mysqli_query($this->conn, $query) or die("Errore: " . mysqli_error($this->conn));;
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

        $result = mysqli_query($this->conn, $query) or die("Errore: " . mysqli_error($this->conn));;
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

        $result = mysqli_query($this->conn, $query) or die("Errore: " . mysqli_error($this->conn));;
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
