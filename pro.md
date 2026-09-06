I will provide the entire project in organized blocks. First, let's start with the **Full Folder Structure** and the **Database Schema**.

Create a folder named `BharatBuy` in your `htdocs` directory and set up these folders inside it.

### 1. Full Folder Structure
```text
BharatBuy/
│
├── ajax/                      # AJAX endpoints for frontend
│   ├── chatbot.php
│   ├── searchProduct.php
│   └── wishlist.php
│
├── api/                       # Business logic & algorithms
│   └── recommendation.php
│
├── css/                       # Stylesheets
│   ├── dashboard.css
│   └── style.css
│
├── database/                  # SQL file
│   └── bharatbuy.sql
│
├── includes/                  # Core PHP logic & UI components
│   ├── config.php
│   ├── db.php
│   ├── footer.php
│   ├── header.php
│   └── session.php
│
├── js/                        # Frontend logic
│   ├── chatbot.js
│   └── search.js
│
├── dashboard.php              # User Dashboard
├── index.php                  # Landing Page
├── login.php                  # Login Page
├── logout.php                 # Logout Logic
├── register.php               # Registration Page
└── wishlist.php               # Wishlist Page
```

---

### 2. The Database Layer (SQL)
**File:** `database/bharatbuy.sql`
*Instructions: Import this into PHPMyAdmin after creating a database named `smart_shopping_assistant`.*

```sql
CREATE DATABASE IF NOT EXISTS smart_shopping_assistant;
USE smart_shopping_assistant;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    website VARCHAR(100) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 0.0,
    review_count INT DEFAULT 0,
    image_url TEXT,
    product_url TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE wishlist (
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

CREATE TABLE chatbot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(150) NOT NULL,
    answer TEXT NOT NULL
);

INSERT INTO chatbot (keyword, answer) VALUES 
('how it works', 'We compare prices from Amazon and eBay using a smart weighted algorithm.'),
('recommendation', 'Scores are based 40% on price, 30% on ratings, and 30% on reviews.'),
('help', 'You can search for any product in the dashboard and I will find the best deal!');
```

---

### 3. Core Configuration & Database Connection
**File:** `includes/config.php`

```php
<?php
define('APP_NAME', 'BharatBuy');
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_shopping_assistant');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change if you have a password

session_start();
```

**File:** `includes/db.php`

```php
<?php
require_once 'config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
```

**File:** `includes/session.php`

```php
<?php
function isLoggedIn() { return isset($_SESSION['user_id']); }
function redirectIfLoggedOut() {
    if (!isLoggedIn()) { header("Location: login.php"); exit(); }
}
```

---

### 4. Layout (Header & Footer)
**File:** `includes/header.php`

```php
<?php require_once 'session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <a href="index.php" class="nav-logo">SmartShop</a>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <?php if(isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                    <li><a href="logout.php" class="btn btn-primary">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Join</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
```

**File:** `includes/footer.php`

```php
    </main>
    <footer><p>&copy; <?php echo date('Y'); ?> BharatBuy</p></footer>
</body>
</html>
```

---

**I have provided the Database, Config, and Layout files. Shall I continue with the CSS, Auth Pages, and Core Logic?**