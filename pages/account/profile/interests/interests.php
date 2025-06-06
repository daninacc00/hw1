<?php
require_once __DIR__ . '/../../../../includes/config.php';
require_once __DIR__ . '/../../../../includes/functions.php';
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../classes/Interest.php';

$active_category = $_GET['category'] ?? 'all';

$user_id = $_SESSION['user_id'] ?? 1;
$manager = new Interest($user_id);

$categories = $manager->getCategories();
$interests = $manager->getInterests();
$userInterests = $manager->getUserInterests( $active_category);
$interestCount = $manager->countUserInterests();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/pages/account/profile/interests/interest.css" />
    <script src="/pages/account/profile/interests/interest.js" defer></script>
</head>

<body>

    <section class="interests-section">
        <div class="interests-header">
            <h2>Interessi</h2>
            <button class="modify-btn">Modifica</button>
        </div>

        <div class="category-tabs">
            <a href="?category=all" class="category-tab <?= $active_category === 'all' ? 'active' : '' ?>">
                Tutto
            </a>
            <a href="?category=sport" class="category-tab <?= $active_category === 'sport' ? 'active' : '' ?>">
                Sport
            </a>
            <a href="?category=articles" class="category-tab <?= $active_category === 'articles' ? 'active' : '' ?>">
                Articoli
            </a>
            <a href="?category=teams" class="category-tab <?= $active_category === 'teams' ? 'active' : '' ?>">
                Squadre
            </a>
            <a href="?category=athletes" class="category-tab <?= $active_category === 'athletes' ? 'active' : '' ?>">
                Atleti
            </a>
            <a href="?category=cities" class="category-tab <?= $active_category === 'cities' ? 'active' : '' ?>">
                Città
            </a>
        </div>

        <div class="interests-description">
            Aggiungi i tuoi interessi per scoprire una collezione di articoli basati sulle tue preferenze.
        </div>

        <div class="loading" id="loading">Caricamento...</div>

        <div class="interests-grid" id="interests-grid">
            <?php if (empty($userInterests)): ?>
                <div class="add-interests-card">
                    <div class="plus-icon">+</div>
                    <h3>Nessun interesse disponibile</h3>
                    <p>Non ci sono interessi in questa categoria al momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($userInterests as $userInterest): ?>
                    <div class="interest-card <?= $userInterest['user_has_interest'] ? 'selected' : '' ?>"
                        data-interest-id="<?= $userInterest['id'] ?>">
                        <div class="category-label"><?= htmlspecialchars($userInterest['category_name']) ?></div>
                        <div class="checkmark">✓</div>
                        <h3><?= htmlspecialchars($userInterest['name']) ?></h3>
                        <p><?= htmlspecialchars($userInterest['description'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>

                <div class="add-interests-card">
                    <div class="plus-icon">+</div>
                    <h3>Aggiungi interessi</h3>
                    <p>Personalizza ulteriormente i tuoi interessi</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div id="interest-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Seleziona i tuoi interessi</h2>
                <button id="modal-close" class="modal-close">&times;</button>
            </div>

            <div class="modal-category-tabs">
                <!-- I tab delle categorie saranno inseriti qui dinamicamente -->
                <button class="modal-category-tab active" data-category="all">Tutto (<?= count($userInterests) ?>)</button>
                <?php foreach ($categories as $category): ?>
                    <?php
                    $count = 0;
                    foreach ($userInterests as $userInterest) {
                        if ($userInterest['category_id'] == $category['id']) {
                            $count++;
                        }
                    }
                    ?>
                    <button class="modal-category-tab" data-category="<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['name']) ?> (<?= $count ?>)
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="modal-body">
                <div id="modal-loading" class="modal-loading">Caricamento...</div>

                <div class="modal-interests-list">
                    <?php
                    foreach ($interests as $interest):
                    ?>
                        <div class="modal-interest-item" data-interest-id="<?= $interest['id'] ?>" data-category="<?= $interest['category_id'] ?>">
                            <div class="modal-interest-image">
                                <img src="/assets/images/interests/<?= $interest['id'] ?>.jpg"
                                    alt="<?= htmlspecialchars($interest['name']) ?>"
                                    onerror="this.src='/assets/images/profile/interests/gym.jpg'">
                            </div>
                            <div class="modal-interest-name"><?= htmlspecialchars($interest['name']) ?></div>
                            <div class="modal-interest-checkbox">
                                <input type="checkbox" <?= $interest['user_has_interest'] ? 'checked' : '' ?>>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="modal-footer">
                <button id="modal-cancel" class="modal-btn modal-cancel">Annulla</button>
                <button id="modal-save" class="modal-btn modal-save">Salva</button>
            </div>
        </div>
    </div>

</body>

</html>