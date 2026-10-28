CREATE DATABASE IF NOT EXISTS student_exchange_hub_pro
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_exchange_hub_pro;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NOT NULL DEFAULT '',
  course VARCHAR(100) NOT NULL DEFAULT '',
  college VARCHAR(150) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  category VARCHAR(50) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  item_condition VARCHAR(50) NOT NULL,
  item_type VARCHAR(30) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(100) NOT NULL DEFAULT 'Campus',
  seller_id INT UNSIGNED NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Available',
  views INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_products_status_created (status, created_at),
  INDEX idx_products_category (category),
  INDEX idx_products_seller (seller_id),
  CONSTRAINT fk_products_seller FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_messages_receiver_created (receiver_id, created_at),
  INDEX idx_messages_sender_created (sender_id, created_at),
  CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===== Stored procedures =====
DROP PROCEDURE IF EXISTS sp_marketplace_stats;
DROP PROCEDURE IF EXISTS sp_search_titles;
DROP PROCEDURE IF EXISTS sp_mark_sold;

DELIMITER $$

CREATE PROCEDURE sp_marketplace_stats()
BEGIN
  SELECT
    (SELECT COUNT(*) FROM products WHERE status = 'Available')                AS items,
    (SELECT COUNT(DISTINCT category) FROM products WHERE status = 'Available') AS categories,
    (SELECT COUNT(*) FROM users)                                               AS students;
END$$

CREATE PROCEDURE sp_search_titles(IN p_term VARCHAR(100))
BEGIN
  SELECT id, title, category, item_type
  FROM products
  WHERE status = 'Available' AND title LIKE CONCAT('%', p_term, '%')
  ORDER BY created_at DESC
  LIMIT 6;
END$$

CREATE PROCEDURE sp_mark_sold(IN p_product_id INT, IN p_seller_id INT)
BEGIN
  UPDATE products SET status = 'Sold'
  WHERE id = p_product_id AND seller_id = p_seller_id AND status = 'Available';
  SELECT ROW_COUNT() AS changed;
END$$

DELIMITER ;
