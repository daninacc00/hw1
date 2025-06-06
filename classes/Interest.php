<?php
require_once __DIR__ . '/../includes/database.php';

class Interest
{
    private $conn;
    private $user_id;

    public function __construct($user_id)
    {
        global $conn;
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    // Aggiunta metodo per accedere alla connessione
    public function getConnection()
    {
        return $this->conn;
    }

    public function toggleInterest($interest_id)
    {
        // Check if already exists
        $check = mysqli_prepare($this->conn, "SELECT * FROM user_interests WHERE user_id = ? AND interest_id = ?");
        mysqli_stmt_bind_param($check, "ii", $this->user_id, $interest_id);
        mysqli_stmt_execute($check);
        $res = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($res) > 0) {
            // Remove
            $stmt = mysqli_prepare($this->conn, "DELETE FROM user_interests WHERE user_id = ? AND interest_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $this->user_id, $interest_id);
            mysqli_stmt_execute($stmt);
            return ['success' => true, 'action' => 'removed'];
        } else {
            // Add
            $stmt = mysqli_prepare($this->conn, "INSERT INTO user_interests (user_id, interest_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $this->user_id, $interest_id);
            mysqli_stmt_execute($stmt);
            return ['success' => true, 'action' => 'added'];
        }
    }

    public function getCategories()
    {
        $res = mysqli_query($this->conn, "SELECT * FROM interest_categories ORDER BY name");
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public function getInterests($category = 'all')
    {
        if ($category === 'all') {
            $sql = "
                SELECT i.*, ic.name as category_name, 
                (SELECT COUNT(*) > 0 FROM user_interests WHERE user_id = ? AND interest_id = i.id) as user_has_interest
                FROM interests i
                JOIN interest_categories ic ON i.category_id = ic.id
                ORDER BY ic.name, i.name
            ";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $this->user_id);
        } else {
            $sql = "
                SELECT i.*, ic.name as category_name, 
                (SELECT COUNT(*) > 0 FROM user_interests WHERE user_id = ? AND interest_id = i.id) as user_has_interest
                FROM interests i
                JOIN interest_categories ic ON i.category_id = ic.id
                WHERE i.category_id = ? OR ic.value = ?
                ORDER BY i.name
            ";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $this->user_id, $category, $category);
        }

        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public function getUserInterests($category = 'all')
    {
        if ($category === 'all') {
            $sql = "
            SELECT i.*, ic.name as category_name, ui.user_id IS NOT NULL as user_has_interest
            FROM interests i
            JOIN interest_categories ic ON i.category_id = ic.id
            JOIN user_interests ui ON i.id = ui.interest_id AND ui.user_id = ?
            ORDER BY ic.name, i.name            
        ";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $this->user_id);
        } else {
            $sql = "
            SELECT i.*, ic.name as category_name, ui.user_id IS NOT NULL as user_has_interest
            FROM user_interests ui
            JOIN interests i ON i.id = ui.interest_id
            JOIN interest_categories ic ON ic.id = i.category_id
            WHERE ic.value = ? AND ui.user_id = ?
            ORDER BY i.name
        ";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "si",  $category, $this->user_id); // supponendo che category sia un ID numerico
        }

        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }



    public function countUserInterests()
    {
        $sql = "SELECT COUNT(*) as total FROM user_interests WHERE user_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $this->user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res)['total'];
    }
}
