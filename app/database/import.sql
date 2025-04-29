-- Use the correct database
USE secondhand_market;

-- Drop tables in order (child tables first)
DROP TRIGGER IF EXISTS after_sale_insert;
DROP TABLE IF EXISTS user_product;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS user;

-- Create user table
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

-- Create product table
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

-- Create user_product table (sales)
CREATE TABLE user_product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    sales INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (product_id) REFERENCES product(id)
);

-- Optional trigger: mark product as sold after sale
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
