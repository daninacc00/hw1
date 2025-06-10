
CREATE TABLE IF NOT EXISTS users (
	id INT(11) NOT NULL AUTO_INCREMENT,
	username VARCHAR(50) NOT NULL ,
	email VARCHAR(100) NOT NULL ,
	password_hash VARCHAR(255) NOT NULL ,
	first_name VARCHAR(50) NOT NULL ,
	last_name VARCHAR(50) NOT NULL ,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	last_login TIMESTAMP NULL DEFAULT NULL,
	account_status INT(11) NULL DEFAULT 0,
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    hex_code VARCHAR(7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    value VARCHAR(10) NOT NULL,
    type INT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    gender INT NOT NULL, 
    shoe_height INT NULL,
    is_bestseller BOOLEAN DEFAULT FALSE,
    is_new_arrival BOOLEAN DEFAULT FALSE,
    is_on_sale BOOLEAN DEFAULT FALSE,
    stock_quantity INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    status INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (sport_id) REFERENCES sports(id)
);

CREATE TABLE IF NOT EXISTS product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    color_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (color_id) REFERENCES colors(id)
);

CREATE TABLE IF NOT EXISTS product_sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id)
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
    FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favorites (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS interest_categories (
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL,
	value VARCHAR(100) NULL DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS interests (
	id INT(11) NOT NULL AUTO_INCREMENT,
	category_id INT(11) NOT NULL,
	name VARCHAR(100) NOT NULL,
	description VARCHAR(255) NULL DEFAULT NULL,
	value VARCHAR(50) NULL DEFAULT NULL,
	image_url VARCHAR(255) NULL DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (id),
	FOREIGN KEY (category_id) REFERENCES interest_categories (id) ON UPDATE RESTRICT ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS slider_images (
	id INT(11) NOT NULL AUTO_INCREMENT,
	src VARCHAR(255) NOT NULL ,
	alt_text VARCHAR(255) NOT NULL ,
	name VARCHAR(100) NOT NULL ,
	is_free_shipping INT NULL DEFAULT 0,
	is_active INT NULL DEFAULT 1,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS user_interests (
	user_id INT(11) NOT NULL,
	interest_id INT(11) NOT NULL,
	added_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (user_id, interest_id) USING BTREE,
	FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE CASCADE,
	FOREIGN KEY (interest_id) REFERENCES interests (id) ON UPDATE RESTRICT ON DELETE CASCADE
);




INSERT INTO categories (name, slug, display_name) VALUES
('men', 'men', 'Uomo'),
('women', 'women', 'Donna'),
('kids', 'kids', 'Bambini');

INSERT INTO sections (name, slug, display_name) VALUES
('shoes', 'shoes', 'Sneakers e scarpe'),
('clothing', 'clothing', 'Abbigliamento'),
('accessories', 'accessories', 'Accessori');

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

INSERT INTO sizes (value, type, sort_order) VALUES
('35', 0, 1), ('35.5', 0, 2), ('36', 0, 3), ('36.5', 0, 4),
('37', 0, 5), ('37.5', 0, 6), ('38', 0, 7), ('38.5', 0, 8),
('39', 0, 9), ('39.5', 0, 10), ('40', 0, 11), ('40.5', 0, 12),
('41', 0, 13), ('41.5', 0, 14), ('42', 0, 15), ('42.5', 0, 16),
('43', 0, 17), ('43.5', 0, 18), ('44', 0, 19), ('44.5', 0, 20),
('45', 0, 21), ('45.5', 0, 22), ('46', 0, 23), ('47', 0, 24),
('47.5', 0, 25), ('48', 0, 26), ('48.5', 0, 27), ('49', 0, 28);

INSERT INTO sizes (value, type, sort_order) VALUES
('XS', 1, 1), ('S', 1, 2), ('M', 1, 3), 
('L', 1, 4), ('XL', 1, 5), ('XXL', 1, 6), ('XXXL', 1, 7);

INSERT INTO products (id, name, slug, description, short_description, price, original_price, discount_percentage, category_id, section_id, sport_id, gender, shoe_height, is_bestseller, is_new_arrival, is_on_sale, stock_quantity, rating, rating_count, status, created_at, updated_at) VALUES
(1, 'Air Jordan 1 Retro Low Quai 54', 'air-jordan-1-retro-low-quai-54', 'Cercando la giusta ispirazione per creare la collezione di quest\'anno, il nostro team di design voleva qualcosa che incarnasse competizione, community e creatività, proprio come il torneo Quai 54. La pista ci ha dato la risposta che volevamo. Ispirata agli sport motoristici francesi, questa AJ1 rétro mescola i classici colori delle gare su circuito con l\'iconico stile Jordan.', 'Sneakers classiche stile air jordan', 139.99, NULL, 0, 1, 1, 1, 0, 0, 1, 0, 0, 50, 4.50, 1245, 0, '2025-06-02 13:21:55', '2025-06-10 22:14:47'),
(2, 'Luka .77 Quai 54', 'luka-.77-quai-54', 'Pensata per essere inarrestabile come il gioco di Luka, questa Luka .77 celebra il torneo Quai 54 con una scarpa realizzata per resistere sui campi outdoor accidentati dove i giocatori costruiscono la loro leggenda. Il mesh antiabrasione e il battistrada in gomma a tutta lunghezza sono a prova di cemento e asfalto. L\'unità Air Zoom e la schiuma reattiva a doppia densità offrono un comfort ideale su qualsiasi campo. Il più grande torneo di streetball del mondo ti sta aspettando. Cosa pensi di fare?', 'Sneakers con suola ammortizzata', 99.99, 129.00, 22, 1, 1, 1, 0, 0, 1, 0, 0, 75, 4.70, 2156, 0, '2025-06-02 13:21:55', '2025-06-10 22:17:01'),
(3, 'Nike Pegasus Premium', 'nike-pegasus-premium', 'Pegasus Premium potenzia l\'ammortizzazione reattiva con un triplo strato delle nostre più potenti tecnologie per il running: schiuma ZoomX, unità Air Zoom sagomata e schiuma ReactX. La Pegasus più reattiva di sempre offre un ritorno di energia senza precedenti. Con una tomaia più leggera dell\'aria, riduce il peso e aumenta la traspirabilità per farti volare ancora più velocemente.', 'Scarpe running con suola ammortizzata', 209.00, NULL, 0, 1, 1, 1, 0, 0, 0, 1, 0, 30, 4.20, 856, 0, '2025-06-02 13:21:55', '2025-06-10 22:21:21'),
(4, 'Air Jordan 1 Retro Low', 'air-jordan-1-retro-low', 'Effetto ghiaccio e menta. Con una tomaia in suede tono su tono, AJ Low "Washed Teal" dona una ventata di freschezza alla silhouette originale dell\'85. Fedeli alla tradizione, il brand Nike Air e il logo Wings esclusivo impreziosiscono rispettivamente la linguetta e il tallone. Una suola tinta Igloo dona il tocco finale al look.', 'Sneaker classica ghiaccio e menta', 159.00, NULL, 0, 1, 1, 1, 0, NULL, 0, 0, 0, 100, 4.30, 645, 0, '2025-06-02 13:21:55', '2025-06-10 22:21:21'),
(5, 'Nike Air Max 95 Recraft', 'nike-air-max-95-recraft', 'Questa Nike Air Max 95 Recraft riporta alla luce un tesoro del passato per celebrare il 30° anniversario dell\'originale. Abbiamo ovviamente mantenuto l\'ammortizzazione Max Air, la resistente pelle scamosciata e le iconiche linee di design che fecero girare la testa a molti nel 1995 per regalare un classico istantaneo al tuo piccolo.', 'Sneaker bambino', 99.00, 109.00, 19, 1, 1, 1, 0, NULL, 0, 1, 1, 25, 4.40, 423, 0, '2025-06-02 13:21:55', '2025-06-10 22:21:17');

INSERT INTO interest_categories (id, name, value, created_at) VALUES
(1, 'Sport', 'sport', '2025-05-18 22:29:16'),
(2, 'Articoli', 'articles', '2025-05-18 22:29:16'),
(3, 'Squadre', 'teams', '2025-05-18 22:29:16'),
(4, 'Atleti', 'athletes', '2025-05-18 22:29:16'),
(5, 'Città', 'cities', '2025-05-18 22:29:16');

INSERT INTO interests (id, category_id, name, description, value, image_url, created_at) VALUES
(1, 1, 'Calcio', 'Sport più popolare al mondo', NULL, '/assets/images/interests/calcio.jpg', '2025-05-18 22:29:16'),
(2, 1, 'Basket', 'Pallacanestro', NULL, '/assets/images/interests/basket.jpg', '2025-05-18 22:29:16'),
(3, 1, 'Tennis', 'Sport con racchetta', NULL, '/assets/images/interests/tennis.jpg', '2025-05-18 22:29:16'),
(4, 1, 'Nuoto', 'Sport acquatico', NULL, '/assets/images/interests/nuoto.avif', '2025-05-18 22:29:16'),
(5, 1, 'Atletica', 'Sport di corsa e salti', NULL, '/assets/images/interests/atletica.jpg', '2025-05-18 22:29:16'),
(10, 3, 'Atletico Madrid', 'Squadra di calcio', NULL, '/assets/images/interests/atletico-madrid.jpg', '2025-05-18 22:29:16'),
(11, 3, 'Chelsea', 'Squadra di calcio', NULL, '/assets/images/interests/chelsea.jpg', '2025-05-18 22:29:16'),
(12, 3, 'FC Barcelona', 'Squadra di calcio', NULL, '/assets/images/interests/fc-barcelona.jpg', '2025-05-18 22:29:16'),
(13, 3, 'Inter', 'Squadra di calcio', NULL, '/assets/images/interests/inter.jpg', '2025-05-18 22:29:16'),
(14, 4, 'Cristiano Ronaldo', 'Calciatore portoghese', NULL, '/assets/images/interests/cristiano-ronaldo.jpg', '2025-05-18 22:29:16'),
(15, 4, 'Kylian Mbappé', 'Calciatore francese', NULL, '/assets/images/interests/kylian-mbappé.jpg', '2025-05-18 22:29:16'),
(16, 4, 'Erling Haaland', 'Calciatore', NULL, '/assets/images/interests/erling-haaland.avif', '2025-05-18 22:29:16'),
(17, 4, 'Vini Jr.', 'Calciatore', NULL, '/assets/images/interests/vini-jr.avif', '2025-05-18 22:29:16'),
(18, 5, 'Berlino', 'Città tedesca', NULL, '/assets/images/interests/berlino.jpg', '2025-05-18 22:29:16'),
(19, 5, 'Chicago', 'Città americana', NULL, '/assets/images/interests/chicago.jpg', '2025-05-18 22:29:16'),
(20, 5, 'Milano', 'Città economica dell\'Italia', NULL, '/assets/images/interests/milano.jpg', '2025-05-18 22:29:16'),
(21, 5, 'Madrid', 'Città della Spagna', NULL, '/assets/images/interests/madrid.jpg', '2025-05-18 22:29:16'),
(22, 2, 'Air Force 1', 'Scarpa di ginnastica', NULL, '/assets/images/interests/air-force-1.avif', '2025-05-18 22:29:16'),
(23, 2, 'Air Max', 'Scarpa di ginnastica', NULL, '/assets/images/interests/air-max.avif', '2025-05-18 22:29:16'),
(24, 2, 'Jordan', 'Scarpa di ginnastica', NULL, '/assets/images/interests/jordan.jpg', '2025-05-18 22:29:16');

INSERT INTO slider_images (id, src, alt_text, name, is_free_shipping, is_active, created_at, updated_at) VALUES
(1, 'assets/images/landingpage/slider/scarpa1.png', 'Air Max', 'Air Max', 1, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(2, 'assets/images/landingpage/slider/scarpa2.png', 'Y2K', 'Y2K', 0, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(3, 'assets/images/landingpage/slider/scarpa3.png', 'Air Force 1', 'Air Force 1', 1, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(4, 'assets/images/landingpage/slider/scarpa4.png', 'Field General', 'Field General', 0, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(5, 'assets/images/landingpage/slider/scarpa5.png', 'Air Jordan', 'Air Jordan', 0, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(6, 'assets/images/landingpage/slider/scarpa6.png', 'Pegasus', 'Pegasus', 0, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(7, 'assets/images/landingpage/slider/scarpa7.png', 'Metcon', 'Metcon', 0, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43'),
(8, 'assets/images/landingpage/slider/scarpa8.png', 'Mercurial', 'Mercurial', 1, 1, '2025-05-18 13:24:43', '2025-05-18 13:24:43');

INSERT INTO product_colors (product_id, color_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), 
(2, 2), (2, 1), (2, 4),  
(3, 1), (3, 2), (3, 5), (3, 6), 
(4, 1), (4, 2), (4, 4), (4, 5), (4, 6), (4, 7), 
(5, 1), (5, 3), (5, 5);  

INSERT INTO product_sizes (product_id, size_id, stock_quantity) VALUES
(1, 11, 5), (1, 13, 8), (1, 15, 12), (1, 17, 10), (1, 19, 7), (1, 21, 8),
(2, 11, 10), (2, 13, 15), (2, 15, 20), (2, 17, 18), (2, 19, 12), (2, 21, 0),
(3, 11, 3), (3, 13, 8), (3, 15, 10), (3, 17, 5), (3, 19, 4);

INSERT INTO product_sizes (product_id, size_id, stock_quantity) VALUES
(4, 29, 15), (4, 30, 25), (4, 31, 30), (4, 32, 20), (4, 33, 10),
(5, 29, 5), (5, 30, 8), (5, 31, 12), (5, 32, 7), (5, 33, 3);

INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) VALUES
(1, '/assets/images/products/air-jordan-1-retro-low-quai-54.avif', 'Air Jordan 1 Retro Low Quai 54 - Vista principale', TRUE, 1),
(1, '/assets/images/products/air-jordan-1-retro-low-quai-54-top.png', 'Air Jordan 1 Retro Low Quai 54 - Vista da sopra', FALSE, 2),
(1, '/assets/images/products/air-jordan-1-retro-low-quai-54-side.png', 'Air Jordan 1 Retro Low Quai 54 - Vista laterale', FALSE, 3),
(2, '/assets/images/products/luka-.77-quai-54.avif', 'Luka .77 Quai 54 - Vista principale', TRUE, 1),
(2, '/assets/images/products/luka-.77-quai-54-side.avif', 'Luka .77 Quai 54 - Vista laterale', FALSE, 2),
(3, '/assets/images/products/nike-pegasus-premium.avif', 'Nike Pegasus Premium - Vista principale', TRUE, 1),
(4, '/assets/images/products/air-jordan-1-retro-low.avif', 'Air Jordan 1 Retro Low', TRUE, 1),
(5, '/assets/images/products/nike-air-max-95-recraft.avif', 'Nike Air Max 95 Recraft', TRUE, 1);
