loadCartItems();

function loadCartItems() {
    // Simuliamo una chiamata API al carrello
    // In futuro, quando creerai l'API del carrello, sostituisci con:
    // fetch("/api/shop/cart/getCart.php")
    
    // Per ora simuliamo la risposta basata sullo stato di login
    simulateCartResponse()
        .then(data => {
            if (data.success) {
                renderCart(data.data);
            } else {
                // Gestisci il caso di utente non autenticato
                if (data.error_type === 'auth_required') {
                    showAuthRequiredState(data.message, data.redirect_url);
                } else {
                    showError(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Errore nel caricamento del carrello:', error);
            showErrorMessage('Errore nel caricamento del carrello. Riprova più tardi.');
        });
}

// Funzione di simulazione - rimuovi quando crei l'API reale del carrello
function simulateCartResponse() {
    return new Promise((resolve) => {
        // Controlla se l'utente è loggato verificando se ci sono counter nel header
        const favCounter = document.getElementById('favorites-counter');
        const cartCounter = document.getElementById('cart-counter');
        
        // Se i counter non esistono o sono nascosti, simula utente non loggato
        if (!favCounter || !cartCounter || 
            favCounter.style.display === 'none' || 
            cartCounter.style.display === 'none') {
            
            resolve({
                success: false,
                message: "Devi essere loggato per accedere al tuo carrello",
                error_type: 'auth_required',
                redirect_url: '/pages/login/login.php',
                data: []
            });
        } else {
            // Simula carrello vuoto per utenti loggati
            resolve({
                success: true,
                data: {
                    items: [], // Array vuoto per ora
                    total: 0,
                    subtotal: 0,
                    shipping: 0,
                    tax: 0
                }
            });
        }
    });
}

function showAuthRequiredState(message, redirectUrl) {
    const cartContent = document.querySelector('.cart-content');
    cartContent.innerHTML = "";

    const authAlert = document.createElement("div");
    authAlert.classList.add("auth-required-alert");
    
    const icon = document.createElement("div");
    icon.style.cssText = `
        font-size: 3rem;
        margin-bottom: 20px;
        color: #6c757d;
    `;
    icon.textContent = "🛒";
    
    const title = document.createElement("h2");
    title.style.cssText = `
        color: #111; 
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
    `;
    title.textContent = "Accedi per vedere il tuo carrello";
    
    const description = document.createElement("p");
    description.style.cssText = `
        color: #6c757d; 
        font-size: 16px;
        margin-bottom: 30px;
        line-height: 1.5;
    `;
    description.textContent = message || "Devi essere loggato per accedere al tuo carrello";
    
    const loginBtn = document.createElement("a");
    loginBtn.href = redirectUrl || "/pages/login/login.php";
    loginBtn.className = "btn";
    loginBtn.style.cssText = `
        background: #000; 
        color: white; 
        padding: 12px 24px; 
        text-decoration: none; 
        border-radius: 24px; 
        display: inline-block; 
        margin-right: 16px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    `;
    loginBtn.textContent = "Accedi";
    loginBtn.addEventListener('mouseover', () => {
        loginBtn.style.background = '#333';
    });
    loginBtn.addEventListener('mouseout', () => {
        loginBtn.style.background = '#000';
    });
    
    const shopBtn = document.createElement("a");
    shopBtn.href = "/pages/shop/shop.php";
    shopBtn.className = "btn";
    shopBtn.style.cssText = `
        background: transparent; 
        color: #000; 
        padding: 12px 24px; 
        text-decoration: none; 
        border-radius: 24px; 
        display: inline-block;
        border: 1px solid #000;
        font-weight: 500;
        transition: all 0.3s ease;
    `;
    shopBtn.textContent = "Continua a fare shopping";
    shopBtn.addEventListener('mouseover', () => {
        shopBtn.style.background = '#000';
        shopBtn.style.color = 'white';
    });
    shopBtn.addEventListener('mouseout', () => {
        shopBtn.style.background = 'transparent';
        shopBtn.style.color = '#000';
    });
    
    authAlert.appendChild(icon);
    authAlert.appendChild(title);
    authAlert.appendChild(description);
    authAlert.appendChild(loginBtn);
    authAlert.appendChild(shopBtn);
    cartContent.appendChild(authAlert);
}

function renderCart(cartData) {
    const cartContent = document.querySelector('.cart-content');

    if (!cartContent) {
        console.error('Container carrello non trovato');
        return;
    }

    cartContent.innerHTML = '';

    if (!cartData.items || cartData.items.length === 0) {
        showEmptyCart();
        return;
    }

    // Crea la struttura del carrello con prodotti
    const cartItems = document.createElement('div');
    cartItems.className = 'cart-items';

    cartData.items.forEach(item => {
        const cartItem = createCartItem(item);
        cartItems.appendChild(cartItem);
    });

    const cartSummary = createCartSummary(cartData);

    cartContent.appendChild(cartItems);
    cartContent.appendChild(cartSummary);
}

function createCartItem(item) {
    const cartItem = document.createElement('div');
    cartItem.className = 'cart-item';
    cartItem.dataset.itemId = item.id;

    cartItem.innerHTML = `
        <img src="${item.image_url}" alt="${item.name}" class="item-image">
        <div class="item-details">
            <div class="item-name">${item.name}</div>
            <div class="item-info">Colore: ${item.color_name}</div>
            <div class="item-info">Taglia: EU ${item.size_value}</div>
            <div class="item-price">€${item.price}</div>
        </div>
        <div class="item-actions">
            <button class="remove-btn" onclick="removeFromCart('${item.id}')">Rimuovi</button>
            <div class="quantity-controls">
                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.quantity - 1})">-</button>
                <span class="quantity-display">${item.quantity}</span>
                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.quantity + 1})">+</button>
            </div>
        </div>
    `;

    return cartItem;
}

function createCartSummary(cartData) {
    const summary = document.createElement('div');
    summary.className = 'cart-summary';

    summary.innerHTML = `
        <div class="summary-row">
            <span>Subtotale</span>
            <span>€${cartData.subtotal.toFixed(2)}</span>
        </div>
        <div class="summary-row">
            <span>Spedizione</span>
            <span>${cartData.shipping > 0 ? '€' + cartData.shipping.toFixed(2) : 'Gratuita'}</span>
        </div>
        <div class="summary-row">
            <span>Tasse</span>
            <span>€${cartData.tax.toFixed(2)}</span>
        </div>
        <div class="summary-row summary-total">
            <span>Totale</span>
            <span>€${cartData.total.toFixed(2)}</span>
        </div>
        <button class="checkout-btn" onclick="proceedToCheckout()">Procedi al checkout</button>
    `;

    return summary;
}

function removeFromCart(itemId) {
    // Qui faresti una chiamata all'API per rimuovere l'item
    console.log('Rimuovendo item:', itemId);
    
    // Per ora simula la rimozione
    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.style.opacity = '0.5';
        setTimeout(() => {
            loadCartItems(); // Ricarica il carrello
        }, 500);
    }
    
    showSuccessMessage('Prodotto rimosso dal carrello');
}

function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(itemId);
        return;
    }
    
    // Qui faresti una chiamata all'API per aggiornare la quantità
    console.log('Aggiornando quantità:', itemId, newQuantity);
    
    // Per ora simula l'aggiornamento
    showSuccessMessage('Quantità aggiornata');
}

function proceedToCheckout() {
    // Implementa la logica di checkout
    alert('Checkout non ancora implementato');
}

function showEmptyCart() {
    const cartContent = document.querySelector('.cart-content');
    cartContent.innerHTML = "";

    const emptyAlert = document.createElement("div");
    emptyAlert.classList.add("cart-empty-alert");
    
    const icon = document.createElement("div");
    icon.style.cssText = `
        font-size: 3rem;
        margin-bottom: 20px;
        color: #6c757d;
    `;
    icon.textContent = "🛒";
    
    const title = document.createElement("h2");
    title.style.cssText = `
        color: #111; 
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
    `;
    title.textContent = "Il tuo carrello è vuoto";
    
    const description = document.createElement("p");
    description.style.cssText = `
        color: #6c757d; 
        font-size: 16px;
        margin-bottom: 30px;
        line-height: 1.5;
    `;
    description.textContent = "I prodotti che aggiungi al carrello appariranno qui";
    
    const shopBtn = document.createElement("a");
    shopBtn.href = "/pages/shop/shop.php";
    shopBtn.className = "btn";
    shopBtn.style.cssText = `
        background: #000; 
        color: white; 
        padding: 12px 24px; 
        text-decoration: none; 
        border-radius: 24px; 
        display: inline-block;
        font-weight: 500;
        transition: background-color 0.3s ease;
    `;
    shopBtn.textContent = "Inizia a fare shopping";
    shopBtn.addEventListener('mouseover', () => {
        shopBtn.style.background = '#333';
    });
    shopBtn.addEventListener('mouseout', () => {
        shopBtn.style.background = '#000';
    });
    
    emptyAlert.appendChild(icon);
    emptyAlert.appendChild(title);
    emptyAlert.appendChild(description);
    emptyAlert.appendChild(shopBtn);
    cartContent.appendChild(emptyAlert);
}

function showError(message) {
    const cartContent = document.querySelector('.cart-content');
    cartContent.innerHTML = `
        <div style="text-align: center; padding: 60px 20px; color: #d32f2f;">
            <h3>Errore</h3>
            <p>${message}</p>
        </div>
    `;
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showMessage(message, type) {
    // Rimuovi messaggi esistenti
    const existingMessages = document.querySelectorAll('.feedback-message');
    existingMessages.forEach(msg => msg.remove());

    const messageElement = document.createElement('div');
    messageElement.className = `feedback-message feedback-${type}`;
    messageElement.textContent = message;

    // Stili per il messaggio
    Object.assign(messageElement.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '12px 20px',
        borderRadius: '4px',
        color: 'white',
        fontWeight: '500',
        zIndex: '1000',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease',
        background: type === 'success' ? '#107C10' : '#d32f2f'
    });

    document.body.appendChild(messageElement);

    // Animazione di entrata
    setTimeout(() => {
        messageElement.style.transform = 'translateX(0)';
    }, 100);

    // Rimozione automatica dopo 3 secondi
    setTimeout(() => {
        messageElement.style.transform = 'translateX(100%)';
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    }, 3000);
}