-- database/pos-schema.sql
-- Run after update-schema.sql (needs the users + products tables to already exist).
--   mysql -u root davao_boss_computer < database/pos-schema.sql

USE davao_boss_computer;

CREATE TABLE IF NOT EXISTS sales (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id   INT UNSIGNED NOT NULL,
    customer_name VARCHAR(150) DEFAULT NULL,
    total_amount  DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS sale_items (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id      INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    quantity     INT UNSIGNED NOT NULL,
    subtotal     DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS quotations (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id      INT UNSIGNED NOT NULL,
    customer_name    VARCHAR(150) DEFAULT NULL,
    customer_contact VARCHAR(150) DEFAULT NULL,
    notes            VARCHAR(255) DEFAULT NULL,
    total_amount     DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS quotation_items (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quotation_id  INT UNSIGNED NOT NULL,
    product_id    INT UNSIGNED NOT NULL,
    product_name  VARCHAR(150) NOT NULL,
    unit_price    DECIMAL(10,2) NOT NULL,
    quantity      INT UNSIGNED NOT NULL,
    subtotal      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
