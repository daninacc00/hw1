<?php
// Configurazione di base
session_start();

// Impostazioni di errore (Da disabilitare in produzione)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Costanti di configurazione
define('SITE_NAME', 'Nike Authentication');
define('SITE_URL', 'http://localhost/progetto-auth');

// Impostazioni di sicurezza
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_UPPER', true);
define('PASSWORD_REQUIRE_LOWER', true);
define('PASSWORD_REQUIRE_NUMBER', true);
define('PASSWORD_REQUIRE_SPECIAL', true);

// In un ambiente reale, queste impostazioni dovrebbero essere in un file esterno non tracciato
// Simuliamo un database con un array per lo sviluppo iniziale
$_SESSION['users'] = $_SESSION['users'] ?? [];
?>