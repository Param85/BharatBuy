# Development Phases Document

# BharatBuy

**Prepared By:** Param P. Shah

**Course:** Bachelor of Computer Applications (BCA) – Semester III  
**Project Type:** User Defined Project (UDP)

---

# 1. Introduction

This document divides the BharatBuy project into multiple development phases. Each phase builds upon the previous one, ensuring that the application remains functional throughout development while gradually introducing new features.

The phases are designed to follow a logical progression, from setting up the project structure to implementing advanced functionalities such as multi-source product comparison and chatbot assistance.

---

# Phase 1 – Project Setup & Foundation

## Objective

Establish the project's foundation and prepare the development environment.

### Tasks

- Install and configure XAMPP
- Create MySQL database
- Create project folder structure
- Configure database connection
- Create reusable header and footer
- Create navigation bar
- Create homepage
- Create configuration files
- Initialize Git repository (optional)

### Deliverables

- Running project on localhost
- Working database connection
- Organized project structure

---

# Phase 2 – User Authentication

## Objective

Implement secure user authentication.

### Features

- User Registration
- Login
- Logout
- Session Management
- Password Hashing
- Authentication Middleware
- Protected Pages

### Database

- Users Table

### Deliverables

Users can securely create accounts, log in, and access protected pages.

---

# Phase 3 – Dashboard & User Profile

## Objective

Create a personalized dashboard after successful login.

### Features

- Welcome Dashboard
- User Profile
- Edit Profile
- Change Password
- Profile Picture (Optional)

### Deliverables

Each user has a personalized dashboard with account information.

---

# Phase 4 – Product Search

## Objective

Allow users to search for products.

### Features

- Search Bar
- Search Validation
- AJAX Product Search
- Display Loading Indicator
- Handle Empty Searches

### Deliverables

Users can search for products through the interface.

---

# Phase 5 – API Integration

## Objective

Fetch product information from external shopping platforms.

### APIs

- SerpApi
- Amazon Product Advertising API
- eBay Browse API

### Features

- API Requests
- JSON Parsing
- Error Handling
- Response Validation

### Deliverables

Product information is successfully retrieved from multiple sources.

---

# Phase 6 – Data Normalization

## Objective

Convert product information from different APIs into a common format.

### Features

Normalize:

- Product Name
- Price
- Rating
- Review Count
- Seller
- Availability
- Product Image
- Product URL

### Deliverables

A unified product structure regardless of API source.

---

# Phase 7 – Product Comparison

## Objective

Display products side by side for comparison.

### Features

Comparison Table

Display:

- Product Name
- Website
- Price
- Rating
- Reviews
- Availability
- Seller

### Deliverables

Users can compare multiple shopping websites on a single page.

---

# Phase 8 – Recommendation Engine

## Objective

Recommend the best product using multiple evaluation factors.

### Features

Recommendation Score based on:

- Price
- Rating
- Review Count
- Seller Reliability

Display

- Best Overall
- Cheapest Option
- Highest Rated

### Deliverables

The system intelligently recommends products instead of simply displaying prices.

---

# Phase 9 – Wishlist (CRUD)

## Objective

Allow users to save products.

### Features

Create

- Save Product

Read

- View Wishlist

Update

- Edit Personal Notes

Delete

- Remove Product

### Deliverables

Complete Wishlist CRUD functionality.

---

# Phase 10 – Search History (CRUD)

## Objective

Store and manage user search history.

### Features

- Save Every Search
- View Search History
- Delete Individual Searches
- Clear Entire History

### Deliverables

Users can manage their previous searches.

---

# Phase 11 – Price Tracking

## Objective

Track product prices over time.

### Features

- Store Product Prices
- Maintain Price History
- Display Previous Prices
- Show Price Changes

### Deliverables

Historical price information for searched products.

---

# Phase 12 – Price Alerts

## Objective

Allow users to monitor desired prices.

### Features

- Set Target Price
- Edit Alert
- Delete Alert
- Notify User During Search

### Deliverables

Users receive notifications when products meet their desired price.

---

# Phase 13 – Chatbot Assistance

## Objective

Provide in-app assistance through a chatbot.

### Features

- Frequently Asked Questions
- Product Search Help
- Navigation Assistance
- Recommendation Explanation

### Deliverables

Working chatbot integrated into the application.

---

# Phase 14 – User Interface Enhancement

## Objective

Improve the application's appearance and usability.

### Features

- Responsive Layout
- Improved Dashboard
- Better Product Cards
- Icons
- Animations
- Loading Indicators
- Empty State Screens

### Deliverables

A polished and user-friendly interface.

---

# Phase 15 – Security Improvements

## Objective

Strengthen application security.

### Features

- Input Validation
- Prepared Statements
- Password Hashing
- Session Validation
- XSS Prevention
- CSRF Protection
- Error Handling

### Deliverables

A secure application following web development best practices.

---

# Phase 16 – Testing & Bug Fixing

## Objective

Ensure the application works correctly across all modules.

### Testing Areas

- Registration
- Login
- Dashboard
- Product Search
- API Responses
- Recommendation Engine
- Wishlist
- Search History
- Price Tracking
- Chatbot

### Deliverables

Stable and bug-free application.

---

# Phase 17 – Documentation

## Objective

Prepare complete project documentation.

### Documents

- Project Requirements Document (PRD)
- Architecture Document
- Rules Document
- Development Phases
- Database Design
- API Documentation
- User Manual
- Installation Guide

### Deliverables

Complete project documentation.

---

# Phase 18 – Final Project Review

## Objective

Prepare the project for submission and demonstration.

### Checklist

- Verify all features
- Review folder structure
- Optimize code
- Remove unused files
- Backup database
- Test on fresh XAMPP installation
- Final UI review

### Deliverables

Submission-ready project.

---

# Overall Development Roadmap

```
Phase 1
Project Setup
        │
        ▼
Phase 2
Authentication
        │
        ▼
Phase 3
Dashboard & Profile
        │
        ▼
Phase 4
Product Search
        │
        ▼
Phase 5
API Integration
        │
        ▼
Phase 6
Data Normalization
        │
        ▼
Phase 7
Product Comparison
        │
        ▼
Phase 8
Recommendation Engine
        │
        ▼
Phase 9
Wishlist (CRUD)
        │
        ▼
Phase 10
Search History (CRUD)
        │
        ▼
Phase 11
Price Tracking
        │
        ▼
Phase 12
Price Alerts
        │
        ▼
Phase 13
Chatbot
        │
        ▼
Phase 14
UI Enhancement
        │
        ▼
Phase 15
Security
        │
        ▼
Phase 16
Testing
        │
        ▼
Phase 17
Documentation
        │
        ▼
Phase 18
Final Review
```

---

# Milestones

| Milestone | Description |
|------------|-------------|
| Milestone 1 | Project setup completed |
| Milestone 2 | Authentication system completed |
| Milestone 3 | Dashboard operational |
| Milestone 4 | Product search functional |
| Milestone 5 | Multi-source API integration completed |
| Milestone 6 | Product comparison operational |
| Milestone 7 | Recommendation engine implemented |
| Milestone 8 | Wishlist and search history completed |
| Milestone 9 | Price tracking and alerts implemented |
| Milestone 10 | Chatbot integrated |
| Milestone 11 | UI finalized |
| Milestone 12 | Testing completed |
| Milestone 13 | Documentation completed |
| Milestone 14 | Project ready for submission |

---

# Conclusion

The BharatBuy project is divided into eighteen structured development phases, allowing each module to be designed, implemented, tested, and refined independently. This phased approach reduces complexity, promotes modular development, and ensures that the application remains functional at every stage.

By following this roadmap, the project evolves from a simple authentication system into a complete full-stack web application featuring multi-source product comparison, intelligent recommendations, user personalization, price tracking, and chatbot assistance. This approach also makes it easier to debug, maintain, and extend the application in the future while aligning with the learning objectives of a BCA Semester III User Defined Project.