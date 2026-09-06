# Database Design Document

# BharatBuy

**Prepared By:** Param P. Shah

**Course:** Bachelor of Computer Applications (BCA) – Semester III  
**Project Type:** User Defined Project (UDP)

---

# 1. Introduction

This document defines the complete database architecture for the **BharatBuy** project.

The database is responsible for storing user information, product search history, wishlists, price tracking, chatbot responses, and user-specific preferences.

The application uses **MySQL** as the Relational Database Management System (RDBMS).

---

# 2. Database Information

| Property | Value |
|----------|-------|
| Database Name | smart_shopping_assistant |
| Database Engine | MySQL |
| Character Set | utf8mb4 |
| Collation | utf8mb4_unicode_ci |
| Storage Engine | InnoDB |

---

# 3. Database Objectives

The database should:

- Store registered users securely.
- Store search history.
- Store user wishlists.
- Track product prices over time.
- Manage price alerts.
- Store chatbot questions and answers.
- Maintain relationships between all modules.
- Prevent duplicate and inconsistent data.

---

# 4. Entity Relationship Overview

```
                    USERS
                      │
        ┌─────────────┼─────────────┐
        │             │             │
        ▼             ▼             ▼
 SEARCH_HISTORY   WISHLIST      PRICE_ALERTS
        │
        ▼
 PRODUCT_PRICES
        │
        ▼
 PRICE_HISTORY

CHATBOT (Independent Table)
```

---

# 5. Database Tables

The project consists of the following tables:

| Table Name | Purpose |
|------------|---------|
| users | Stores registered users |
| search_history | Stores searched products |
| wishlist | Stores saved products |
| product_prices | Stores latest fetched product information |
| price_history | Stores historical prices |
| price_alerts | Stores target price notifications |
| chatbot | Stores chatbot questions and responses |

---

# 6. Table Design

---

## Table: users

### Purpose

Stores all registered users.

### Columns

| Column | Type | Null | Key | Description |
|----------|------|------|-----|-------------|
| id | INT | NO | PK | User ID |
| full_name | VARCHAR(100) | NO | | Full Name |
| email | VARCHAR(150) | NO | UNIQUE | Email Address |
| password | VARCHAR(255) | NO | | Hashed Password |
| profile_image | VARCHAR(255) | YES | | Profile Picture |
| created_at | TIMESTAMP | NO | | Registration Date |
| updated_at | TIMESTAMP | YES | | Last Updated |

### Relationships

One User

↓

Many Search History

↓

Many Wishlist

↓

Many Price Alerts

---

## Table: search_history

### Purpose

Stores every product searched by a user.

### Columns

| Column | Type |
|----------|------|
| id | INT |
| user_id | INT |
| product_name | VARCHAR(255) |
| searched_at | TIMESTAMP |

### Foreign Keys

user_id

↓

users.id

---

## Table: wishlist

### Purpose

Stores products saved by users.

### Columns

| Column | Type |
|----------|------|
| id | INT |
| user_id | INT |
| product_name | VARCHAR(255) |
| website | VARCHAR(100) |
| current_price | DECIMAL(10,2) |
| product_url | TEXT |
| image_url | TEXT |
| notes | TEXT |
| created_at | TIMESTAMP |

### Foreign Key

user_id

↓

users.id

---

## Table: product_prices

### Purpose

Stores the latest product information fetched from APIs.

### Columns

| Column | Type |
|----------|------|
| id | INT |
| product_name | VARCHAR(255) |
| website | VARCHAR(100) |
| current_price | DECIMAL(10,2) |
| rating | DECIMAL(2,1) |
| review_count | INT |
| seller_name | VARCHAR(150) |
| availability | VARCHAR(50) |
| product_url | TEXT |
| image_url | TEXT |
| last_updated | TIMESTAMP |

---

## Table: price_history

### Purpose

Maintains historical product prices.

### Columns

| Column | Type |
|----------|------|
| id | INT |
| product_id | INT |
| website | VARCHAR(100) |
| price | DECIMAL(10,2) |
| recorded_at | TIMESTAMP |

### Foreign Key

product_id

↓

product_prices.id

---

## Table: price_alerts

### Purpose

Stores target prices chosen by users.

### Columns

| Column | Type |
|----------|------|
| id | INT |
| user_id | INT |
| product_name | VARCHAR(255) |
| target_price | DECIMAL(10,2) |
| created_at | TIMESTAMP |

### Foreign Key

user_id

↓

users.id

---

## Table: chatbot

### Purpose

Stores chatbot knowledge base.

### Columns

| Column | Type |
|----------|------|
| id | INT |
| keyword | VARCHAR(150) |
| question | TEXT |
| answer | TEXT |

---

# 7. Relationships

## users → search_history

One-to-Many

One user can have multiple searches.

---

## users → wishlist

One-to-Many

One user can save multiple products.

---

## users → price_alerts

One-to-Many

One user can create multiple alerts.

---

## product_prices → price_history

One-to-Many

One product has multiple historical price records.

---

# 8. Database Normalization

The database follows Third Normal Form (3NF).

## First Normal Form (1NF)

- No repeating groups.
- Atomic values.
- Unique rows.

Satisfied.

---

## Second Normal Form (2NF)

- All non-key attributes depend on the primary key.

Satisfied.

---

## Third Normal Form (3NF)

- No transitive dependencies.
- Data stored only once.

Satisfied.

---

# 9. Constraints

## Primary Keys

- users.id
- wishlist.id
- search_history.id
- product_prices.id
- price_history.id
- price_alerts.id
- chatbot.id

---

## Foreign Keys

search_history.user_id

↓

users.id

wishlist.user_id

↓

users.id

price_alerts.user_id

↓

users.id

price_history.product_id

↓

product_prices.id

---

## Unique Constraints

users.email

---

## Default Values

created_at

↓

CURRENT_TIMESTAMP

---

# 10. Indexes

Recommended indexes:

| Table | Column |
|--------|---------|
| users | email |
| wishlist | user_id |
| search_history | user_id |
| product_prices | product_name |
| price_history | product_id |
| price_alerts | user_id |

These indexes improve search performance.

---

# 11. Sample Records

## users

| id | Name | Email |
|----|------|-------|
| 1 | Param Shah | param@example.com |

---

## search_history

| id | User | Product |
|----|------|---------|
| 1 | 1 | iPhone 15 |

---

## wishlist

| id | Product |
|----|---------|
| 1 | Samsung S25 |

---

## product_prices

| Website | Price |
|----------|-------|
| Amazon | 64999 |
| eBay | 65150 |

---

# 12. SQL Schema

## users

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);
```

---

## search_history

```sql
CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

## wishlist

```sql
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255),
    website VARCHAR(100),
    current_price DECIMAL(10,2),
    product_url TEXT,
    image_url TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

## product_prices

```sql
CREATE TABLE product_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255),
    website VARCHAR(100),
    current_price DECIMAL(10,2),
    rating DECIMAL(2,1),
    review_count INT,
    seller_name VARCHAR(150),
    availability VARCHAR(50),
    product_url TEXT,
    image_url TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## price_history

```sql
CREATE TABLE price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    website VARCHAR(100),
    price DECIMAL(10,2),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(product_id)
    REFERENCES product_prices(id)
    ON DELETE CASCADE
);
```

---

## price_alerts

```sql
CREATE TABLE price_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_name VARCHAR(255),
    target_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

## chatbot

```sql
CREATE TABLE chatbot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(150),
    question TEXT,
    answer TEXT
);
```

---

# 13. Database Workflow

```
User Registers
      │
      ▼
users
      │
      ▼
User Logs In
      │
      ▼
Dashboard
      │
      ▼
Search Product
      │
      ▼
API Response
      │
      ▼
product_prices
      │
      ├────────► price_history
      │
      ├────────► search_history
      │
      ├────────► wishlist
      │
      └────────► price_alerts

Chatbot
      │
      ▼
chatbot
```

---

# 14. Security Considerations

The database will implement the following security measures:

- Passwords stored using `password_hash()`.
- Prepared statements for all SQL queries.
- Foreign key constraints to maintain referential integrity.
- Cascading deletes for related user data.
- Input validation before database operations.
- Unique email addresses to prevent duplicate accounts.

---

# 15. Future Database Enhancements

Future versions of the project may include additional tables such as:

- product_categories
- user_preferences
- notifications
- seller_information
- product_reviews
- comparison_history
- admin_users
- activity_logs
- api_request_logs
- recommendation_history

These tables can be added without redesigning the existing database schema.

---

# 16. Conclusion

The BharatBuy database is designed using relational database principles and normalized to Third Normal Form (3NF). The schema supports secure user authentication, efficient product comparison, personalized features such as wishlists and price alerts, historical price tracking, and chatbot functionality.

Its modular design ensures maintainability, scalability, and compatibility with future enhancements, making it well-suited for a full-stack BCA Semester III User Defined Project.