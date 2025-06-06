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

function formatItalianDate($dateString)
{
    $mesi = [
        1 => 'Gennaio',
        2 => 'Febbraio',
        3 => 'Marzo',
        4 => 'Aprile',
        5 => 'Maggio',
        6 => 'Giugno',
        7 => 'Luglio',
        8 => 'Agosto',
        9 => 'Settembre',
        10 => 'Ottobre',
        11 => 'Novembre',
        12 => 'Dicembre'
    ];

    $date = new DateTime($dateString);
    $mese = $mesi[(int)$date->format('n')];
    $anno = $date->format('Y');

    return "$mese $anno";
}

?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il mio Profilo - Nike</title>
    <link rel="stylesheet" href="/pages/account/profile/profile.css" />
</head>

<body>
    <div id="tab-profile" class="tab-content active">
        <section class="profile-section">
            <div class="profile-header">
                <div class="profile-avatar"><?php echo $user["nome"][0] . $user["cognome"][0] ?></div>
                <div class="profile-info">
                    <h1><?php echo $user["nome"] . " " . $user["cognome"] ?></h1>
                    <p>Member Nike da <?php
                                        echo formatItalianDate($user["data_registrazione"]);
                                        ?></p>
                </div>
            </div>
        </section>

        <?php include 'interests/interests.php'; ?>
    </div>

</body>