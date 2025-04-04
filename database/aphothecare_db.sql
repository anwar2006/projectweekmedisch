-- Create database
CREATE DATABASE IF NOT EXISTS aphothecare_db;
USE aphothecare_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'staff', 'admin') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(255),
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User addresses table
CREATE TABLE IF NOT EXISTS user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('billing', 'shipping') NOT NULL,
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    is_default BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Remember tokens for "remember me" functionality
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Product categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    parent_id INT,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    sku VARCHAR(50),
    description TEXT,
    short_description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    stock_quantity INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,
    category VARCHAR(50) NOT NULL,
    requires_prescription BOOLEAN DEFAULT FALSE,
    image VARCHAR(255),
    featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Product images
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT,
    prescription_file VARCHAR(255) NOT NULL,
    doctor_name VARCHAR(100),
    doctor_license VARCHAR(50),
    patient_name VARCHAR(100),
    issue_date DATE,
    expiry_date DATE,
    notes TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewed_by INT,
    review_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Order statuses
CREATE TABLE IF NOT EXISTS order_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255)
);

-- Insert default order statuses
INSERT INTO order_statuses (name, description) VALUES
('Pending', 'Order has been placed but not yet processed'),
('Processing', 'Order is being processed'),
('Shipped', 'Order has been shipped'),
('Delivered', 'Order has been delivered'),
('Cancelled', 'Order was cancelled');

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    billing_address_id INT,
    shipping_address_id INT,
    status_id INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0.00,
    shipping_amount DECIMAL(10, 2) DEFAULT 0.00,
    discount_amount DECIMAL(10, 2) DEFAULT 0.00,
    tracking_number VARCHAR(100),
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (billing_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (shipping_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (status_id) REFERENCES order_statuses(id)
);

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- User activity logs
CREATE TABLE IF NOT EXISTS user_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'spam') DEFAULT 'new',
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, password, role, created_at) VALUES
('Admin', 'User', 'admin@aphothecare.com', '$2y$10$8z3V4v3HJq9XjgV5Y5W6UO8ivTY4B5HH62S1U1ygCTKvTCq0AeIxy', 'admin', NOW());

-- Sample product categories
INSERT INTO categories (name, slug, description) VALUES
('Pain Relief', 'pain-relief', 'Pain relief medications and products'),
('Vitamins & Supplements', 'vitamins-supplements', 'Vitamins, minerals, and nutritional supplements'),
('Cold & Flu', 'cold-flu', 'Medications and products for cold and flu symptoms'),
('Allergies', 'allergies', 'Allergy medications and products'),
('First Aid', 'first-aid', 'First aid supplies and products'),
('Skin Care', 'skin-care', 'Skin care products and medications');

-- Sample products (medications)
INSERT INTO products (name, slug, description, price, category, requires_prescription, stock_quantity, reorder_level, featured) VALUES
('Paracetamol 500mg', 'paracetamol-500mg', 'Pain relief tablets for headaches, pain and fever. Pack of 16 tablets.', 5.99, 'medications', 0, 50, 15, 1),
('Ibuprofen 400mg', 'ibuprofen-400mg', 'Anti-inflammatory pain relief. Pack of 24 tablets.', 6.99, 'medications', 0, 45, 15, 1),
('Amoxicillin 250mg', 'amoxicillin-250mg', 'Antibiotic medication. Pack of 21 capsules.', 12.99, 'medications', 1, 30, 10, 0),
('Cetirizine 10mg', 'cetirizine-10mg', 'Antihistamine for allergy relief. Pack of 30 tablets.', 7.50, 'medications', 0, 40, 15, 0),
('Fluoxetine 20mg', 'fluoxetine-20mg', 'Antidepressant medication. Pack of 28 capsules.', 14.99, 'medications', 1, 25, 10, 0),
('Salbutamol Inhaler', 'salbutamol-inhaler', 'Relieves asthma symptoms. 200 doses.', 18.75, 'medications', 1, 20, 8, 0);

-- Sample products (health products)
INSERT INTO products (name, slug, description, price, category, requires_prescription, stock_quantity, reorder_level, featured) VALUES
('Vitamin C 1000mg', 'vitamin-c-1000mg', 'Supports immune system health. 60 tablets.', 12.50, 'health-products', 0, 65, 20, 1),
('Multivitamin Daily', 'multivitamin-daily', 'Complete daily multivitamin. 90 tablets.', 16.95, 'health-products', 0, 55, 20, 0),
('Omega-3 Fish Oil', 'omega-3-fish-oil', 'Supports heart and brain health. 60 capsules.', 14.25, 'health-products', 0, 48, 15, 0),
('Digital Thermometer', 'digital-thermometer', 'Accurate digital thermometer for body temperature readings.', 15.75, 'health-products', 0, 35, 10, 1),
('Blood Pressure Monitor', 'blood-pressure-monitor', 'Digital blood pressure monitor for home use.', 49.99, 'health-products', 0, 18, 5, 0),
('First Aid Kit', 'first-aid-kit', 'Comprehensive first aid kit for home use.', 24.99, 'health-products', 0, 25, 10, 0); 