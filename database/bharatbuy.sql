CREATE DATABASE IF NOT EXISTS smart_shopping_assistant;
USE smart_shopping_assistant;

-- 1. users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    alerts_enabled TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. product_prices Table
CREATE TABLE IF NOT EXISTS product_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    website VARCHAR(100) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) NOT NULL,
    discount_rate INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    image_url TEXT,
    product_url TEXT,
    description TEXT,
    is_out_of_stock TINYINT(1) DEFAULT 0,
    category VARCHAR(100) DEFAULT 'General',
    seller_name VARCHAR(150) NULL,
    item_condition VARCHAR(100) NULL,
    item_id VARCHAR(50) NULL,
    shipping_info VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. search_history Table
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    website VARCHAR(100) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    product_url TEXT NOT NULL,
    image_url TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. price_history Table
CREATE TABLE IF NOT EXISTS price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product_prices(id) ON DELETE CASCADE
);

-- 6. product_trackers Table
CREATE TABLE IF NOT EXISTS product_trackers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    target_price DECIMAL(10,2) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product_prices(id) ON DELETE CASCADE
);

-- 7. user_notifications Table
CREATE TABLE IF NOT EXISTS user_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    product_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product_prices(id) ON DELETE SET NULL
);

-- 8. chatbot Table (Keyword-based FAQ lookup)
CREATE TABLE IF NOT EXISTS chatbot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(150) NOT NULL,
    answer TEXT NOT NULL
);

-- Pre-populate chatbot FAQs
INSERT INTO chatbot (keyword, answer) VALUES 
('how it works', 'We compare prices from Amazon and eBay using a smart weighted algorithm.'),
('recommendation', 'Scores are based 40% on price, 30% on ratings, and 30% on reviews.'),
('help', 'You can search for any product in the dashboard and I will find the best deal!');

-- 9. chatbot_logs Table (Tracks API metrics & errors)
CREATE TABLE IF NOT EXISTS chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    source VARCHAR(20) NOT NULL, -- 'gemini', 'fallback', 'rate_limit', 'rejected'
    response_time_ms INT DEFAULT 0,
    error_message TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 10. provider_logs Table
CREATE TABLE IF NOT EXISTS provider_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_name VARCHAR(100) NOT NULL,
    status INT NOT NULL,
    response_time_ms INT DEFAULT 0,
    error_message TEXT DEFAULT NULL,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. api_cache Table
CREATE TABLE IF NOT EXISTS api_cache (
    provider_name VARCHAR(100) NOT NULL,
    cache_key VARCHAR(255) NOT NULL,
    response_data LONGTEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (provider_name, cache_key)
);
