<?php
// Configurazione di base
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Impostazioni di errore (Da disabilitare in produzione)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Costanti di configurazione
define('SITE_NAME', 'Nike Authentication');

// Impostazioni di sicurezza
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_UPPER', true);
define('PASSWORD_REQUIRE_LOWER', true);
define('PASSWORD_REQUIRE_NUMBER', true);
define('PASSWORD_REQUIRE_SPECIAL', true);
?>