CREATE DATABASE IF NOT EXISTS pharmacy_sales;
USE pharmacy_sales;

CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(100) NOT NULL PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pharmacist') NOT NULL DEFAULT 'pharmacist',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS medicines (
    medicine_name VARCHAR(255) NOT NULL PRIMARY KEY,
    expiry_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sales (
    medicine_name VARCHAR(255) NOT NULL,
    quantity_sold INT NOT NULL,
    amount_received DECIMAL(10, 2) NOT NULL,
    sold_date DATE NOT NULL,
    pharmacist VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sales_sold_date ON sales(sold_date);
CREATE INDEX idx_medicines_expiry ON medicines(expiry_date);
CREATE INDEX idx_sales_medicine ON sales(medicine_name);

-- Default admin account: username=admin, password=admin123
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$12$s6PrHlf.rovaiufad/zYA.wSUpCwoGGJpceyn9MEtDqDA1o2W84zu', 'admin');
