# Architecture Document

# BharatBuy

**Prepared By:** Param P. Shah

**Course:** Bachelor of Computer Applications (BCA) – Semester III  
**Project Type:** User Defined Project (UDP)

---

# 1. Introduction

This document describes the overall architecture of the **BharatBuy** web application. It explains how different components of the system interact, the technologies used, the project directory structure, database architecture, and the complete application workflow.

The project follows a traditional **three-tier architecture**, separating the Presentation Layer, Business Logic Layer, and Data Layer to improve maintainability, scalability, and code organization.

---

# 2. System Architecture

```
+-------------------------+
|        User Browser     |
| (HTML, CSS, JavaScript) |
+-----------+-------------+
            |
            | HTTP Request
            |
            ▼
+-------------------------+
|      PHP Backend        |
| Business Logic Layer    |
+-----------+-------------+
            |
            |
   +--------+--------+
   |                 |
   ▼                 ▼
MySQL Database   External APIs
                    |
        -----------------------------
        |            |              |
        ▼            ▼              ▼
     SerpApi     Amazon API     eBay API
```

---

# 3. Architecture Layers

## 3.1 Presentation Layer

Responsible for displaying information to users.

Technologies:

- HTML5
- CSS3
- JavaScript
- AJAX (Fetch API)

Responsibilities:

- User Interface
- Form Validation
- Product Search Interface
- Dashboard
- Wishlist
- Product Comparison
- Chatbot Interface

---

## 3.2 Business Logic Layer

Responsible for processing application logic.

Technology:

- PHP

Responsibilities:

- User Authentication
- API Communication
- Product Data Processing
- Recommendation Algorithm
- CRUD Operations
- Session Management
- Chatbot Logic

---

## 3.3 Data Layer

Responsible for storing and retrieving data.

Technology:

- MySQL

Stores:

- Users
- Wishlist
- Search History
- Price History
- Price Alerts
- Chatbot Knowledge Base

---

# 4. Technology Stack

## Frontend

- HTML5
- CSS3
- JavaScript
- Fetch API (AJAX)

---

## Backend

- PHP 8

---

## Database

- MySQL

---

## Web Server

- Apache (XAMPP)

---

## External APIs

- SerpApi
- Amazon Product Advertising API
- eBay Browse API

---

## Development Tools

- Visual Studio Code
- XAMPP
- phpMyAdmin
- Git
- GitHub

---

# 5. Application Workflow

```
User

↓

Login / Register

↓

Dashboard

↓

Search Product

↓

PHP Receives Search Request

↓

Call Multiple APIs

↓

Collect Product Data

↓

Normalize Data

↓

Recommendation Algorithm

↓

Compare Products

↓

Display Results

↓

Save Search History

↓

Optional Actions

├── Add to Wishlist
├── Create Price Alert
└── Chat with Assistant
```

---

# 6. Authentication Flow

```
Register

↓

Validate Form

↓

Store User in Database

↓

Login

↓

Verify Credentials

↓

Create Session

↓

Dashboard

↓

Logout

↓

Destroy Session
```

---

# 7. Product Search Flow

```
User enters Product Name

↓

JavaScript sends request

↓

PHP receives request

↓

Call APIs

↓

Receive JSON Responses

↓

Normalize Product Data

↓

Compare Products

↓

Calculate Recommendation Score

↓

Return Results

↓

Display Comparison Table
```

---

# 8. Recommendation Engine Flow

```
Product Results

↓

Extract

• Price
• Rating
• Reviews
• Seller

↓

Calculate Individual Scores

↓

Weighted Calculation

↓

Overall Recommendation Score

↓

Rank Products

↓

Display Best Product
```

---

# 9. CRUD Modules

## Users

Create
- Register

Read
- View Profile

Update
- Edit Profile

Delete
- Delete Account (Optional)

---

## Wishlist

Create
- Add Product

Read
- View Wishlist

Update
- Edit Personal Notes

Delete
- Remove Product

---

## Search History

Create
- Save Search

Read
- View Search History

Delete
- Delete Individual Search
- Clear History

---

## Price Alerts

Create
- Add Alert

Read
- View Alerts

Update
- Modify Alert

Delete
- Remove Alert

---

# 10. Database Structure

## Main Tables

### users

Stores registered user information.

---

### wishlist

Stores products saved by users.

---

### search_history

Stores previous searches.

---

### price_history

Stores historical product prices.

---

### alerts

Stores target prices for notifications.

---

### chatbot

Stores predefined chatbot questions and responses.

---

# 11. Folder Structure

```
BharatBuy/

│
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── search.php
├── compare.php
├── wishlist.php
├── history.php
├── alerts.php
├── profile.php
├── chatbot.php
│
├── api/
│   ├── serpapi.php
│   ├── amazon.php
│   ├── ebay.php
│   ├── normalize.php
│   └── recommendation.php
│
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── auth.php
│   ├── session.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── ajax/
│   ├── searchProduct.php
│   ├── chatbot.php
│   ├── wishlist.php
│   └── alerts.php
│
├── css/
│   ├── style.css
│   ├── dashboard.css
│   └── chatbot.css
│
├── js/
│   ├── app.js
│   ├── dashboard.js
│   ├── chatbot.js
│   ├── search.js
│   └── wishlist.js
│
├── images/
│
├── uploads/
│
├── database/
│   └── bharatbuy.sql
│
└── README.md
```

---

# 12. API Integration

The application will retrieve product data using multiple APIs.

```
User Search

↓

PHP

↓

API Request

↓

SerpApi

↓

Amazon API

↓

eBay API

↓

Receive JSON

↓

Normalize Data

↓

Recommendation Engine

↓

Display Results
```

Each API module will work independently so that additional APIs can be integrated without affecting existing functionality.

---

# 13. Recommendation Algorithm

The recommendation engine evaluates products using weighted criteria.

Example:

```
Recommendation Score

=

40% Price

+

30% Rating

+

20% Number of Reviews

+

10% Seller Reliability
```

The product with the highest overall score is recommended as the best purchase.

---

# 14. Security Architecture

The application will implement the following security measures:

- Password hashing
- Session-based authentication
- Input validation
- SQL injection prevention using prepared statements
- XSS prevention
- Server-side validation
- Secure session management

---

# 15. Future Scalability

The architecture is designed to support future enhancements, including:

- Additional shopping APIs
- AI-powered chatbot
- Machine Learning recommendation engine
- Mobile application
- Browser extension
- Email notifications
- Push notifications
- Admin dashboard
- Voice search

---

# 16. Advantages of the Architecture

- Modular design
- Easy maintenance
- Separation of concerns
- Scalable API integration
- Secure authentication
- Organized folder structure
- Easy feature expansion
- Efficient database management

---

# 17. Summary

The BharatBuy follows a modular three-tier architecture that separates the user interface, business logic, and data storage. The application integrates multiple external APIs to collect product information, processes the data through a recommendation engine, and presents intelligent shopping suggestions to users.

The architecture is designed to be scalable, maintainable, and suitable for future enhancements while demonstrating key concepts of full-stack web development, API integration, CRUD operations, and database management.