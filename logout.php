<?php
// Includi configurazione
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Effettua il logout
logoutUser();

// Reindirizza alla pagina di login
redirect('login.php');
?>