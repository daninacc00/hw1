function handleAction(action) {
    switch (action) {
        case 'profile':
            window.location.href = "/pages/account/account.php";
            break;
        case 'logout':
            window.location.href = "/pages/logout.php";
            break;
        default:
            break;
    }
}

document.querySelectorAll('.action-item').forEach(item => {
    const action = item.getAttribute("data-action");
    item.addEventListener("click", () => handleAction(action))
});

let cartCount = 0;
let favoritesCount = 0;

// Funzione per creare e mostrare popup di notifica
function showNotificationPopup(type, title, message, actions = []) {
    // Rimuovi popup esistenti
    const existingPopup = document.querySelector('.notification-popup');
    if (existingPopup) {
        existingPopup.remove();
    }

    // Crea nuovo popup
    const popup = document.createElement('div');
    popup.className = `notification-popup ${type}`;

    popup.innerHTML = `
                <div class="popup-header">
                    <h3 class="popup-title">${title}</h3>
                    <button class="popup-close" onclick="closeNotificationPopup(this)">&times;</button>
                </div>
                <p class="popup-message">${message}</p>
                ${actions.length > 0 ? `
                    <div class="popup-actions">
                        ${actions.map(action => `
                            <a href="${action.url}" class="popup-btn ${action.primary ? 'primary' : ''}">${action.text}</a>
                        `).join('')}
                    </div>
                ` : ''}
            `;

    document.body.appendChild(popup);

    // Mostra popup con animazione
    setTimeout(() => {
        popup.classList.add('show');
    }, 10);

    // Auto-rimozione dopo 5 secondi
    setTimeout(() => {
        closeNotificationPopup(popup);
    }, 5000);
}

// Funzione per chiudere popup
function closeNotificationPopup(element) {
    const popup = element.classList ? element : element.closest('.notification-popup');
    popup.classList.remove('show');
    setTimeout(() => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    }, 300);
}

// Funzione per aggiornare contatore carrello
function updateCartCounter(quantity) {
    cartCount += quantity;
    const counter = document.getElementById('cart-counter');
    if (cartCount > 0) {
        counter.textContent = cartCount;
        counter.style.display = 'flex';
    } else {
        counter.style.display = 'none';
    }
}

// Funzione per aggiornare contatore preferiti
function updateFavoritesCounter(quantity) {
    favoritesCount += quantity;
    const counter = document.getElementById('favorites-counter');
    if (favoritesCount > 0) {
        counter.textContent = favoritesCount;
        counter.style.display = 'flex';
    } else {
        counter.style.display = 'none';
    }
}
