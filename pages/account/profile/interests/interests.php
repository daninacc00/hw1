<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/pages/account/profile/interests/interest.css" />
    <script src="/pages/account/profile/interests/interest.js" defer></script>
</head>
<section class="interests-section">
    <div class="interests-header">
        <h2>Interessi</h2>
        <button class="modify-btn">Modifica</button>
    </div>

    <div class="category-tabs" id="category-tabs">
    </div>

    <div class="interests-description">
        Aggiungi i tuoi interessi per scoprire una collezione di articoli basati sulle tue preferenze.
    </div>

    <div class="loading" id="loading">Caricamento...</div>

    <div class="interests-grid" id="interests-grid">
    </div>
</section>

<div id="interest-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Seleziona i tuoi interessi</h2>
            <button id="modal-close" class="modal-close">&times;</button>
        </div>

        <div class="modal-category-tabs" id="modal-category-tabs">
        </div>

        <div class="modal-body">
            <div id="modal-loading" class="modal-loading">Caricamento...</div>

            <div class="modal-interests-list" id="modal-interests-list">
            </div>
        </div>

        <div class="modal-footer">
            <button id="modal-cancel" class="modal-btn modal-cancel">Annulla</button>
            <button id="modal-save" class="modal-btn modal-save">Salva</button>
        </div>
    </div>
</div>