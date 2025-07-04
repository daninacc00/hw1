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
    const search = window.location.search;
    if (!search) return null;

    const query = search.substring(1);
    const pairs = query.split('&');

    for (const pair of pairs) {
        const [key, value] = pair.split('=');
        if (key === 'id' && value) {
            return decodeURIComponent(value);
        }
    }

    return null;
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

    container.appendChild(productImages);
    container.appendChild(productInfo);
    container.style.display = 'grid';

    document.title = product.name + ' - Nuovi arrivi';
    checkAddToCartButton();
}

function renderThumbnails(images) {
    const thumbnails = document.createElement("div");

    if (!images || images.length === 0) return fragment;

    images.forEach((image, index) => {
        const thumbDiv = document.createElement('div');
        thumbDiv.className = 'thumbnail' + (index === 0 ? ' active' : '');
        thumbDiv.addEventListener("click", () => changeMainImage(image.image_url, thumbDiv));

        const img = document.createElement('img');
        img.src = image.image_url;
        img.alt = image.alt_text;

        thumbDiv.appendChild(img);
        thumbnails.appendChild(thumbDiv);
    });

    return thumbnails;
}


function renderRating(product) {
    if (!product.rating || product.rating <= 0)
        return document.createElement("div");

    const container = document.createElement('div');
    container.className = 'rating';

    const starsDiv = document.createElement('div');
    starsDiv.className = 'stars';

    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('i');
        if (i <= product.rating) {
            star.className = 'fas fa-star';
        } else if (i - 0.5 <= product.rating) {
            star.className = 'fas fa-star-half-alt';
        } else {
            star.className = 'far fa-star';
        }
        starsDiv.appendChild(star);
    }

    const ratingText = document.createElement('span');
    ratingText.className = 'rating-text';
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
    if (!colors || colors.length === 0)
        return document.createElement('div');

    const wrapper = document.createElement('div');
    wrapper.className = 'colors';

    const title = document.createElement('h3');
    title.textContent = 'Colori disponibili';

    const options = document.createElement('div');
    options.className = 'color-options';

    colors.forEach((color, index) => {
        const div = document.createElement('div');
        div.className = 'color-option' + (index === 0 ? ' selected' : '');
        div.style.backgroundColor = color.hex_code;
        div.dataset.colorId = color.id;
        div.title = color.name;
        div.addEventListener("click", () => selectColor(div));
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
    header.className = 'sizes-header';

    const title = document.createElement('h3');
    title.className = 'sizes-title';
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
        div.className = 'size-option';
        if (size.stock_quantity == 0) {
            div.classList.add('out-of-stock');
        }
        div.dataset.sizeId = size.id;
        div.dataset.stock = size.stock_quantity;
        div.textContent = 'EU ' + size.value;
        div.addEventListener('click', () => selectSize(div));
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
    const text = product.description;
    p.innerHTML = text;

    div.appendChild(p);

    return div;
}

function renderAdditionalInfo(product) {
    const div = document.createElement('div');
    div.className = 'additional-info';

    if (product.sport_name) {
        const sport = document.createElement('p');
        sport.className = 'info-item';

        const sportLabel = document.createElement('strong');
        sportLabel.textContent = 'Categoria Sport: ';

        const sportValue = document.createElement('span');
        sportValue.textContent = product.sport_name;

        sport.appendChild(sportLabel);
        sport.appendChild(sportValue);
        div.appendChild(sport);
    }

    if (product.shoe_height && shoeHeightLabels[product.shoe_height]) {
        const height = document.createElement('p');
        height.className = 'info-item';

        const heightLabel = document.createElement('strong');
        heightLabel.textContent = 'Altezza: ';

        const heightValue = document.createElement('span');
        heightValue.textContent = shoeHeightLabels[product.shoe_height];

        height.appendChild(heightLabel);
        height.appendChild(heightValue);
        div.appendChild(height);
    }

    return div;
}

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

function addToCart() {
    if (!selectedSizeId) {
        showErrorMessage('Seleziona una taglia prima di aggiungere al carrello');
        return;
    }

    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const originalText = addToCartBtn.textContent;

    addToCartBtn.disabled = true;
    addToCartBtn.textContent = 'Aggiungendo...';

    const formData = new FormData();
    formData.append('productId', productId);
    formData.append('colorId', selectedColorId);
    formData.append('sizeId', selectedSizeId);
    formData.append('quantity', 1);

    fetch('/api/shop/cart/addToCart.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateCartCounter(1);
                showNotificationPopup(
                    'success',
                    'Aggiunto al carrello',
                    'Il prodotto è stato aggiunto al tuo carrello.',
                    [
                        { text: 'Continua shopping', url: '#', primary: false },
                        { text: 'Vai al carrello', url: '/pages/shop/cart/cart.php', primary: true }
                    ]
                );
            } else {
               if (result.error_type === 'auth_required') {
                    window.location.href = "/pages/login/login.php"
                } else {
                    showErrorMessage(result.message || 'Errore durante l\'aggiunta al carrello');
                }
            }
        })
        .catch(error => {
            console.error('Errore nella richiesta:', error);
            showErrorMessage('Errore di connessione. Riprova più tardi.');
        })
        .finally(function () {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = originalText;
            checkAddToCartButton();
        });
}

function updateFavoriteButton() {
    const favBtn = document.getElementById('add-to-favorites-btn');
    if (!favBtn) return;

    favBtn.removeEventListener('click', addFavorite);
    favBtn.removeEventListener('click', removeFavorite);

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

function addFavorite() {
    const favBtn = document.getElementById('add-to-favorites-btn');
    const originalText = favBtn.textContent;

    favBtn.disabled = true;
    favBtn.textContent = 'Aggiungendo...';

    const formData = new FormData();
    formData.append('productId', productId);

    fetch('/api/shop/favorites/addToFavorites.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                isFavorite = true;
                updateFavoriteButton();
                showNotificationPopup(
                    'success',
                    'Aggiunto ai preferiti',
                    'Il prodotto è stato aggiunto ai tuoi preferiti.',
                    [
                        { text: 'Vai ai preferiti', url: '/pages/shop/favorites/favorites.php', primary: true }
                    ]
                );
                updateFavoritesCounter(1);
            } else {
                if (result.error_type === 'auth_required') {
                    window.location.href = "/pages/login/login.php"
                } else {
                    showErrorMessage(result.message || 'Errore durante l\'aggiunta ai preferiti');
                }
            }
        })
        .catch(error => {
            console.error('Errore nella richiesta:', error);
            showErrorMessage('Errore di connessione. Riprova più tardi.');
        })
        .finally(function () {
            favBtn.disabled = false;
            if (!isFavorite) {
                favBtn.textContent = originalText;
            }
        });
}

function removeFavorite() {
    const favBtn = document.getElementById('add-to-favorites-btn');
    const originalText = favBtn.textContent;

    favBtn.disabled = true;
    favBtn.textContent = 'Rimuovendo...';

    const formData = new FormData();
    formData.append('productId', productId);

    fetch('/api/shop/favorites/removeFromFavorites.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
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
                if (result.error_type === 'auth_required') {
                    window.location.href = "/pages/login/login.php"
                } else {
                    showErrorMessage(result.message || 'Errore durante la rimozione dai preferiti');
                }
            }
        })
        .catch(error => {
            console.error('Errore nella richiesta:', error);
            showErrorMessage('Errore di connessione. Riprova più tardi.');
        })
        .finally(function () {
            favBtn.disabled = false;
            if (isFavorite) {
                favBtn.textContent = originalText;
            }
        });
}

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

        setTimeout(function () {
            hideErrorMessage();
        }, 5000);
    }
}

function hideErrorMessage() {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) errorDiv.style.display = 'none';
}

function formatPrice(price) {
    const number = parseFloat(price);

    if (isNaN(number)) return "";

    return '€' + number.toFixed(2).replace('.', ',');
}

