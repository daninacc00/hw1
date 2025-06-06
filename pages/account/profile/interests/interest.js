const categoryTabs = document.querySelector('.category-tabs');
if (categoryTabs) {
    categoryTabs.addEventListener('wheel', (e) => {
        e.preventDefault();
        e.currentTarget.scrollLeft += e.deltaY;
    });
}

function showAddInterestModal() {
    document.getElementById('interest-modal').style.display = 'flex';
}

function closeInterestModal() {
    document.getElementById('interest-modal').style.display = 'none';
}

const addInterestCard = document.querySelector('.add-interests-card');
if (addInterestCard) {
    addInterestCard.addEventListener('click', showAddInterestModal);
}

const modifyBtn = document.querySelector('.modify-btn');
if (modifyBtn) {
    modifyBtn.addEventListener('click', showAddInterestModal);
}


const modalCategoryTabs = document.querySelector('.modal-category-tabs');
if (modalCategoryTabs) {
    modalCategoryTabs.addEventListener('wheel', (e) => {
        e.preventDefault();
        e.currentTarget.scrollLeft += e.deltaY;
    });
}

const interestCheckboxes = document.querySelectorAll(".modal-interest-checkbox input");
interestCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('click', function () {
        const container = this.closest('.modal-interest-item');
        const interestId = container?.dataset.interestId;

        if (!interestId) {
            console.error("Interest ID non trovato");
            return;
        }

        toggleInterest(interestId, this);
    });
});


function switchModalCategory(categoryId) {
    // Aggiorna lo stato attivo del tab
    document.querySelectorAll('.modal-category-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.category === categoryId) {
            tab.classList.add('active');
        }
    });

    // Mostra/nascondi gli interessi in base alla categoria
    document.querySelectorAll('.modal-interest-item').forEach(item => {
        if (categoryId === 'all' || item.dataset.category === categoryId) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

const modalTabs = document.querySelectorAll('.modal-category-tab');
modalTabs.forEach(tab => {
    tab.addEventListener('click', function () {
        const categoryId = this.dataset.category;
        switchModalCategory(categoryId);
    });
});










document.getElementById('modal-close').addEventListener('click', closeInterestModal);
document.getElementById('modal-cancel').addEventListener('click', closeInterestModal);
document.getElementById('modal-save').addEventListener('click', saveInterestsAndClose);


function updateInterestCards(userInterests) {
    const grid = document.getElementById("interests-grid");
    if (!grid) return;

    // Rimuovi solo le interest-card esistenti
    const oldCards = grid.querySelectorAll(".interest-card");
    oldCards.forEach(card => card.remove());

    // Se non ci sono interessi, mostra solo il messaggio "nessun interesse"
    if (userInterests.length === 0) {
        // Nascondi l'elemento "aggiungi interessi" se presente
        const addCard = grid.querySelector(".add-interests-card");
        if (addCard) addCard.style.display = "none";
        return;
    }

    // Altrimenti, mostra tutte le nuove card
    userInterests.forEach(interest => {
        const card = document.createElement("div");
        card.classList.add("interest-card");
        if (interest.user_has_interest) card.classList.add("selected");
        card.dataset.interestId = interest.id;

        const categoryLabel = document.createElement("div");
        categoryLabel.classList.add("category-label");
        categoryLabel.textContent = interest.category_name;

        const checkmark = document.createElement("div");
        checkmark.classList.add("checkmark");
        checkmark.textContent = "✓";

        const title = document.createElement("h3");
        title.textContent = interest.name;

        const description = document.createElement("p");
        description.textContent = interest.description || "";

        card.appendChild(categoryLabel);
        card.appendChild(checkmark);
        card.appendChild(title);
        card.appendChild(description);

        // Inserisci prima della card "Aggiungi interessi" se esiste, altrimenti in fondo
        const addCard = grid.querySelector(".add-interests-card");
        if (addCard) {
            grid.insertBefore(card, addCard);
        } else {
            grid.appendChild(card);
        }
    });

    // Assicurati che "aggiungi interessi" sia visibile
    const addCard = grid.querySelector(".add-interests-card");
    if (addCard) addCard.style.display = "";
}


// Funzione per selezionare/deselezionare un interesse
function toggleInterest(interestId, cardElement) {
    const loading = document.getElementById('modal-loading');
    loading.style.display = 'block';

    fetch('/api/interests.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=toggle_interest&interest_id=${interestId}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) throw new Error("Errore nella selezione");

        // Aggiorna visivamente il checkbox e la card
        if (data.action === 'added') {
            cardElement.classList.add('selected');
        } else {
            cardElement.classList.remove('selected');
        }

        // Dopo il toggle: recupera gli userInterests aggiornati
        return fetch('/api/interests.php')
            .then(res => res.json());
    })
    .then(updatedData => {
        loading.style.display = 'none';

        if (updatedData.success) {
            updateInterestCards(updatedData.user_interests);
            updateCategoryTabsCount(updatedData.user_interests);
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        console.error('Errore:', error);
    });
}



// Funzione per aggiornare il contatore degli interessi
function updateCategoryTabsCount(userInterests) {
    // Raccogli tutti i bottoni tab
    const tabButtons = document.querySelectorAll('.modal-category-tab');
    if (!tabButtons.length) return;

    // Calcola il conteggio per ogni categoria
    const counts = {
        all: userInterests.length,
    };

    userInterests.forEach(interest => {
        const catId = interest.category_id;
        if (!counts[catId]) {
            counts[catId] = 0;
        }
        counts[catId]++;
    });

    // Aggiorna i testi nei bottoni
    tabButtons.forEach(button => {
        const category = button.dataset.category;
        const baseText = button.textContent.split(' (')[0]; // rimuove il numero vecchio
        const count = counts[category] || 0;
        button.textContent = `${baseText} (${count})`;
    });
}


// Funzione per chiudere la modale


// Funzione per cambiare categoria nella modale


// Funzione per salvare le modifiche e chiudere la modale
function saveInterestsAndClose() {
    // Mostra un messaggio di conferma

    // Chiudi la modale
    closeInterestModal();
}







