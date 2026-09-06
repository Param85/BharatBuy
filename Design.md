# Design Document

# BharatBuy

**Prepared By:** Param P. Shah

**Course:** Bachelor of Computer Applications (BCA) – Semester III  
**Project Type:** User Defined Project (UDP)

---

# 1. Introduction

This document defines the visual identity and user experience guidelines for the **BharatBuy** web application. The goal is to create a modern, clean, and intuitive interface that enables users to compare products and navigate the application with ease.

The design philosophy focuses on **simplicity**, **readability**, and **accessibility**, ensuring that information such as prices, ratings, and recommendations is easy to understand at a glance.

---

# 2. Design Goals

The application should:

- Have a modern and professional appearance.
- Be easy to navigate for all users.
- Prioritize readability and clarity.
- Present comparison data in an organized manner.
- Maintain visual consistency across all pages.
- Be responsive on desktop, tablet, and mobile devices.

---

# 3. Design Theme

**Theme Name:** Modern Tech Minimal

The interface will use a clean layout with soft colors, rounded components, subtle shadows, and generous spacing to create a professional shopping assistant experience.

Design characteristics:

- Minimalistic
- Professional
- User-friendly
- Lightweight
- Responsive
- Accessible

---

# 4. Color Palette

## Primary Color

**Royal Blue**

```
#2563EB
```

Used for:

- Buttons
- Navigation Bar
- Links
- Icons
- Active Elements

---

## Secondary Color

**Sky Blue**

```
#60A5FA
```

Used for:

- Hover Effects
- Cards
- Badges
- Highlights

---

## Accent Color

**Emerald Green**

```
#10B981
```

Used for:

- Recommended Product
- Success Messages
- Price Drop Indicators
- Positive Status

---

## Warning Color

**Amber**

```
#F59E0B
```

Used for:

- Notifications
- Warnings
- Pending Status

---

## Error Color

**Red**

```
#EF4444
```

Used for:

- Error Messages
- Invalid Input
- Failed Requests

---

## Background Color

```
#F8FAFC
```

---

## Card Background

```
#FFFFFF
```

---

## Primary Text

```
#1E293B
```

---

## Secondary Text

```
#64748B
```

---

## Border Color

```
#E2E8F0
```

---

# 5. Typography

## Primary Font

**Poppins**

Used for:

- Headings
- Navigation
- Buttons
- Cards

---

## Secondary Font

**Inter**

Used for:

- Paragraphs
- Product Details
- Tables
- Forms

---

## Font Sizes

| Element | Size |
|----------|------|
| Main Heading | 36px |
| Section Heading | 28px |
| Sub Heading | 22px |
| Card Title | 18px |
| Body Text | 16px |
| Small Text | 14px |
| Labels | 13px |

---

# 6. Layout Style

The application will use a centered layout with a maximum content width to improve readability.

```
---------------------------------------
 Navigation Bar
---------------------------------------

 Sidebar (Optional) | Main Content

---------------------------------------

 Footer
---------------------------------------
```

Spacing should remain consistent throughout the application.

---

# 7. Navigation Bar

The navigation bar should include:

- Logo
- Home
- Dashboard
- Compare Products
- Wishlist
- Search History
- Chatbot
- Profile
- Logout

Features:

- Sticky navigation
- Hover animations
- Active page indicator

---

# 8. Buttons

Buttons should have:

- Rounded corners
- Soft shadows
- Smooth hover animations
- Clear labels

Primary Button

- Blue background
- White text

Secondary Button

- White background
- Blue border

Danger Button

- Red background

Success Button

- Green background

---

# 9. Cards

Cards will be used extensively for:

- Products
- Wishlist
- Dashboard Statistics
- User Profile
- Search Results

Each card should include:

- Rounded corners
- Soft shadow
- Padding
- Hover effect
- Consistent spacing

---

# 10. Forms

All forms should have:

- Rounded input fields
- Floating or top-aligned labels
- Input validation messages
- Focus highlights
- Clear submit buttons

Forms include:

- Login
- Registration
- Product Search
- Profile Update
- Price Alert

---

# 11. Dashboard Design

The dashboard should display:

- Welcome Banner
- Quick Statistics
- Recent Searches
- Wishlist Summary
- Price Alerts
- Recommended Products

Statistics cards may include:

- Total Searches
- Saved Products
- Active Alerts
- Recently Compared Products

---

# 12. Product Comparison Page

The comparison page is the core of the application.

Display:

- Product Image
- Product Name
- Price
- Rating
- Number of Reviews
- Seller
- Availability
- Recommendation Score
- Buy Link

Recommended products should be visually highlighted using a green badge.

Example:

```
⭐ Best Overall

Recommendation Score

96/100
```

---

# 13. Recommendation Badge

Use color coding.

Green

```
Best Overall
```

Blue

```
Best Value
```

Orange

```
Highest Rated
```

Gray

```
Budget Option
```

---

# 14. Search Bar

The search bar should be:

- Large
- Centered
- Rounded
- Responsive

Include:

- Search Icon
- Placeholder Text

Example

```
Search for any product...
```

---

# 15. Tables

Comparison tables should include:

- Zebra striping
- Hover highlighting
- Responsive layout
- Sticky header (optional)

Columns:

- Website
- Price
- Rating
- Reviews
- Availability
- Recommendation

---

# 16. Chatbot Design

The chatbot should appear as a floating widget.

Features:

- Floating chat button
- Slide-in chat window
- User messages on the right
- Bot responses on the left
- Scrollable conversation
- Typing indicator (optional)

---

# 17. Icons

Use **Font Awesome** icons.

Examples:

🏠 Home

🔍 Search

❤️ Wishlist

📈 Price History

🔔 Alerts

🤖 Chatbot

👤 Profile

🚪 Logout

---

# 18. Animations

Use subtle animations only.

Examples:

- Button hover
- Card hover
- Fade-in sections
- Smooth scrolling
- Loading spinner
- Search loading animation

Avoid excessive animations that distract users.

---

# 19. Responsive Design

The application should support:

Desktop

- Full navigation
- Multi-column layout

Tablet

- Responsive cards
- Collapsible navigation

Mobile

- Hamburger menu
- Single-column layout
- Touch-friendly buttons
- Scrollable comparison tables

---

# 20. Empty States

Provide informative messages when no data is available.

Examples:

Wishlist

```
Your wishlist is empty.

Start saving products to compare them later.
```

Search History

```
No recent searches found.
```

Search Results

```
No products found.

Try searching with different keywords.
```

---

# 21. Notifications

Notification types:

Success

- Product added successfully.

Information

- Searching products...

Warning

- API temporarily unavailable.

Error

- Unable to retrieve product information.

---

# 22. Loading Screens

Display loading indicators during:

- Login
- Registration
- Product Search
- API Requests
- Recommendation Calculation

Use animated spinners or skeleton loading cards.

---

# 23. Accessibility

The interface should:

- Maintain sufficient color contrast.
- Support keyboard navigation.
- Use descriptive button labels.
- Include alternative text for images.
- Display readable font sizes.

---

# 24. Visual Consistency

Maintain consistency in:

- Colors
- Fonts
- Buttons
- Cards
- Icons
- Shadows
- Border radius
- Spacing
- Page layout

---

# 25. Future Design Enhancements

Future versions may include:

- Dark Mode
- Custom Themes
- Glassmorphism UI
- Animated Product Cards
- Interactive Price Charts
- Voice Search Interface
- AI Chat Assistant Theme
- Personalized Dashboard Widgets

---

# 26. Overall Design Philosophy

The BharatBuy should feel like a professional product comparison platform rather than a traditional college project. Every design element should guide users toward making informed purchasing decisions while keeping the interface clean, intuitive, and visually appealing.

The final design should balance aesthetics with usability, ensuring that users can quickly compare products, understand recommendations, and navigate the application without unnecessary complexity.