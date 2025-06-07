-- Tabella categorie principali
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella sezioni (shoes, clothing, etc.)
CREATE TABLE IF NOT EXISTS sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella sport/attività
CREATE TABLE IF NOT EXISTS sports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella colori
CREATE TABLE IF NOT EXISTS colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    hex_code VARCHAR(7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella taglie
CREATE TABLE IF NOT EXISTS sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    value VARCHAR(10) NOT NULL,
    type INT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella prodotti principale
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    discount_percentage INT DEFAULT 0,
    category_id INT NOT NULL,
    section_id INT NOT NULL,
    sport_id INT,
    gender INT NOT NULL, -- ENUM('men', 'women', 'kids', 'unisex')
    shoe_height INT NULL, -- ENUM('low', 'mid', 'high')
    is_bestseller BOOLEAN DEFAULT FALSE,
    is_new_arrival BOOLEAN DEFAULT FALSE,
    is_on_sale BOOLEAN DEFAULT FALSE,
    stock_quantity INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    status INT DEFAULT 0, -- ENUM('active', 'inactive', 'discontinued')
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (sport_id) REFERENCES sports(id),
    INDEX idx_category_gender (category_id, gender),
    INDEX idx_section_gender (section_id, gender),
    INDEX idx_price (price),
    INDEX idx_status (status)
);

-- Tabella immagini prodotto
CREATE TABLE IF NOT EXISTS product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_primary (product_id, is_primary)
);

-- Tabella relazione prodotti-colori
CREATE TABLE IF NOT EXISTS product_colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    color_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (color_id) REFERENCES colors(id),
    UNIQUE KEY unique_product_color (product_id, color_id)
);

-- Tabella relazione prodotti-taglie
CREATE TABLE IF NOT EXISTS product_sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id),
    UNIQUE KEY unique_product_size (product_id, size_id)
);

CREATE TABLE IF NOT EXISTS cart (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    color_id INT DEFAULT NULL,
    size_id INT DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES utenti (id_utente) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favorites (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES utenti (id_utente) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Inserimento dati base

-- Categorie
INSERT INTO categories (name, slug, display_name) VALUES
('men', 'men', 'Uomo'),
('women', 'women', 'Donna'),
('kids', 'kids', 'Bambini');

-- Sezioni
INSERT INTO sections (name, slug, display_name) VALUES
('shoes', 'shoes', 'Sneakers e scarpe'),
('clothing', 'clothing', 'Abbigliamento'),
('accessories', 'accessories', 'Accessori');

-- Sport/Attività
INSERT INTO sports (name, slug, display_name) VALUES
('lifestyle', 'lifestyle', 'Lifestyle'),
('jordan', 'jordan', 'Jordan'),
('running', 'running', 'Running'),
('basketball', 'basketball', 'Basketball'),
('football', 'football', 'Calcio'),
('training', 'training', 'Allenamento e palestra'),
('skateboard', 'skateboard', 'Skateboard'),
('golf', 'golf', 'Golf'),
('tennis', 'tennis', 'Tennis'),
('walking', 'walking', 'Camminata');

-- Colori
INSERT INTO colors (name, hex_code) VALUES
('Nero', '#000000'),
('Bianco', '#FFFFFF'),
('Grigio', '#808080'),
('Rosso', '#FF0000'),
('Blu', '#0000FF'),
('Verde', '#008000'),
('Giallo', '#FFFF00'),
('Arancione', '#FFA500'),
('Rosa', '#FFC0CB'),
('Viola', '#800080'),
('Marrone', '#A52A2A'),
('Beige', '#F5F5DC');

-- Taglie scarpe
INSERT INTO sizes (value, type, sort_order) VALUES
('35', 0, 1), ('35.5', 0, 2), ('36', 0, 3), ('36.5', 0, 4),
('37', 0, 5), ('37.5', 0, 6), ('38', 0, 7), ('38.5', 0, 8),
('39', 0, 9), ('39.5', 0, 10), ('40', 0, 11), ('40.5', 0, 12),
('41', 0, 13), ('41.5', 0, 14), ('42', 0, 15), ('42.5', 0, 16),
('43', 0, 17), ('43.5', 0, 18), ('44', 0, 19), ('44.5', 0, 20),
('45', 0, 21), ('45.5', 0, 22), ('46', 0, 23), ('47', 0, 24),
('47.5', 0, 25), ('48', 0, 26), ('48.5', 0, 27), ('49', 0, 28);

-- Taglie abbigliamento
INSERT INTO sizes (value, type, sort_order) VALUES
('XS', 1, 1), ('S', 1, 2), ('M', 1, 3), 
('L', 1, 4), ('XL', 1, 5), ('XXL', 1, 6), ('XXXL', 1, 7);

-- Prodotti di esempio
INSERT INTO products (name, slug, description, short_description, price, original_price, discount_percentage, category_id, section_id, sport_id, gender, shoe_height, is_bestseller, is_new_arrival, is_on_sale, stock_quantity, rating, rating_count) VALUES
('Nike Air Max 90', 'nike-air-max-90-men', 'Le Nike Air Max 90 presentano un design classico con dettagli moderni e comfort eccezionale per tutto il giorno.', 'Sneakers classiche con ammortizzazione Air Max', 119.99, 139.99, 14, 1, 1, 1, 0, 0, TRUE, FALSE, TRUE, 50, 4.5, 1245),
('Nike Air Force 1', 'nike-air-force-1-men', 'Un classico senza tempo che combina stile e comfort in una silhouette iconica.', 'Sneakers iconiche in pelle bianca', 99.99, NULL, 0, 1, 1, 1, 0, 0, TRUE, FALSE, FALSE, 75, 4.7, 2156),
('Nike Revolution 5', 'nike-revolution-5-men', 'Scarpe da running leggere e traspiranti, perfette per allenamenti quotidiani.', 'Scarpe running per principianti', 64.99, NULL, 0, 1, 1, 3, 0, 0, FALSE, TRUE, FALSE, 30, 4.2, 856),
('Nike Dri-FIT T-Shirt', 'nike-dri-fit-tshirt-men', 'Maglietta tecnica con tecnologia Dri-FIT per mantenere la pelle asciutta durante l\'allenamento.', 'T-shirt tecnica traspirante', 29.99, NULL, 0, 1, 2, 6, 0, NULL, FALSE, FALSE, FALSE, 100, 4.3, 645),
('Nike Sportswear Hoodie', 'nike-sportswear-hoodie-men', 'Felpa con cappuccio in cotone morbido, perfetta per il tempo libero.', 'Felpa casual con cappuccio', 64.99, 79.99, 19, 1, 2, 1, 0, NULL, FALSE, TRUE, TRUE, 25, 4.4, 423);

-- Associazioni colori prodotti
INSERT INTO product_colors (product_id, color_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5),  -- Air Max 90: 5 colori
(2, 2), (2, 1), (2, 4),  -- Air Force 1: 3 colori
(3, 1), (3, 2), (3, 5), (3, 6),  -- Revolution 5: 4 colori
(4, 1), (4, 2), (4, 4), (4, 5), (4, 6), (4, 7),  -- T-shirt: 6 colori
(5, 1), (5, 3), (5, 5);  -- Hoodie: 3 colori

-- Associazioni taglie prodotti (scarpe)
INSERT INTO product_sizes (product_id, size_id, stock_quantity) VALUES
-- Air Max 90
(1, 11, 5), (1, 13, 8), (1, 15, 12), (1, 17, 10), (1, 19, 7), (1, 21, 8),
-- Air Force 1
(2, 11, 10), (2, 13, 15), (2, 15, 20), (2, 17, 18), (2, 19, 12), (2, 21, 0),
-- Revolution 5
(3, 11, 3), (3, 13, 8), (3, 15, 10), (3, 17, 5), (3, 19, 4);

-- Associazioni taglie abbigliamento
INSERT INTO product_sizes (product_id, size_id, stock_quantity) VALUES
-- T-shirt
(4, 29, 15), (4, 30, 25), (4, 31, 30), (4, 32, 20), (4, 33, 10),
-- Hoodie
(5, 29, 5), (5, 30, 8), (5, 31, 12), (5, 32, 7), (5, 33, 3);

-- Immagini prodotti
INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) VALUES
(1, '/assets/products/air-max-90-1.jpg', 'Nike Air Max 90 - Vista principale', TRUE, 1),
(1, '/assets/products/air-max-90-2.jpg', 'Nike Air Max 90 - Vista laterale', FALSE, 2),
(1, '/assets/products/air-max-90-3.jpg', 'Nike Air Max 90 - Vista posteriore', FALSE, 3),
(2, '/assets/products/air-force-1-1.jpg', 'Nike Air Force 1 - Vista principale', TRUE, 1),
(2, '/assets/products/air-force-1-2.jpg', 'Nike Air Force 1 - Vista laterale', FALSE, 2),
(3, '/assets/products/revolution-5-1.jpg', 'Nike Revolution 5 - Vista principale', TRUE, 1),
(4, '/assets/products/dri-fit-tshirt-1.jpg', 'Nike Dri-FIT T-Shirt', TRUE, 1),
(5, '/assets/products/hoodie-1.jpg', 'Nike Sportswear Hoodie', TRUE, 1);