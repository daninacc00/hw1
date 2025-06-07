loadFavoriteProducts();

function loadFavoriteProducts() {
    fetch("/api/shop/favorites/getFavorites.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProducts(data.data);
            } else {
                console.log(data.error_type)
                if (data.error_type === 'auth_required') {
                    showAuthRequiredState(data.message, data.redirect_url);
                } else {
                    showErrorMessage(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Errore nel caricamento dei prodotti preferiti:', error);
            showErrorMessage('Errore nel caricamento dei prodotti. Riprova più tardi.');
        });
}

function renderProducts(products) {
    const productsGrid = document.querySelector('.products-grid');

    if (!productsGrid) {
        console.error('Container prodotti non trovato');
        return;
    }

    productsGrid.innerHTML = '';

    if (products.length === 0) {
        showEmptyState();
        return;
    }

    products.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.productId = product.id;

    const imageContainer = document.createElement('div');
    imageContainer.className = 'product-image-container';

    const image = document.createElement('img');
    image.className = 'product-image';
    image.src = product.image_url;
    image.alt = product.name;

    const heartButton = document.createElement('button');
    heartButton.className = 'heart-icon';
    heartButton.addEventListener('click', (e) => {
        e.preventDefault();
        handleRemoveFromFavorites(product.id, card);
    });

    const heartImg = document.createElement('img');
    heartImg.src = "/assets/icons/hearth-icon-filled.svg";
    heartImg.alt = 'Rimuovi dai preferiti';

    heartButton.appendChild(heartImg);
    imageContainer.appendChild(image);
    imageContainer.appendChild(heartButton);

    const infoContainer = document.createElement('div');
    infoContainer.className = 'product-info-container';

    const info = document.createElement('div');
    info.className = 'product-info';

    const principalInfo = document.createElement('div');
    principalInfo.className = 'product-principal-info';

    const name = document.createElement('div');
    name.className = 'product-name';
    name.textContent = product.name;

    const categoryDiv = document.createElement('div');
    categoryDiv.className = 'product-category';
    const categoryText = `${product.section_name} - ${product.category_name}`;
    categoryDiv.textContent = categoryText;

    principalInfo.appendChild(name)
    principalInfo.appendChild(categoryDiv)

    const price = document.createElement('div');
    price.className = 'product-price';
    price.textContent = `€${product.price}`

    const cartButton = document.createElement('button');
    cartButton.className = `product-status ${product.isInCart ? 'status-added' : 'status-add-to-cart'}`;
    cartButton.addEventListener('click', (e) => {
        e.preventDefault();
        handleCartToggle(product.id, cartButton, product.isInCart);
    });

    if (product.isInCart) {
        cartButton.innerHTML = '<span class="status-indicator"></span>Aggiunto';
    } else {
        cartButton.textContent = 'Aggiungi al carrello';
    }

    info.appendChild(principalInfo);
    info.appendChild(price);

    infoContainer.appendChild(info);
    infoContainer.appendChild(cartButton);

    card.appendChild(imageContainer);
    card.appendChild(infoContainer);

    return card;
}

function handleRemoveFromFavorites(productId, cardElement) {
    const data = {
        productId: productId
    };

    fetch("/api/shop/favorites/removeFromFavorites.php", {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                cardElement.style.opacity = '0.5';

                setTimeout(() => {
                    loadFavoriteProducts();
                }, 1000);

                updateFavoritesCounter(-1);
            } else {
                if (result.error_type === 'auth_required') {
                    showAuthRequiredState(result.message, result.redirect_url);
                } else {
                    showErrorMessage(result.message || 'Errore durante la rimozione dai preferiti');
                }
            }
        }).catch(error => {
            console.error('Errore nella rimozione dai preferiti:', error);
            showErrorMessage('Errore nella rimozione del prodotto. Riprova.');
        })
}

function handleCartToggle(productId, buttonElement, isCurrentlyInCart) {
    if (isCurrentlyInCart) {
        handleRemoveFromCart(productId, buttonElement);
    } else {
        handleAddToCart(productId, buttonElement);
    }
}

function handleRemoveFromCart(productId, buttonElement) {
    buttonElement.disabled = true;
    buttonElement.style.opacity = '0.6';
    const originalText = buttonElement.innerHTML;

    buttonElement.textContent = 'Rimuovendo...';

    // Rimuoviamo TUTTE le varianti del prodotto dal carrello
    fetch("/api/shop/cart/removeFromCart.php", {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ productId: productId })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                buttonElement.className = 'product-status status-add-to-cart';
                buttonElement.textContent = 'Aggiungi al carrello';
                
                // Aggiorna il counter del carrello diminuendo del numero di varianti rimosse
                // Per ora usiamo -1, ma idealmente dovremmo sapere quante varianti sono state rimosse
                updateCartCounter(-1);
                showSuccessMessage('Prodotto rimosso dal carrello');
                
                // Ricarica i preferiti per aggiornare lo stato
                setTimeout(() => {
                    loadFavoriteProducts();
                }, 500);
            } else {
                if (result.error_type === 'auth_required') {
                    showAuthRequiredState(result.message, result.redirect_url);
                } else {
                    showErrorMessage(result.message || 'Errore durante la rimozione dal carrello');
                buttonElement.innerHTML = originalText;
                }
                
            }
        })
        .catch(error => {
            console.error('Errore nella rimozione dal carrello:', error);
            showErrorMessage('Errore nella rimozione del prodotto. Riprova.');
            buttonElement.innerHTML = originalText;
        })
        .finally(() => {
            buttonElement.disabled = false;
            buttonElement.style.opacity = '1';
        });
}

function handleAddToCart(productId, buttonElement) {
    // Per i prodotti nei preferiti, mostriamo un popup per scegliere colore e taglia
    showAddToCartModal(productId, buttonElement);
}

function showAddToCartModal(productId, buttonElement) {
    // Creiamo un modal semplice per la selezione di colore e taglia
    const modal = document.createElement('div');
    modal.className = 'cart-modal';
    modal.innerHTML = `
        <div class="cart-modal-content">
            <div class="cart-modal-header">
                <h3>Seleziona opzioni</h3>
                <button class="cart-modal-close">&times;</button>
            </div>
            <div class="cart-modal-body">
                <div class="loading">Caricamento opzioni...</div>
            </div>
        </div>
    `;

    // Stili inline per il modal
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
    `;

    const modalContent = modal.querySelector('.cart-modal-content');
    modalContent.style.cssText = `
        background: white;
        border-radius: 8px;
        padding: 20px;
        max-width: 400px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    `;

    document.body.appendChild(modal);

    // Chiudi modal
    modal.querySelector('.cart-modal-close').addEventListener('click', () => {
        document.body.removeChild(modal);
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });

    // Carica i dettagli del prodotto
    fetch(`/api/shop/getProduct.php?id=${productId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                renderCartModal(result.data, modal, buttonElement);
            } else {
                showErrorMessage('Errore nel caricamento delle opzioni prodotto');
                document.body.removeChild(modal);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            showErrorMessage('Errore nel caricamento delle opzioni prodotto');
            document.body.removeChild(modal);
        });
}

function renderCartModal(product, modal, buttonElement) {
    const modalBody = modal.querySelector('.cart-modal-body');
    let selectedColorId = product.colors[0]?.id || null;
    let selectedSizeId = null;

    modalBody.innerHTML = `
        <div class="product-options">
            <h4>${product.name}</h4>
            <p class="product-price">€${product.price}</p>
            
            ${product.colors && product.colors.length > 0 ? `
                <div class="color-selection">
                    <h5>Colore</h5>
                    <div class="color-options">
                        ${product.colors.map((color, index) => `
                            <div class="color-option ${index === 0 ? 'selected' : ''}" 
                                 data-color-id="${color.id}" 
                                 title="${color.name}"
                                 style="background-color: ${color.hex_code}; width: 30px; height: 30px; border-radius: 50%; margin: 5px; cursor: pointer; border: 2px solid ${index === 0 ? '#000' : '#ddd'}; display: inline-block;">
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            ${product.sizes && product.sizes.length > 0 ? `
                <div class="size-selection">
                    <h5>Taglia</h5>
                    <div class="size-options" style="display: flex; flex-wrap: wrap; gap: 5px;">
                        ${product.sizes.map(size => `
                            <button class="size-option" 
                                    data-size-id="${size.id}" 
                                    ${size.stock_quantity <= 0 ? 'disabled' : ''}
                                    style="padding: 8px 12px; border: 1px solid #ddd; background: white; cursor: ${size.stock_quantity <= 0 ? 'not-allowed' : 'pointer'}; border-radius: 4px; ${size.stock_quantity <= 0 ? 'opacity: 0.5;' : ''}">
                                EU ${size.value} ${size.stock_quantity <= 0 ? '(Esaurito)' : ''}
                            </button>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            <button class="add-to-cart-modal-btn" disabled style="width: 100%; padding: 12px; margin-top: 20px; background: #ccc; color: white; border: none; border-radius: 24px; cursor: not-allowed; font-weight: 500;">
                Seleziona una taglia
            </button>
        </div>
    `;

    // Gestione selezione colore
    modalBody.querySelectorAll('.color-option').forEach(colorEl => {
        colorEl.addEventListener('click', () => {
            modalBody.querySelectorAll('.color-option').forEach(el => {
                el.style.border = '2px solid #ddd';
                el.classList.remove('selected');
            });
            colorEl.style.border = '2px solid #000';
            colorEl.classList.add('selected');
            selectedColorId = colorEl.dataset.colorId;
        });
    });

    // Gestione selezione taglia
    modalBody.querySelectorAll('.size-option').forEach(sizeEl => {
        sizeEl.addEventListener('click', () => {
            if (!sizeEl.disabled) {
                modalBody.querySelectorAll('.size-option').forEach(el => {
                    el.style.background = 'white';
                    el.style.color = 'black';
                    el.style.borderColor = '#ddd';
                });
                sizeEl.style.background = '#000';
                sizeEl.style.color = 'white';
                sizeEl.style.borderColor = '#000';
                selectedSizeId = sizeEl.dataset.sizeId;
                
                // Abilita il bottone aggiungi al carrello
                const addBtn = modalBody.querySelector('.add-to-cart-modal-btn');
                addBtn.disabled = false;
                addBtn.style.background = '#000';
                addBtn.style.cursor = 'pointer';
                addBtn.textContent = 'Aggiungi al carrello';
            }
        });
    });

    // Gestione click aggiungi al carrello
    modalBody.querySelector('.add-to-cart-modal-btn').addEventListener('click', () => {
        if (selectedSizeId) {
            addToCartWithOptions(product.id, selectedColorId, selectedSizeId, buttonElement, modal);
        }
    });
}

function addToCartWithOptions(productId, colorId, sizeId, buttonElement, modal) {
    const addBtn = modal.querySelector('.add-to-cart-modal-btn');
    addBtn.disabled = true;
    addBtn.textContent = 'Aggiungendo...';
    addBtn.style.background = '#ccc';

    const data = {
        productId: productId,
        colorId: colorId,
        sizeId: sizeId,
        quantity: 1
    };

    fetch("/api/shop/cart/addToCart.php", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                buttonElement.className = 'product-status status-added';
                buttonElement.innerHTML = '<span class="status-indicator"></span>Aggiunto';
                updateCartCounter(1);
                showSuccessMessage('Prodotto aggiunto al carrello');
                document.body.removeChild(modal);
                
                // Ricarica i preferiti per aggiornare lo stato del prodotto
                setTimeout(() => {
                    loadFavoriteProducts();
                }, 500);
            } else {
                showErrorMessage(result.message || 'Errore durante l\'aggiunta al carrello');
                addBtn.disabled = false;
                addBtn.style.background = '#000';
                addBtn.textContent = 'Aggiungi al carrello';
            }
        })
        .catch(error => {
            console.error('Errore nell\'aggiunta al carrello:', error);
            showErrorMessage('Errore nell\'aggiunta al carrello. Riprova.');
            addBtn.disabled = false;
            addBtn.style.background = '#000';
            addBtn.textContent = 'Aggiungi al carrello';
        });
}

function showEmptyState() {
    const productsGrid = document.querySelector('.products-grid');
    productsGrid.innerHTML = "";

    const emptyAlert = document.createElement("div");
    emptyAlert.classList.add("favorites-empty-alert");
    
    const emptyText = document.createElement("p");
    emptyText.textContent = "Gli articoli aggiunti ai preferiti saranno salvati qui";
    
    const shopBtn = document.createElement("a");
    shopBtn.href = "/pages/shop/shop.php";
    shopBtn.className = "btn";
    shopBtn.style.cssText = "background: #000; color: white; padding: 12px 24px; text-decoration: none; border-radius: 24px; display: inline-block; margin-top: 20px;";
    shopBtn.textContent = "Inizia a fare shopping";
    
    emptyAlert.appendChild(emptyText);
    emptyAlert.appendChild(shopBtn);
    productsGrid.appendChild(emptyAlert);
}

function showAuthRequiredState(message, redirectUrl) {
    const productsGrid = document.querySelector('.products-grid');
    productsGrid.innerHTML = "";

    const authAlert = document.createElement("div");
    authAlert.classList.add("auth-required-alert");
    authAlert.style.cssText = `
        grid-column: 1 / -1; 
        text-align: center; 
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    `;
    
    const icon = document.createElement("div");
    icon.style.cssText = `
        font-size: 3rem;
        margin-bottom: 20px;
        color: #6c757d;
    `;
    icon.textContent = "❤️";
    
    const title = document.createElement("h2");
    title.style.cssText = `
        color: #111; 
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
    `;
    title.textContent = "Accedi per vedere i tuoi preferiti";
    
    const description = document.createElement("p");
    description.style.cssText = `
        color: #6c757d; 
        font-size: 16px;
        margin-bottom: 30px;
        line-height: 1.5;
    `;
    description.textContent = message || "Devi essere loggato per accedere ai tuoi preferiti";
    
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
    shopBtn.style.cssText = "background: #000; color: white; padding: 12px 24px; text-decoration: none; border-radius: 24px; display: inline-block; margin-top: 20px;";
    shopBtn.textContent = "Inizia a fare shopping";
    
    authAlert.appendChild(icon);
    authAlert.appendChild(title);
    authAlert.appendChild(description);
    authAlert.appendChild(loginBtn);
    productsGrid.appendChild(authAlert);
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