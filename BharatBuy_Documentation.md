# BharatBuy - Project Documentation

This document contains the complete system documentation for the **BharatBuy** application, covering the project introduction, core objectives, problem statements, functionalities, and database table designs.

---

## 1. Introduction

### 1.1 Project Background and Context
The rapid expansion of the digital economy has transformed the retail landscape globally, and particularly in India. With initiatives like "Digital India," the widespread adoption of high-speed mobile internet, and the revolution of unified payment interfaces (UPI), e-commerce has shifted from a luxury to an everyday necessity. Millions of Indian consumers now purchase electronics, apparel, household items, and groceries online. 

However, this explosive growth has led to a highly fragmented market. The same product is frequently listed across multiple online shopping platforms—such as Amazon, eBay, Flipkart, and Walmart—at varying prices, shipping fees, delivery times, and seller terms. E-commerce portals employ dynamic pricing algorithms that adjust rates based on demand, search history, inventory levels, and competitor behavior. Consequently, consumers are faced with the daunting task of manually tracking and comparing prices to ensure they do not overpay, creating a significant cognitive load.

### 1.2 The Price Comparison Dilemma
Traditional price comparison websites only solve a fraction of this problem. They typically pull pricing data and sort listings from cheapest to most expensive. This approach is fundamentally flawed because it ignores crucial trust and quality signals. A product that appears cheap might have an extremely low customer rating, a small volume of reviews, high shipping costs, or be completely out of stock. 

In e-commerce, a purchase decision is based on a combination of **cost**, **quality**, and **reliability**. Without an aggregated tool that evaluates these factors collectively, consumers often fall victim to low-quality products or fraudulent listings. There is a clear need for an intelligent assistant that not only aggregates products but also analyzes metadata to recommend the overall best deal.

### 1.3 The "BharatBuy" Branded Identity
**BharatBuy** is designed specifically to fill this gap. Styled as a premium, localized, and intelligent shopping assistant, the name *BharatBuy* represents a tool created to empower Indian consumers to navigate e-commerce platforms confidently and make smart financial decisions. By integrating real-time web scrapers, a custom evaluation algorithm, and state-of-the-art AI, the application acts as a personal shopping consultant working on behalf of the buyer. It provides transparency, saves time, and helps families optimize their monthly household budgets.

### 1.4 Technological Scope & Innovation
From a software engineering perspective, BharatBuy is a full-stack web application designed on a three-tier model using PHP and MySQL. It introduces several key technical innovations:
1. **Dynamic Scraper Orchestrator**: Merges multi-source asynchronous queries, executes normalization scripts, and handles fallback mock sets smoothly when upstream APIs are throttled or offline.
2. **Value Score Recommendation Algorithm**: Applies mathematical weights to different product parameters to output a single, easy-to-understand value rating out of 100, highlighting the "Best Overall Deal."
3. **Automated Price Tracking Engine**: Monitors user-specified target thresholds and integrates with SMTP mail servers using Google App Passwords to deliver instant, real-time email alerts.
4. **Conversational LLM Integration**: Embeds the Google Gemini API to parse natural language shopping queries, suggest products, and provide a conversational user experience.
5. **Session Account Selector**: Redesigns the entry portal with a Gmail-style account selector to make profile switching secure and frictionless.

### 1.5 Target Audience and System Use-Cases
The application serves a wide demographic of users:
* **Budget-Conscious Shoppers**: Users looking to buy high-ticket electronics (laptops, phones) who want to wait for the absolute lowest price point.
* **Daily Consumers**: Shoppers comparing apparel or accessories who want to quickly verify seller ratings and discounts in one place.
* **Tech Enthusiasts**: Users who want to chat with an AI assistant to get instant model comparisons and technical recommendations.
* **Evaluation Committee**: Built cleanly and modularly, the project serves as an ideal academic submission for the Bachelor of Computer Applications (BCA) Semester III User Defined Project (UDP), demonstrating the practical application of full-stack CRUD, API handling, mail triggers, and database normalization.

---

## 2. Project Objectives

The primary objectives of the BharatBuy project are:

1. **Unified Search Aggregation**:
   To develop a centralized search interface that queries multiple active e-commerce platforms (like Amazon and eBay) simultaneously, eliminating the need for manual site-to-site searches. This mechanism saves the user from opening dozens of browser tabs by unifying raw API search results. The database acts as a central aggregator, normalizing various product data models into a clean, uniform list that is displayed instantly to the user.

2. **Dynamic Data Scraping & API Connections**:
   To build a robust web scraping and API connector orchestrator that retrieves real-time pricing, ratings, reviews, and stock availability of products. By utilizing structured cURL requests, API providers, and scraper fallbacks, the system guarantees up-to-the-minute product information. If live external APIs fail or are throttled, the engine utilizes a high-fidelity `MockProvider` fallback to maintain system functionality and prevent empty result pages.

3. **Intelligent Value Recommendation Scoring**:
   To implement a custom, weighted mathematical algorithm (Value Score out of 100) that ranks deals by overall value (balancing price, discount, rating, and review count) rather than just listing the cheapest option. This recommendation engine weighs product price at 40%, customer rating at 30%, and review count volume at 30%. It automatically tags the highest-scoring product as the "Best Overall Deal" at the top of the search grid, shielding users from buying cheap, low-rated items.

4. **Historical Price Tracking & Trend Charts**:
   To design a historical database logger that records product price variations over time, displaying them to the user via interactive trend charts. The database registers every price fluctuation captured during searches. The details page then formats this data and feeds it to Chart.js on the frontend, drawing a smooth, interactive line graph. If the product is brand new and has no history yet, the system draws a clean horizontal baseline matching the current live price to keep the user interface looking complete.

5. **Real-time Price Drop Alerts**:
   To enable users to set custom target price thresholds for any product, triggering automatic database trackers. Shoppers can input their desired purchase price on any product page. This registers a tracking hook in the `product_trackers` table. Whenever the system runs checks, it matches the target threshold against the current live price. If the price drops to or below the target, the trigger fires, log-notifies the user, and deactivates the tracker to prevent duplicate alerts.

6. **Automated SMTP Email Notifications**:
   To integrate secure SMTP mail delivery (via Gmail App Passwords) that instantly emails users when a tracked product's price drops to or below their target threshold. Powered by PHPMailer, the email system connects securely via TLS encryption to Gmail's servers using credentials stored safely in a `.env` file. It sets the character set encoding to UTF-8, ensuring that emojis (like 🔥) and special symbols in the subject line render cleanly on all mobile and desktop email clients.

7. **Conversational AI Chatbot**:
   To integrate a natural language processing assistant powered by the Google Gemini API to answer shopping queries, guide platform usage, and recommend search prompts. The chatbot is embedded globally across all pages and uses custom system prompt engineering. If the external Gemini API is unreachable or rate-limited, the system automatically falls back to local database keyword matching, ensuring the chat interface remains functional and responsive at all times.

8. **Secure User Session & Account Management**:
   To implement safe account creation, hashed passwords, session handling, and a personalized profile dashboard containing wishlist items and alert history. The system hashes user passwords using the secure `bcrypt` algorithm. It tracks active user sessions to protect dashboard access, and integrates a customized, Gmail-style account selector card on the login screen to allow quick, safe profile switching for saved local accounts.

9. **Premium Responsive User Interface**:
   To design a modern, visually stunning frontend using vanilla CSS that adapts seamlessly across mobile screens, tablets, and desktops. The UI utilizes custom CSS variables, elegant transitions, micro-animations (like a shaking notification bell), glassmorphism, and unified button styles. All grids use `auto-fill` alignments to prevent product cards from stretching awkwardly, ensuring a premium e-commerce look.

10. **Academic & Scalable Architecture**:
    To structure a clean three-tier PHP/MySQL architecture with modular code files, safe inputs, and robust database query handlers ready for academic evaluation. By separating database connections (`db.php`), configuration constraints (`config.php`), visual headers/footers, and AJAX endpoints, the project follows strict software engineering practices. All inputs are sanitized using PDO prepared statements to block SQL injection, meeting all criteria for a BCA Semester III User Defined Project.

---

## 3. Problem Statement

The development of the BharatBuy application directly addresses the following critical challenges faced by online consumers today:

1. **E-commerce Platform Fragmentation**:
   The online retail space is highly fragmented. To purchase a single product, consumers must manually visit multiple websites (such as Amazon, eBay, and Flipkart) to compare prices. This repetitive process leads to information overload, user fatigue, and a high amount of time wasted switching between platforms.

2. **The "Cheapest Price" Trap**:
   Most comparison tools only sort items by the lowest price. However, the cheapest option is often sold by a low-rated merchant, has poor customer reviews, high hidden shipping fees, or is out of stock. Consumers lack a balanced scoring system that evaluates the overall *value* of the deal, leading to buyer's remorse.

3. **Inability to Track Rapid Price Fluctuations**:
   E-commerce prices fluctuate continuously based on demand, stock levels, and promotional events. It is impossible for a user to manually monitor these price shifts, resulting in missed savings on high-ticket items.

4. **No Cross-Platform Competitor Price Drop Alerts**:
   Individual online stores only notify users about their own internal deals and will never alert a user if a competitor drops their price. Consumers lack a platform-agnostic tracker that monitors competitor pricing on their behalf.

5. **Scattered and Disorganized Wishlists**:
   Shoppers must maintain separate, isolated wishlists on multiple websites. There is no centralized place to organize saved items across different platforms, make side-by-side comparison calls, and manage price alerts.

6. **Lack of Instant, Interactive Shopping Assistance**:
   Traditional e-commerce websites offer static FAQ pages that do not help users solve active doubts or navigate complex features. Consumers lack a conversational assistant to answer questions about recommendations and platform functionality in real-time.

7. **Search Noise and Accessory Pollution**:
   When consumers search for a specific high-value product (such as a smartphone like "Realme P3x Pro" or "OnePlus 10T"), the search result pages on major e-commerce platforms are heavily polluted with unrelated low-value accessories like phone cases, tempered glass screen guards, charging cables, and sleeves. This "noise" obscures the actual product the user is looking to buy, making price comparison highly frustrating.

8. **Shared Device Login Friction in Households**:
   In typical Indian households, multiple family members often share a single computer or tablet to browse and shop online. Constantly logging out, re-typing email addresses, and entering credentials creates significant usability friction and leads to shared accounts, which compromises the personalization of wishlists, search history, and price alert logs.

9. **API Network Fragility and System Downtimes**:
   Traditional e-commerce comparison portals rely entirely on live external API calls to display listings. If an external API (like Amazon or eBay) is temporarily rate-limited, experiences network latency, or goes offline, the application completely crashes or displays blank, broken pages to the user. There is a lack of self-healing software structures that maintain search stability during network disruptions.

---

## 4. Core System Functionalities

The **BharatBuy** application offers a comprehensive suite of features, categorized into 10 key system functionalities:

1. **Secure User Authentication & Account Chooser**:
   Features standard hashed signup/login forms. It includes a custom, Gmail-style account chooser on the login screen that lists previously saved user profiles on the browser, allowing for quick one-click profile switching.

2. **Unified Search Engine & Scraper Orchestrator**:
   Normalizes search keywords (including raw URL conversions) and simultaneously queries multiple API/data providers (Amazon, eBay, Walmart, MockProvider). It automatically merges duplicates, selects the cheapest deal among identical listings, and saves all results to the database.

3. **Keyword Strictness Filtering**:
   Features a "Strict Mode" toggle in settings that filters out accessory noise (like cases, covers, protectors, sleeves) and model mismatches from the search results, ensuring only matching products are shown.

4. **Weighted Value Recommendation Engine**:
   Evaluates each aggregated deal and calculates a Value Score out of 100 based on a weighted formula including price ratios, star rating, total review volume, discount percentage, and stock status. It automatically badges and displays the highest-scoring deal as the "Best Overall Deal" at the top of the search grid.

5. **Interactive Price Trend Graphing**:
   Draws a beautiful, responsive line chart (powered by Chart.js) on the product details page, plotting price movements over time to help users decide if they should buy now or wait. It automatically draws a flat baseline at the current price if the product is new and has no history yet.

6. **Price Tracking & Dynamic Alerting**:
   Allows users to set a custom target alert price directly on the product details page. This auto-registers active database trackers to run background price checks during web scraping or page visits.

7. **Instant SMTP E-mail Alerts**:
   Uses PHPMailer to send real-time price drop email notifications to users (configured securely via Google App Passwords inside the `.env` configuration). It supports full UTF-8 subject line formatting for correct emoji rendering on mobile mail clients.

8. **Header Notifications Dropdown**:
   Features a Notification Bell (🔔) in the header navigation menu with an unread badge counter. Real-time alerts trigger a bell shake animation and automatically drop down the notification menu directly under the button to show the alert message.

9. **Centralized User Wishlist**:
   Allows users to bookmark their favorite items across multiple platforms in one centralized list. It includes auto-healing database fallbacks that automatically repair broken product links or legacy image paths directly upon page load.

10. **Global AI Chatbot (Google Gemini API)**:
    Embeds an interactive conversational AI helper on the bottom right of all pages. It answers queries, suggests shopping tips, guides users through site functionality, and recommends matching products using system prompt engineering.

---

## 5. Database Tables Design

The **BharatBuy** database is structured using 10 MySQL relational tables:

### 5.1 `users` Table
Stores registered shopper credentials and settings.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique identifier for each user |
| `full_name` | `VARCHAR(100)` | - | Full name of the user |
| `email` | `VARCHAR(150)` | **UNIQUE** | Registered email address (used for login and price drop alerts) |
| `password` | `VARCHAR(255)` | - | Securely hashed password (via `password_hash()`) |
| `alerts_enabled` | `TINYINT(1)` | - | Toggle for email notifications (1 = Enabled, 0 = Disabled) |
| `created_at` | `TIMESTAMP` | - | Date and time of user registration |

---

### 5.2 `product_prices` Table
Stores current product deals aggregated from e-commerce providers.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique identifier for each product deal |
| `product_name` | `VARCHAR(255)` | - | Full name / Title of the product |
| `website` | `VARCHAR(100)` | - | Source website (Amazon, eBay, Walmart, etc.) |
| `current_price` | `DECIMAL(10,2)` | - | Current listed price (in INR) |
| `original_price` | `DECIMAL(10,2)` | - | Original price before discount (in INR) |
| `discount_rate` | `INT` | - | Discount percentage (0-100) |
| `rating` | `DECIMAL(3,2)` | - | Average product customer rating (0.00 to 5.00) |
| `review_count` | `INT` | - | Total number of customer reviews |
| `image_url` | `TEXT` | - | Link to the product image |
| `product_url` | `TEXT` | - | Direct referral link to purchase on the merchant site |
| `description` | `TEXT` | - | Product description or key bullet points |
| `is_out_of_stock` | `TINYINT(1)` | - | Stock status (1 = Out of stock, 0 = In stock) |
| `category` | `VARCHAR(100)` | - | Product classification category (Laptops, Audio, Clothing, etc.) |
| `created_at` | `TIMESTAMP` | - | Timestamp when the product record was created |
| `updated_at` | `TIMESTAMP` | - | Auto-updated timestamp on modification |

---

### 5.3 `search_history` Table
Logs user search terms to show recent searches.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique search log ID |
| `user_id` | `INT` | **FOREIGN KEY** | Links to `users.id` (Cascades on delete) |
| `product_name` | `VARCHAR(255)` | - | Raw search query string typed by the user |
| `searched_at` | `TIMESTAMP` | - | Timestamp of search execution |

---

### 5.4 `wishlist` Table
Stores user-bookmarked items.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique wishlist item ID |
| `user_id` | `INT` | **FOREIGN KEY** | Links to `users.id` (Cascades on delete) |
| `product_name` | `VARCHAR(255)` | - | Name of the saved product |
| `website` | `VARCHAR(100)` | - | Source website of the product |
| `current_price` | `DECIMAL(10,2)` | - | Saved current price |
| `product_url` | `TEXT` | - | Purchase referral link |
| `image_url` | `TEXT` | - | Product image link (auto-repaired on load if broken) |
| `created_at` | `TIMESTAMP` | - | Timestamp when item was bookmarked |

---

### 5.5 `price_history` Table
Logs historical price variations to draw Chart.js line charts.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique log entry ID |
| `product_id` | `INT` | **FOREIGN KEY** | Links to `product_prices.id` (Cascades on delete) |
| `price` | `DECIMAL(10,2)` | - | Price value logged at that point in time |
| `recorded_at` | `TIMESTAMP` | - | Timestamp when the price check was recorded |

---

### 5.6 `product_trackers` Table
Manages active price drop trackers set by users.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique tracker ID |
| `user_id` | `INT` | **FOREIGN KEY** | Links to `users.id` (Cascades on delete) |
| `product_id` | `INT` | **FOREIGN KEY** | Links to `product_prices.id` (Cascades on delete) |
| `target_price` | `DECIMAL(10,2)` | - | Target threshold price input by the user |
| `is_active` | `TINYINT(1)` | - | Active toggle (1 = Tracking, 0 = Triggered and deactivated) |
| `created_at` | `TIMESTAMP` | - | Timestamp when the price alert was set |

---

### 5.7 `user_notifications` Table
Stores notifications displayed in the header dropdown list.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique notification entry ID |
| `user_id` | `INT` | **FOREIGN KEY** | Links to `users.id` (Cascades on delete) |
| `title` | `VARCHAR(150)` | - | Header title (e.g. "🔥 Price Drop Alert!") |
| `message` | `TEXT` | - | Short message describing the price drop |
| `product_id` | `INT` | **FOREIGN KEY** | Links to `product_prices.id` (Nulls on delete) |
| `created_at` | `TIMESTAMP` | - | Timestamp when the notification was fired |

---

### 5.8 `chatbot` Table
Stores predefined keyword-based FAQ responses for fallback chatbot matches.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique FAQ entry ID |
| `keyword` | `VARCHAR(150)` | - | Keyword trigger phrase (e.g. "how it works") |
| `answer` | `TEXT` | - | Pre-configured text answer matched to keyword |

---

### 5.9 `chatbot_logs` Table
Tracks Gemini API request metrics, response times, and connection errors.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique chatbot log entry ID |
| `user_id` | `INT` | **FOREIGN KEY** | Links to `users.id` (Nulls on delete) |
| `source` | `VARCHAR(20)` | - | API classification source ('gemini', 'fallback', 'rate_limit') |
| `response_time_ms` | `INT` | - | Round-trip response time of request in milliseconds |
| `error_message` | `TEXT` | - | Exception details if the API call failed |
| `created_at` | `TIMESTAMP` | - | Timestamp when the chatbot interaction occurred |

---

### 5.10 `provider_logs` Table
Tracks API statuses of e-commerce providers to track network success rates.
| Column Name | Data Type | Key Type | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | **PRIMARY KEY (Auto-Inc)** | Unique log ID |
| `provider_name` | `VARCHAR(100)` | - | Class name of the provider (e.g. `AmazonNinjaProvider`) |
| `status` | `INT` | - | HTTP Response code (200 = Success, 500 = Error) |
| `response_time_ms` | `INT` | - | Query execution response time |
| `error_message` | `TEXT` | - | Details of exception if request failed |
| `logged_at` | `TIMESTAMP` | - | Timestamp of logging |
