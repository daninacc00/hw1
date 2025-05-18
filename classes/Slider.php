<?php
require_once __DIR__ . '/../includes/database.php';

class Slider
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getSliderImages()
    {
        $sql = "SELECT * 
                FROM slider_images
                WHERE is_active = 1";
                
        $result = mysqli_query($this->conn, $sql);
        $images = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $image = [
                    'id' => (int)$row['id'],
                    'src' => $row['src'],
                    'alt' => $row['alt_text'],
                    'name' => $row['name'],
                    'isFreeShipping' => (bool)$row['is_free_shipping']
                ];
                $images[] = $image;
            }
            
            mysqli_free_result($result);
            
            return [
                'success' => true, 
                'data' => $images, 
                'count' => count($images)
            ];
        } else {
            return [
                'success' => false, 
                'message' => 'Errore durante il recupero delle immagini dello slider: ' . mysqli_error($this->conn)
            ];
        }
    }
}