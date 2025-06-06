
<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../classes/User.php';

$userManager = new User();

$user_id = $_SESSION['user_id'] ?? 1;

$user = null;
if (isLoggedIn()) {
    $user = $userManager->getUtenteById($user_id);
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I miei ordini - Nike</title>
    <link rel="stylesheet" href="/pages/account/settings/settings.css" />
</head>

<div id="tab-settings" class="tab-content">
    <section class="settings-section">
        <div class="section-header">
            <h2>Impostazioni Account</h2>
            <p>Gestisci le tue informazioni personali e le preferenze</p>
        </div>
        
        <div class="settings-content">
            <div class="settings-group">
                <h3>Informazioni Personali</h3>
                <div class="setting-item">
                    <label>Nome</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" />
                </div>
                <div class="setting-item">
                    <label>Cognome</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['cognome'] ?? ''); ?>" />
                </div>
                <div class="setting-item">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" />
                </div>
                <div class="setting-item">
                    <label>Data di nascita</label>
                    <input type="date" value="<?php echo htmlspecialchars($user['data_nascita'] ?? ''); ?>" />
                </div>
            </div>
            
            <div class="settings-group">
                <h3>Preferenze di Comunicazione</h3>
                <div class="setting-item checkbox-item">
                    <input type="checkbox" id="email-promo" checked />
                    <label for="email-promo">Ricevi email promozionali</label>
                </div>
                <div class="setting-item checkbox-item">
                    <input type="checkbox" id="sms-notifications" />
                    <label for="sms-notifications">Notifiche SMS</label>
                </div>
                <div class="setting-item checkbox-item">
                    <input type="checkbox" id="newsletter" checked />
                    <label for="newsletter">Newsletter Nike</label>
                </div>
            </div>
            
            <div class="settings-group">
                <h3>Privacy e Sicurezza</h3>
                <div class="setting-item">
                    <button class="settings-btn">Cambia Password</button>
                </div>
                <div class="setting-item">
                    <button class="settings-btn">Gestisci Privacy</button>
                </div>
                <div class="setting-item">
                    <button class="settings-btn danger">Elimina Account</button>
                </div>
            </div>
            
            <div class="settings-actions">
                <button class="save-btn">Salva Modifiche</button>
                <button class="cancel-btn">Annulla</button>
            </div>
        </div>
    </section>
</div>