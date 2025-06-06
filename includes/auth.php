<?php
/**
 * Funzioni di autenticazione
 */

/**
 * Verifica se un utente è autenticato
 * 
 * @return bool True se l'utente è loggato, false altrimenti
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Ottiene l'utente corrente
 * 
 * @return array|null Dati dell'utente o null se non loggato
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = $_SESSION['user_id'];
    
    // In un'implementazione reale questo recupererebbe l'utente dal database
    // foreach ($_SESSION['users'] as $user) {
    //     if ($user['id'] === $userId) {
    //         // Non restituire la password
    //         $userCopy = $user;
    //         unset($userCopy['password']);
    //         return $userCopy;
    //     }
    // }
    
    return null;
}

/**
 * Verifica se esiste già un username
 * 
 * @param string $username Username da verificare
 * @return bool True se l'username esiste già, false altrimenti
 */
function usernameExists($username) {
    // In un'implementazione reale questo verificherebbe nel database
    // foreach ($_SESSION['users'] as $user) {
    //     if (strtolower($user['username']) === strtolower($username)) {
    //         return true;
    //     }
    // }
    
    return false;
}

/**
 * Valida una password secondo i criteri di sicurezza
 * 
 * @param string $password Password da validare
 * @return bool True se la password rispetta i criteri, false altrimenti
 */
function validatePassword($password) {
    $length = strlen($password) >= PASSWORD_MIN_LENGTH;
    $uppercase = PASSWORD_REQUIRE_UPPER ? preg_match('/[A-Z]/', $password) : true;
    $lowercase = PASSWORD_REQUIRE_LOWER ? preg_match('/[a-z]/', $password) : true;
    $number = PASSWORD_REQUIRE_NUMBER ? preg_match('/[0-9]/', $password) : true;
    $special = PASSWORD_REQUIRE_SPECIAL ? preg_match('/[^a-zA-Z0-9]/', $password) : true;
    
    return $length && $uppercase && $lowercase && $number && $special;
}

/**
 * Registra un nuovo utente
 * 
 * @param string $username Username dell'utente
 * @param string $email Email dell'utente
 * @param string $password Password dell'utente (verrà hashata)
 * @return bool True se la registrazione è avvenuta con successo, false altrimenti
 */
function registerUser($username, $email, $password) {
    // Verifica se l'username esiste già
    if (usernameExists($username)) {
        return false;
    }
    
    // Genera un ID unico
    $id = uniqid();
    
    // Hasha la password (in un'implementazione reale useresti password_hash())
    $hashedPassword = md5($password); // Nota: md5 non è sicuro! Usalo solo per demo
    
    // Crea un nuovo utente
    $user = [
        'id' => $id,
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Salva l'utente (in un'implementazione reale questo salverebbe nel database)
    // $_SESSION['users'][] = $user;
    
    return true;
}

/**
 * Autentica un utente
 * 
 * @param string $username Username dell'utente
 * @param string $password Password dell'utente
 * @return bool True se l'autenticazione è avvenuta con successo, false altrimenti
 */
function authenticateUser($username, $password) {
    // Hasha la password per il confronto
    $hashedPassword = md5($password); // Nota: md5 non è sicuro! Usalo solo per demo
    
    // Cerca l'utente (in un'implementazione reale questo cercherebbe nel database)
    // foreach ($_SESSION['users'] as $user) {
    //     if (strtolower($user['username']) === strtolower($username) && $user['password'] === $hashedPassword) {
    //         // Utente trovato, imposta la sessione
    //         $_SESSION['utente_id'] = $user['id'];
    //         return true;
    //     }
    // }
    
    return false;
}

/**
 * Effettua il logout dell'utente
 */
function logoutUser() {
    // Elimina la variabile di sessione utente_id
    unset($_SESSION['user_id']);
    
    // Opzionale: distruggi completamente la sessione
    // session_destroy();
}
?>