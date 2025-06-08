<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il mio Profilo - Nike</title>
    <link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/favicon.ico" />

    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="profile/profile.css" />
    <script src="profile/profile.js" defer></script>
</head>

<div id="tab-profile" class="tab-content active">
    <section class="profile-section">
        <div id="loading" class="loading-spinner">
            <p>Caricamento profilo...</p>
        </div>
         <div id="error-message" class="error-message" style="display: none;">
                <p>Errore nel caricamento del profilo. Riprova più tardi.</p>
            </div>
        <div id="profile-content" class="profile-header" style="display: none;">
            <div class="profile-avatar" id="profile-avatar"></div>
            <div class="profile-info">
                <h1 id="profile-name"></h1>
                <p id="profile-member-since"></p>
            </div>
        </div>
    </section>

    <?php include 'profile/interests/interests.php'; ?>
</div>