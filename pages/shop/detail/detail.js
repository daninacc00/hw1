let selectedColorId = null;
let selectedSizeId = null;
let productId = null;
let productData = null;
let isFavorite = false;

const shoeHeightLabels = {
    'low': 'Basse',
    'mid': 'Medie',
    'high': 'Alte'
};

function getProductIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

loadProduct();

function loadProduct() {
    showLoading();
    hideError();

    productId = getProductIdFromUrl();

    fetch(`/api/shop/getProduct.php?id=${productId}`)
        .then(response => response.json())
        .then(result => {
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Prodotto non trovato');
            }

            productData = result.data;
            isFavorite = result.data.is_favorite;

            renderProduct(result.data);
            updateFavoriteButton();

            hideLoading();
        })
        .catch(error => {
            console.error('Errore nel caricamento del prodotto:', error);
            showError(error.message);
            hideLoading();
        });
}

function renderProduct(product) {
    const container = document.getElementById('product-detail');
    container.innerHTML = '';

    if (product.colors && product.colors.length > 0) {
        selectedColorId = product.colors[0].id;
    }

    const productImages = document.createElement('div');
    productImages.className = 'product-images';

    const thumbnails = document.createElement('div');
    thumbnails.className = 'thumbnails';
    thumbnails.appendChild(renderThumbnails(product.images));

    const mainImage = document.createElement('div');
    mainImage.className = 'main-image';

    const img = document.createElement('img');
    img.id = 'main-product-image';
    img.src = product.images[0].image_url;
    img.alt = product.name;
    mainImage.appendChild(img);

    productImages.appendChild(thumbnails);
    productImages.appendChild(mainImage);

    const productInfo = document.createElement('div');
    productInfo.className = 'product-info';

    const title = document.createElement('h1');
    title.textContent = product.name;

    const categoryDiv = document.createElement('div');
    categoryDiv.className = 'product-category';
    const categoryText = `${product.section_name} - ${product.category_name}`;
    categoryDiv.textContent = categoryText;

    const addToCartBtn = document.createElement('button');
    addToCartBtn.className = 'btn add-to-cart';
    addToCartBtn.id = 'add-to-cart-btn';
    addToCartBtn.textContent = 'Aggiungi al carrello';
    addToCartBtn.disabled = true;
    addToCartBtn.addEventListener("click", addToCart);

    const addToFavBtn = document.createElement('button');
    addToFavBtn.className = 'btn add-to-favorites';
    addToFavBtn.id = 'add-to-favorites-btn';
    addToFavBtn.textContent = 'Aggiungi ai preferiti';

    const errorMsg = document.createElement('div');
    errorMsg.id = 'error-message';
    errorMsg.className = 'error-message';
    errorMsg.style.display = 'none';

    const successMsg = document.createElement('div');
    successMsg.id = 'success-message';
    successMsg.className = 'success-message';
    successMsg.style.display = 'none';

    // Componi product-info
    productInfo.appendChild(title);
    productInfo.appendChild(categoryDiv);
    productInfo.appendChild(renderRating(product));
    productInfo.appendChild(renderPrice(product));
    productInfo.appendChild(renderColors(product.colors));
    productInfo.appendChild(renderSizes(product.sizes));
    productInfo.appendChild(addToCartBtn);
    productInfo.appendChild(addToFavBtn);
    productInfo.appendChild(errorMsg);
    productInfo.appendChild(successMsg);
    productInfo.appendChild(renderDescription(product));
    productInfo.appendChild(renderAdditionalInfo(product));

    // Aggiungi tutto al container
    container.appendChild(productImages);
    container.appendChild(productInfo);
    container.style.display = 'grid';

    // Aggiorna il titolo della pagina
    document.title = product.name + ' - Nuovi arrivi';
    checkAddToCartButton();
}

// Funzioni di rendering dei componenti
function renderThumbnails(images) {
    const fragment = document.createDocumentFragment();

    if (!images || images.length === 0) return fragment;

    images.forEach((image, index) => {
        const thumbDiv = document.createElement('div');
        thumbDiv.className = 'thumbnail' + (index === 0 ? ' active' : '');
        thumbDiv.onclick = () => changeMainImage(image.image_url, thumbDiv);

        const img = document.createElement('img');
        img.src = escapeHtml(image.image_url);
        img.alt = escapeHtml(image.alt_text || '');

        thumbDiv.appendChild(img);
        fragment.appendChild(thumbDiv);
    });

    return fragment;
}


function renderRating(product) {
    if (!product.rating || product.rating <= 0) return document.createDocumentFragment();

    const container = document.createElement('div');
    container.className = 'rating';

    const starsDiv = document.createElement('div');
    starsDiv.className = 'stars';

    const stars = Array.from({ length: 5 }, (_, i) =>
        i < product.rating ? '★' : '☆'
    ).join('');
    starsDiv.textContent = stars;

    const ratingText = document.createElement('span');
    ratingText.textContent = `${product.rating} (${product.rating_count || 0} recensioni)`;

    container.appendChild(starsDiv);
    container.appendChild(ratingText);

    return container;
}

function renderPrice(product) {
    const div = document.createElement('div');
    div.className = 'price';

    const current = document.createElement('span');
    current.className = 'current-price';
    current.textContent = formatPrice(product.price);
    div.appendChild(current);

    if (product.original_price && product.original_price > product.price) {
        const original = document.createElement('span');
        original.className = 'original-price';
        original.textContent = formatPrice(product.original_price);

        const discount = document.createElement('span');
        discount.className = 'discount';
        discount.textContent = `-${product.discount_percentage || 0}%`;

        div.appendChild(original);
        div.appendChild(discount);
    }

    return div;
}

function renderColors(colors) {
    if (!colors || colors.length === 0) return document.createDocumentFragment();

    const wrapper = document.createElement('div');
    wrapper.className = 'colors';

    const title = document.createElement('h3');
    title.textContent = 'Colori disponibili';

    const options = document.createElement('div');
    options.className = 'color-options';

    colors.forEach((color, index) => {
        const div = document.createElement('div');
        div.className = 'color-option' + (index === 0 ? ' selected' : '');
        div.style.backgroundColor = escapeHtml(color.hex_code);
        div.dataset.colorId = color.id;
        div.title = escapeHtml(color.name);
        div.onclick = () => selectColor(div);
        options.appendChild(div);
    });

    wrapper.appendChild(title);
    wrapper.appendChild(options);

    return wrapper;
}

function renderSizes(sizes) {
    if (!sizes || sizes.length === 0)
        return document.createDocumentFragment();

    const wrapper = document.createElement('div');
    wrapper.className = 'sizes';

    const header = document.createElement('div');
    header.style.display = 'flex';
    header.style.justifyContent = 'space-between';
    header.style.alignItems = 'center';
    header.style.marginBottom = '15px';

    const title = document.createElement('h3');
    title.textContent = 'Seleziona la taglia/misura';

    const guide = document.createElement('a');
    guide.className = 'size-guide';
    guide.textContent = 'Guida alle taglie e alle misure';
    guide.href = "https://www.nike.com/it/size-fit/scarpe-kids";

    header.appendChild(title);
    header.appendChild(guide);

    const options = document.createElement('div');
    options.className = 'size-options';

    sizes.forEach(size => {
        const div = document.createElement('div');
        div.className = 'size-option' + (size.stock_quantity == 0 ? ' out-of-stock' : '');
        div.dataset.sizeId = size.id;
        div.dataset.stock = size.stock_quantity;
        div.textContent = 'EU ' + escapeHtml(size.value);
        div.onclick = () => selectSize(div);
        options.appendChild(div);
    });

    wrapper.appendChild(header);
    wrapper.appendChild(options);

    return wrapper;
}

function renderDescription(product) {
    if (!product.description) return document.createDocumentFragment();

    const div = document.createElement('div');
    div.className = 'product-description';

    const p = document.createElement('p');
    const text = escapeHtml(product.description).replace(/\n/g, '<br>');
    p.innerHTML = text;

    div.appendChild(p);

    return div;
}

function renderAdditionalInfo(product) {
    const div = document.createElement('div');
    div.className = 'additional-info';

    if (product.sport_name) {
        const sport = document.createElement('p');
        sport.style.marginTop = '15px';
        sport.innerHTML = `<strong>Categoria Sport:</strong> ${escapeHtml(product.sport_name)}`;
        div.appendChild(sport);
    }

    if (product.shoe_height && shoeHeightLabels[product.shoe_height]) {
        const height = document.createElement('p');
        height.style.marginTop = '10px';
        height.innerHTML = `<strong>Altezza:</strong> ${shoeHeightLabels[product.shoe_height]}`;
        div.appendChild(height);
    }

    return div;
}

// Funzioni di interazione
function changeMainImage(imageUrl, thumbnailElement) {
    document.getElementById('main-product-image').src = imageUrl;

    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });

    thumbnailElement.classList.add('active');
}

function selectColor(colorElement) {
    document.querySelectorAll('.color-option').forEach(color => {
        color.classList.remove('selected');
    });

    colorElement.classList.add('selected');
    selectedColorId = colorElement.dataset.colorId;
    checkAddToCartButton();
}

function selectSize(sizeElement) {
    if (sizeElement.classList.contains('out-of-stock')) {
        showErrorMessage('Taglia non disponibile');
        return;
    }

    document.querySelectorAll('.size-option').forEach(size => {
        size.classList.remove('selected');
    });

    sizeElement.classList.add('selected');
    selectedSizeId = sizeElement.dataset.sizeId;

    hideErrorMessage();
    checkAddToCartButton();
}

function checkAddToCartButton() {
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    if (selectedSizeId) {
        addToCartBtn.disabled = false;
        addToCartBtn.textContent = 'Aggiungi al carrello';
    } else {
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Seleziona una taglia';
    }
}

async function addToCart() {
    if (!selectedSizeId) {
        showErrorMessage('Seleziona una taglia prima di aggiungere al carrello');
        return;
    }

    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const originalText = addToCartBtn.textContent;

    // Disabilita il pulsante e mostra loading
    addToCartBtn.disabled = true;
    addToCartBtn.textContent = 'Aggiungendo...';

    const data = {
        productId: productId,
        colorId: selectedColorId,
        sizeId: selectedSizeId,
        quantity: 1
    };

    try {
        const response = await fetch('/api/shop/cart/addToCart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            updateCartCounter(data.quantity);
            showNotificationPopup(
                'success',
                'Aggiunto al carrello',
                'Il prodotto è stato aggiunto al tuo carrello.',
                [
                    { text: 'Continua shopping', url: '#', primary: false },
                    { text: 'Vai al carrello', url: '/cart', primary: true }
                ]
            );
        } else {
            showErrorMessage(result.message || 'Errore durante l\'aggiunta al carrello');
        }
    } catch (error) {
        console.error('Errore nella richiesta:', error);
        showErrorMessage('Errore di connessione. Riprova più tardi.');
    } finally {
        // Riabilita il pulsante
        addToCartBtn.disabled = false;
        addToCartBtn.textContent = originalText;
        checkAddToCartButton(); // Ricontrolla lo stato del pulsante
    }
}

function updateFavoriteButton() {
    const favBtn = document.getElementById('add-to-favorites-btn');
    if (!favBtn) return;

    favBtn.removeEventListener('click', addFavorite);
    favBtn.removeEventListener('click', removeFavorite);

    // Aggiorna il testo e la classe
    if (isFavorite) {
        favBtn.textContent = 'Rimuovi dai preferiti';
        favBtn.classList.add('in-favorites');
        favBtn.addEventListener("click", removeFavorite);
    } else {
        favBtn.textContent = 'Aggiungi ai preferiti';
        favBtn.classList.remove('in-favorites');
        favBtn.addEventListener("click", addFavorite);
    }
}

async function addFavorite() {
    const favBtn = document.getElementById('add-to-favorites-btn');
    const originalText = favBtn.textContent;

    favBtn.disabled = true;
    favBtn.textContent = 'Aggiungendo...';

    try {
        const response = await fetch('/api/shop/favorites/addToFavorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ productId })
        });

        const result = await response.json();

        if (result.success) {
            isFavorite = true;
            updateFavoriteButton();
            showNotificationPopup(
                'success',
                'Aggiunto ai preferiti',
                'Il prodotto è stato aggiunto ai tuoi preferiti.',
                [
                    { text: 'Vai ai preferiti', url: '/favorites', primary: true }
                ]
            );
            updateFavoritesCounter(1);
        } else {
            showErrorMessage(result.message || 'Errore durante l\'aggiunta ai preferiti');
        }
    } catch (error) {
        console.error('Errore nella richiesta:', error);
        showErrorMessage('Errore di connessione. Riprova più tardi.');
    } finally {
        favBtn.disabled = false;
        if (!isFavorite) {
            favBtn.textContent = originalText;
        }
    }
}

async function removeFavorite() {
    const favBtn = document.getElementById('add-to-favorites-btn');
    const originalText = favBtn.textContent;

    favBtn.disabled = true;
    favBtn.textContent = 'Rimuovendo...';

    try {
        const response = await fetch('/api/shop/favorites/removeFromFavorites.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ productId })
        });

        const result = await response.json();

        if (result.success) {
            isFavorite = false;
            updateFavoriteButton();
            updateFavoritesCounter(-1);
            showNotificationPopup(
                'success',
                'Rimosso dai preferiti',
                'Il prodotto è stato rimosso dai tuoi preferiti.'
            );
        } else {
            showErrorMessage(result.message || 'Errore durante la rimozione dai preferiti');
        }
    } catch (error) {
        console.error('Errore nella richiesta:', error);
        showErrorMessage('Errore di connessione. Riprova più tardi.');
    } finally {
        favBtn.disabled = false;
        if (isFavorite) {
            favBtn.textContent = originalText;
        }
    }
}

// Funzioni di utilità
function showLoading() {
    const loading = document.getElementById('loading');
    const productDetail = document.getElementById('product-detail');
    const error = document.getElementById('error');

    if (loading) loading.style.display = 'block';
    if (productDetail) productDetail.style.display = 'none';
    if (error) error.style.display = 'none';
}

function hideLoading() {
    const loading = document.getElementById('loading');
    if (loading) loading.style.display = 'none';
}

function showError(message) {
    const errorDiv = document.getElementById('error');
    const errorMessage = document.getElementById('error-message');
    const productDetail = document.getElementById('product-detail');

    if (errorMessage) errorMessage.textContent = message;
    if (errorDiv) errorDiv.style.display = 'block';
    if (productDetail) productDetail.style.display = 'none';
}

function hideError() {
    const errorDiv = document.getElementById('error');
    if (errorDiv) errorDiv.style.display = 'none';
}

function showErrorMessage(message) {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';

        // Nascondi automaticamente dopo 5 secondi
        setTimeout(() => {
            hideErrorMessage();
        }, 5000);
    }
}

function hideErrorMessage() {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) errorDiv.style.display = 'none';
}

function formatPrice(price) {
    return new Intl.NumberFormat('it-IT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}