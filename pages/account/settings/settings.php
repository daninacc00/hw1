<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../classes/User.php';

$userManager = new User();

$user_id = $_SESSION['user_id'] ?? 1;
$user = null;
if (isLoggedIn()) {
    $user = $userManager->getUserById($user_id);
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impostazioni - Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/pages/account/settings/settings.css" />
</head>

<div id="tab-settings" class="tab-content">
    <div class="settings-container">
        <div class="sidebar">
            <h2>Impostazioni</h2>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-user"></i>
                            Dettagli account
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-credit-card"></i>
                            Metodi di pagamento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            Indirizzi di consegna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-shopping-bag"></i>
                            Preferenze di acquisto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-envelope"></i>
                            Preferenze sulle comunicazioni
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            Privacy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-eye"></i>
                            Visibilità del profilo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">
                            <i class="nav-icon fas fa-link"></i>
                            Account collegati
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <main class="main-content">
            <div class="page-header">
                <h1>Dettagli account</h1>
            </div>

            <div class="form-section">
                <form>
                    <div class="form-group">
                        <label for="email">E-mail*</label>
                        <input type="email" id="email" class="form-control" value="yovico6214@inkight.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="password" id="password" class="form-control" value="**********" readonly style="max-width: 300px;">
                            <button type="button" class="btn-link">Modifica</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Numero di telefono</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="tel" id="phone" class="form-control" placeholder="Inserisci numero" style="max-width: 300px;">
                            <button type="button" class="btn-link">Aggiungi</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="birthdate">Data di nascita*</label>
                        <input type="date" id="birthdate" class="form-control" value="2000-04-28">
                    </div>

                    <div class="form-group">
                        <label>Posizione</label>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="country">Paese/regione*</label>
                                <div class="select-wrapper">
                                    <select id="country" class="form-select" required>
                                        <option value="IT" selected>Italia</option>
                                        <option value="US">Stati Uniti</option>
                                        <option value="FR">Francia</option>
                                        <option value="DE">Germania</option>
                                        <option value="ES">Spagna</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="province">Provincia</label>
                                <div class="select-wrapper">
                                    <select id="province" class="form-select">
                                        <option value="" selected>Seleziona provincia</option>
                                        <option value="MI">Milano</option>
                                        <option value="RM">Roma</option>
                                        <option value="NA">Napoli</option>
                                        <option value="TO">Torino</option>
                                        <option value="PA">Palermo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>