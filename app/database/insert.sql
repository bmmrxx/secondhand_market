-- Use the correct database
USE secondhand_market;

-- Drop objects in the correct order to handle foreign keys
DROP TRIGGER IF EXISTS after_sale_insert;
DROP TABLE IF EXISTS user_product;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS user;

-- Create the user table
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'member'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create the product table
CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255),
    category ENUM('footwear', 'bottoms', 'tops', 'home-decor', 'elektrical-devices', 'kitchen', 'card-games', 'video-games', 'board-games'),
    description TEXT,
    price INT,
    status ENUM('sold', 'in stock'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

-- Create the user_product table (for sales)
CREATE TABLE user_product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    sales INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (product_id) REFERENCES product(id)
);

-- Optional trigger to mark a product as sold after a sale
DELIMITER //
CREATE TRIGGER after_sale_insert
AFTER INSERT ON user_product
FOR EACH ROW
BEGIN
    UPDATE product
    SET status = 'sold'
    WHERE id = NEW.product_id;
END;
//
DELIMITER ;

-- Insert generic users
INSERT INTO user (username, firstname, lastname, email, password, role)
VALUES
('testuser', 'Test', 'User', 'test@example.com', 'testpassword', 'member'),
('admin', 'Admin', 'User', 'admin@example.com', 'testpassword', 'admin');


-- Insert one product for each category
INSERT INTO product (user_id, name, category, description, price, status)
VALUES
(1, 'Nike Air Max', 'footwear', 'Lightweight running shoes in great condition.', 85, 'in stock'),
(2, 'Levi\'s 501 Jeans', 'bottoms', 'Classic straight-fit jeans.', 40, 'in stock'),
(1, 'Winter Jacket', 'tops', 'Warm and waterproof jacket.', 50, 'in stock'),
(2, 'Scandinavian Wall Clock', 'home-decor', 'Minimalist wooden wall clock.', 35, 'in stock'),
(1, 'Sony WH-1000XM4', 'elektrical-devices', 'Noise-cancelling headphones.', 200, 'in stock'),
(2, 'Instant Pot Duo', 'kitchen', '7-in-1 electric pressure cooker.', 60, 'in stock'),
(1, 'Uno', 'card-games', 'Fun family card game.', 10, 'in stock'),
(2, 'The Legend of Zelda: Breath of the Wild', 'video-games', 'Open-world adventure game for Nintendo Switch.', 45, 'in stock'),
(1, 'Settlers of Catan', 'board-games', 'Classic strategy board game.', 25, 'in stock');

-- Insert sales into user_product table
INSERT INTO user_product (user_id, product_id, sales)
VALUES
(2, 1, 1),  -- Admin (user_id 2) buys Nike Air Max from Test (user_id 1)
(1, 5, 1);  -- Test (user_id 1) buys Sony WH-1000XM4 from Admin (user_id 2)
