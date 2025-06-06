
loadFavoriteProducts();

function loadFavoriteProducts() {
    fetch("/api/shop/favorites/getFavorites.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProducts(data.data);
            } else {
                showError(data.message);
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
    cartButton.className = `product-status ${product.is_in_Cart ? 'status-added' : 'status-add-to-cart'}`;
    cartButton.addEventListener('click', (e) => {
        e.preventDefault();
        handleCartToggle(product.id, cartButton);
    });

    if (product.is_in_Cart) {
        cartButton.textContent = 'Aggiunto';
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
                showErrorMessage(result.message || 'Errore durante la rimozione dai preferiti');
            }
        }).catch(error => {
            console.error('Errore nella rimozione dai preferiti:', error);
            showErrorMessage('Errore nella rimozione del prodotto. Riprova.');
        })
}

function handleRemoveFromCart(productId, buttonElement) {
    buttonElement.disabled = true;
    buttonElement.style.opacity = '0.6';

    fetch("api/shop/cart/removeToCart", {
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
                buttonElement.textContent = 'Aggiungi';
            } else {
                showErrorMessage(result.message || 'Errore durante la rimozione dal carrello');
            }
        })
        .catch(error => {
            console.error('Errore nella rimozione dal carrello:', error);
            showErrorMessage('Errore nella rimozione del prodotto. Riprova.');
        })
        .finally(() => {
            buttonElement.disabled = false;
            buttonElement.style.opacity = '1';
        });
}

function handleAddToCart(productId, buttonElement) {
    buttonElement.disabled = true;
    buttonElement.style.opacity = '0.6';

    fetch("api/shop/cart/addToCart", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ productId: productId })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                buttonElement.className = 'product-status status-added';
                buttonElement.textContent = 'Aggiunto';
            } else {
                showErrorMessage(result.message || 'Errore durante l\'aggiunta nel carrello');
            }
        })
        .catch(error => {
            console.error('Errore nell\'aggiunta nel carrello:', error);
            showErrorMessage('Errore nell\'aggiunta nel carrello. Riprova.');
        })
        .finally(() => {
            buttonElement.disabled = false;
            buttonElement.style.opacity = '1';
        });
}


function showEmptyState() {
    const productsGrid = document.querySelector('.products-grid');
    productsGrid.innerHTML = "";

    const emptyAlert = document.createElement("div");
    emptyAlert.classList.add("favorites-empty-alert");
    emptyAlert.textContent = "Gli articoli aggiunti ai preferiti saranno salvati qui";

    productsGrid.appendChild(emptyAlert)
}

function showErrorMessage(message) {
    showMessage(message, 'error');
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