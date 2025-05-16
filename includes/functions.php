<?php
/**
 * Funzioni di utilità generali
 */

/**
 * Reindirizza a una URL specifica
 * 
 * @param string $url URL di destinazione
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Genera un token CSRF per proteggere i form
 * 
 * @return string Token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 * 
 * @param string $token Token da verificare
 * @return bool True se il token è valido, false altrimenti
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

/**
 * Pulisce i dati di input
 * 
 * @param string $data Dati da pulire
 * @return string Dati puliti
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>