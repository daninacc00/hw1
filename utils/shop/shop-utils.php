<?php

class ShopUtils {
    public static function validateFilters($filters) {
        $validGenders = [0, 1, 2];
        $validSections = ['shoes', 'clothing', 'accessories'];
        $validSorts = ['price_asc', 'price_desc', 'name_asc', 'name_desc', 'newest', 'rating'];
        $validShoeHeights = ['low', 'mid', 'high'];
        
        $errors = [];
        
        if (isset($filters['gender'])) {
            $genders = $filters['gender'];
            foreach ($genders as $gender) {
                if (!in_array($gender, $validGenders)) {
                    $errors[] = "Genere non valido: $gender";
                }
            }
        }
        
        if (isset($filters['section']) && !in_array($filters['section'], $validSections)) {
            $errors[] = "Sezione non valida";
        }
        
        if (isset($filters['sort']) && !in_array($filters['sort'], $validSorts)) {
            $errors[] = "Ordinamento non valido";
        }
        
        if (isset($filters['shoe_height']) && !in_array($filters['shoe_height'], $validShoeHeights)) {
            $errors[] = "Altezza scarpa non valida";
        }
        
        if (isset($filters['min_price']) && (!is_numeric($filters['min_price']) || $filters['min_price'] < 0)) {
            $errors[] = "Prezzo minimo non valido";
        }
        
        if (isset($filters['max_price']) && (!is_numeric($filters['max_price']) || $filters['max_price'] < 0)) {
            $errors[] = "Prezzo massimo non valido";
        }
        
        if (isset($filters['min_price'], $filters['max_price']) && 
            $filters['min_price'] > $filters['max_price']) {
            $errors[] = "Il prezzo minimo non può essere maggiore del prezzo massimo";
        }
        
        return $errors;
    }
}

?>