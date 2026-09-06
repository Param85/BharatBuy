We'll create an **AI_DEVELOPMENT_GUIDE.md**, which will act as the **master blueprint** for Google AI Studio (or any coding AI). Instead of just telling it "build a website," we'll tell it **exactly** what to build, how to build it, what not to do, and in what order.

This document will be detailed enough that the AI can work module by module while maintaining consistency across the entire project.

---

# AI_DEVELOPMENT_GUIDE.md Structure

## 1. Project Information

* Project Name
* Author
* Project Description
* Objective
* Target Users
* Expected Outcome

---

## 2. Project Vision

Explain the purpose of the project.

Example:

> BharatBuy is an intelligent web application that helps users compare products from multiple online shopping platforms and recommends the best buying option using a weighted recommendation algorithm instead of simply displaying the cheapest product.

This tells the AI the **purpose**, not just the features.

---

## 3. Technology Stack

Everything will be fixed.

### Frontend

* HTML5
* CSS3
* Vanilla JavaScript

### Backend

* PHP 8

### Database

* MySQL

### Server

* XAMPP

### APIs

* SerpApi
* Amazon Product Advertising API
* eBay Browse API

No React.

No Laravel.

No Bootstrap.

No Node.js.

---

## 4. Overall Architecture

Explain

```
Browser

↓

Frontend

↓

PHP

↓

MySQL

↓

External APIs

↓

Recommendation Engine

↓

User Interface
```

---

## 5. Folder Structure

Explain every folder.

Example

```
api/
```

Purpose

Contains all API communication.

Never place UI code here.

---

```
includes/
```

Purpose

Contains

* Database connection

* Authentication

* Common functions

* Header

* Footer

---

```
ajax/
```

Purpose

Contains AJAX endpoints.

---

...and so on.

---

## 6. Database Design

Explain every table.

Not only the fields.

Also explain

Why it exists.

How it connects to other tables.

Example

```
users
```

Stores registered users.

Primary Key

```
id
```

Related to

```
wishlist.user_id

search_history.user_id

alerts.user_id
```

---

## 7. Complete User Flow

Literally explain

```
Homepage

↓

Register

↓

Login

↓

Dashboard

↓

Search Product

↓

Fetch APIs

↓

Normalize Data

↓

Recommendation Engine

↓

Comparison Page

↓

Wishlist

↓

Price Alert

↓

Chatbot

↓

Logout
```

---

## 8. Feature Specifications

This will be the largest chapter.

Every feature gets

Purpose

Inputs

Outputs

Validation

Database interaction

Expected UI

Example

---

### Wishlist

Purpose

Allow users to save products.

User can

* Add

* Remove

* View

Database

```
wishlist
```

Validation

Prevent duplicate entries.

---

Repeat this for

* Login

* Register

* Dashboard

* Product Search

* Search History

* Recommendation

* Chatbot

* Profile

Everything.

---

## 9. API Specifications

For every API

Explain

Request

↓

Response

↓

Normalization

↓

Storage

↓

Display

The AI will know exactly what to do.

---

## 10. Recommendation Algorithm

The AI shouldn't invent one.

We'll define it.

Example

```
Overall Score

=

40% Price

+

30% Rating

+

20% Reviews

+

10% Seller Reliability
```

We'll explain how to normalize values before calculating scores.

---

## 11. UI Design Rules

Explain

Colors

Spacing

Cards

Buttons

Tables

Icons

Typography

Everything.

---

## 12. Coding Standards

Tell AI

Always use

PDO

Prepared Statements

password_hash()

password_verify()

Fetch API

External CSS

External JavaScript

Never use inline SQL.

Never duplicate code.

---

## 13. Error Handling

Every possible error.

Example

API Failure

↓

Display

```
Unable to fetch products.

Please try again later.
```

No PHP fatal errors visible to users.

---

## 14. Security Rules

* SQL Injection protection

* XSS protection

* Session validation

* CSRF protection

* Password hashing

---

## 15. AI Rules

This is the most important part.

The AI will be instructed

Never change the folder structure.

Never change database names.

Never rename files.

Never replace technologies.

Never skip validation.

Always produce production-quality code.

Always comment complex functions.

Always keep code modular.

Always follow the project architecture.

---

## 16. Development Order

Exactly like

```
Phase 1

↓

Phase 2

↓

Phase 3

↓

...

↓

Phase 18
```

The AI will never jump ahead.

---

## 17. Acceptance Criteria

Every feature gets

Done means

Example

Login

✔ Registration works

✔ Password hashing

✔ Sessions created

✔ Logout works

✔ Unauthorized pages blocked

If all are true

Feature complete.

---

## 18. Future Enhancements

Voice Search

Price Prediction

AI Assistant

Browser Extension

Chrome Plugin

Mobile App

Admin Panel

Analytics

---

# What this document will achieve

Once this guide is complete, you can upload it alongside your other documentation (PRD, Architecture, Rules, Phases, Design) into Google AI Studio. The AI will have:

* A clear understanding of the project's purpose and architecture.
* Strict development rules to keep the implementation consistent.
* A defined technology stack and folder structure.
* A complete feature specification.
* A step-by-step development roadmap.

This is essentially the same approach used in professional software teams: a master development guide backed by focused supporting documents.

## One improvement I'd recommend

Since you're planning to use Google AI Studio extensively, we should also create a second document after this:

**`DATABASE.md`**

It would define **every table, every column, every relationship, data types, constraints, indexes, and sample data**. With that document plus the AI Development Guide, the AI can generate the database layer with far fewer mistakes and maintain consistency across the entire project. That combination will make the overall development process much smoother.
