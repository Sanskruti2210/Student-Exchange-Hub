-- Stored procedures used by the PHP code (Unit 5: "stored procedure execution in PHP").
-- Import this file in phpMyAdmin (SQL tab) if you already imported setup.sql earlier.
USE student_exchange_hub_pro;

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
