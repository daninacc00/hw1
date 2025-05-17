<?php
// Includi configurazione
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Effettua il logout
logoutUser();

// Reindirizza alla pagina di login
redirect('/pages/login/login.php');
?>