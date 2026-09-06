# Project Requirements Document (PRD)

# BharatBuy

**Prepared By:** Param P. Shah

**Course:** Bachelor of Computer Applications (BCA) – Semester III  
**Project Type:** User Defined Project (UDP)

---

# 1. Project Overview

BharatBuy is a web-based application designed to help users make informed purchasing decisions by comparing products from multiple online shopping platforms. Instead of simply displaying the lowest price, the application analyzes multiple factors such as price, customer ratings, number of reviews, seller reliability, and product availability to recommend the best overall purchase.

The application provides a centralized platform where users can search for products, compare offers, maintain wishlists, track price changes, and receive intelligent recommendations through a user-friendly interface.

---

# 2. Problem Statement

Online shoppers often spend considerable time visiting multiple e-commerce websites to compare product prices and reviews before making a purchase. This process is repetitive, time-consuming, and may still lead to suboptimal buying decisions.

Most users compare products manually, making it difficult to identify the best value rather than simply the cheapest option.

BharatBuy aims to simplify this process by collecting product information from multiple sources and presenting it in a single interface with intelligent recommendations.

---

# 3. Project Objective

The primary objective of this project is to develop a web application that:

- Allows users to search products from multiple shopping platforms.
- Compares prices, ratings, and reviews.
- Recommends the best product using a recommendation algorithm.
- Maintains user-specific information such as search history and wishlist.
- Provides chatbot assistance for easier navigation and shopping guidance.

---

# 4. Target Users

The application is intended for:

- Students
- General online shoppers
- Budget-conscious buyers
- Technology enthusiasts
- Users who frequently compare products before purchasing

---

# 5. Project Scope

The project focuses on providing a centralized shopping comparison platform.

It includes:

- User authentication
- Product comparison
- Recommendation engine
- Wishlist management
- Search history
- Price tracking
- Chatbot assistance

The project does not process payments or sell products directly. Instead, it redirects users to the respective shopping platform after comparison.

---

# 6. Functional Requirements

## 6.1 User Authentication

The system shall allow users to:

- Register a new account
- Log in securely
- Log out
- Maintain user sessions

---

## 6.2 User Dashboard

The dashboard shall display:

- Welcome message
- Recent searches
- Wishlist
- Saved price alerts
- Account information

---

## 6.3 Product Search

Users shall be able to:

- Search products using keywords
- View search results from multiple shopping platforms
- Filter search results

---

## 6.4 Product Comparison

The system shall compare:

- Product Name
- Price
- Rating
- Number of Reviews
- Seller
- Availability
- Delivery Information (if available)

---

## 6.5 Recommendation Engine

The system shall recommend products based on multiple weighted factors instead of only price.

Possible factors include:

- Price
- Customer Rating
- Number of Reviews
- Seller Reliability
- Availability

---

## 6.6 Wishlist

Users shall be able to:

- Add products
- View saved products
- Remove products
- Add personal notes

---

## 6.7 Search History

The system shall:

- Store previous searches
- Display search history
- Allow users to clear history

---

## 6.8 Price Tracking

The system shall:

- Store searched product prices
- Maintain historical price records
- Display price changes over time

---

## 6.9 Price Alerts

Users shall be able to:

- Set target prices
- Receive notifications when the target price is reached

---

## 6.10 Chatbot

The chatbot shall:

- Answer frequently asked questions
- Help users navigate the application
- Assist in product comparison
- Explain recommendation results

---

# 7. Non-Functional Requirements

The system should:

- Be responsive across desktop and mobile devices.
- Provide a clean and intuitive interface.
- Load search results efficiently.
- Store user information securely.
- Validate user input.
- Handle invalid searches gracefully.
- Be modular and maintainable.

---

# 8. Technology Stack

## Frontend

- HTML5
- CSS3
- JavaScript

## Backend

- PHP

## Database

- MySQL

## APIs

- SerpApi
- Amazon Product Advertising API
- eBay Browse API

## Server

- XAMPP

---

# 9. Database Requirements

The project will include the following primary tables:

- Users
- Wishlist
- Search History
- Price History
- Price Alerts
- Chatbot Knowledge Base

Additional tables may be added during implementation if required.

---

# 10. External Integrations

The application will integrate with external APIs to retrieve product information such as:

- Product Name
- Price
- Product Rating
- Number of Reviews
- Product Images
- Seller Information

---

# 11. User Roles

## Registered User

Permissions:

- Register
- Login
- Search products
- Compare products
- Manage wishlist
- Manage search history
- Set price alerts
- Interact with chatbot

---

## Administrator (Optional)

Permissions:

- Manage chatbot responses
- Monitor system usage
- Manage users
- View application statistics

---

# 12. Expected Workflow

1. User registers or logs in.
2. User searches for a product.
3. The system queries multiple APIs.
4. Product data is collected and standardized.
5. Products are compared.
6. Recommendation algorithm generates the best option.
7. Results are displayed.
8. Search is saved to history.
9. User may save products to wishlist.
10. User may create price alerts.
11. Chatbot assists users when required.

---

# 13. Key Features

- User Registration & Login
- Dashboard
- Product Search
- Multi-Source Product Comparison
- Smart Recommendation Engine
- Wishlist Management
- Search History
- Price Tracking
- Price Alerts
- Chatbot Assistance
- Responsive User Interface

---

# 14. Limitations

- Product information depends on third-party API availability.
- API rate limits may restrict the number of searches.
- Some shopping websites may not provide public APIs.
- Price information may not update instantly.
- Recommendation accuracy depends on the quality of retrieved data.

---

# 15. Future Enhancements

Future versions of the project may include:

- AI-powered shopping assistant
- Machine learning-based recommendation engine
- Price prediction
- Browser extension
- Mobile application
- Email notifications
- Voice search
- Barcode scanning
- Sentiment analysis of customer reviews

---

# 16. Success Criteria

The project will be considered successful if users can:

- Create and manage accounts.
- Search products successfully.
- Compare products from multiple sources.
- Receive intelligent recommendations.
- Maintain wishlists and search history.
- Track product prices.
- Use chatbot assistance effectively.
- Navigate the application with ease.

---

# 17. Conclusion

BharatBuy aims to simplify online shopping by bringing together product information from multiple e-commerce platforms into a single application. By combining product comparison, personalized features, price tracking, and intelligent recommendations, the system helps users make informed purchasing decisions while reducing the time and effort required for manual comparison.

The project demonstrates practical implementation of full-stack web development concepts, database management, API integration, CRUD operations, recommendation algorithms, and user-centric application design, making it a comprehensive User Defined Project for BCA Semester III.