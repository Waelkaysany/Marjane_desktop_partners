-- Create equipementpartner database
CREATE DATABASE IF NOT EXISTS equipementpartner;
USE equipementpartner;

-- Create the requests table
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_id INT NOT NULL,
    equipement_id INT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'
);

-- Insert sample equipment data (optional)
INSERT INTO requests (partner_id, equipement_id, time, status) VALUES
(1, 1, '2025-08-26 19:15:59', 'pending'),
(1, 1, '2025-08-26 19:36:08', 'pending'),
(1, 2, '2025-08-26 19:51:11', 'pending');

-- Create equipment catalog table (optional - for reference)
CREATE TABLE IF NOT EXISTS equipment_catalog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    image_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample equipment catalog
INSERT INTO equipment_catalog (id, name, type, description, price) VALUES
(1, 'FrostLine Triple Glass Door Cooler', 'refrigerator', 'A high-capacity, energy-efficient commercial display refrigerator with triple glass doors, adjustable shelving, and LED lighting, perfect for showcasing beverages and perishables in retail spaces.', 299.00),
(2, 'Heavy-Duty Shelf', 'shelf', 'Durable, spacious, and perfect for retail or storage, this heavy-duty shelving unit offers multiple tiers to organize and display products efficiently.', 299.00),
(3, 'Smart Checkout Pro', 'checkout', 'A modern, high-performance retail checkout counter designed for efficiency and style, featuring a sleek POS system, conveyor belt, and spacious bagging area for a seamless shopping experience.', 299.00);
