<?php
require_once __DIR__ . '/../includes/database.php';

class Interest
{
    private $conn;
    private $userId;

    public function __construct($userId)
    {
        global $conn;
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function toggleInterest($interestId)
    {
        $sql = "SELECT * FROM user_interests WHERE user_id = '$this->userId' AND interest_id = '$interestId'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $userInterests = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $userInterests[] = $row;
        }

        if (count($userInterests) > 0) {

            $sql = "DELETE FROM user_interests WHERE user_id = '$this->userId' AND interest_id = '$interestId'";
            $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

            return ['success' => true, 'action' => 'removed', 'interestId' => $interestId, "message" => "Interesse rimosso con successo"];
        } else {
            $sql = "INSERT INTO user_interests (user_id, interest_id) VALUES ('$this->userId', '$interestId')";
            $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

            return ['success' => true, 'action' => 'added', 'interestId' => $interestId, "message" => "Interesse aggiunto con successo"];
        }
    }

    public function getCategories()
    {
        $sql = "SELECT * FROM interest_categories ORDER BY name";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }

        return [
            'success' => true,
            'data' => $categories
        ];
    }

    public function getInterests($category = 'all')
    {
        if ($category === 'all') {
            $sql = "SELECT i.*, ic.name as category_name, 
                (SELECT COUNT(*) > 0 FROM user_interests WHERE user_id = '$this->userId' AND interest_id = i.id) as user_has_interest
                FROM interests i
                JOIN interest_categories ic ON i.category_id = ic.id
                ORDER BY ic.name, i.name
            ";
        } else {
            $sql = "SELECT i.*, ic.name as category_name, 
                (SELECT COUNT(*) > 0 FROM user_interests WHERE user_id = '$this->userId' AND interest_id = i.id) as user_has_interest
                FROM interests i
                JOIN interest_categories ic ON i.category_id = ic.id
                WHERE i.category_id ='$category'? OR ic.value ='$category'
                ORDER BY i.name
            ";
        }

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $interests = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $interests[] = $row;
        }

        return [
            'success' => true,
            'data' => $interests
        ];
    }

    public function getUserInterests($category = 'all')
    {
        if ($category === 'all') {
            $sql = " SELECT i.*, ic.name as category_name, ui.user_id IS NOT NULL as user_has_interest
                FROM interests i
                JOIN interest_categories ic ON i.category_id = ic.id
                JOIN user_interests ui ON i.id = ui.interest_id AND ui.user_id = '$this->userId'
                ORDER BY ic.name, i.name            
            ";
        } else {
            $sql = "SELECT i.*, ic.name as category_name, ui.user_id IS NOT NULL as user_has_interest
                FROM user_interests ui
                JOIN interests i ON i.id = ui.interest_id
                JOIN interest_categories ic ON ic.id = i.category_id
                WHERE ic.value = '$category' AND ui.user_id = '$this->userId'
                ORDER BY i.name
            ";
        }

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        $userInterests = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $userInterests[] = $row;
        }

        return [
            'success' => true,
            'data' => $userInterests
        ];
    }
}
