# Rules Document

# BharatBuy

**Prepared By:** Param P. Shah

**Course:** Bachelor of Computer Applications (BCA) – Semester III  
**Project Type:** User Defined Project (UDP)

---

# 1. Purpose

This document defines the development rules, coding standards, constraints, and best practices that must be followed throughout the development of the **BharatBuy** project.

The objective is to ensure that all generated code remains consistent, maintainable, secure, and compatible with the technologies chosen for the project.

---

# 2. Technology Constraints

The AI must only use the following technologies unless explicitly instructed otherwise.

## Frontend

- HTML5
- CSS3
- Vanilla JavaScript (ES6)

---

## Backend

- PHP 8+

---

## Database

- MySQL

---

## Server

- XAMPP (Apache + MySQL)

---

## External APIs

- SerpApi
- Amazon Product Advertising API
- eBay Browse API

---

## Version Control

- Git
- GitHub

---

# 3. Libraries and Framework Rules

## Allowed

- Fetch API
- PHP cURL
- PHP PDO
- Chart.js (for price history visualization)
- Font Awesome
- Google Fonts

---

## Avoid

The AI should NOT use:

- React
- Angular
- Vue
- Laravel
- Symfony
- Node.js
- Express.js
- Bootstrap (unless specifically requested)
- jQuery (unless specifically requested)
- Firebase
- MongoDB
- NoSQL databases

The project must remain a traditional PHP + MySQL web application suitable for BCA Semester III.

---

# 4. Coding Standards

## HTML

- Use semantic HTML5 elements.
- Maintain proper indentation.
- Use meaningful IDs and class names.
- Ensure accessibility where possible.

---

## CSS

- Keep styles modular.
- Avoid inline CSS.
- Use external stylesheets.
- Maintain consistent spacing and naming conventions.

---

## JavaScript

- Use Vanilla JavaScript only.
- Prefer Fetch API over XMLHttpRequest.
- Use meaningful variable and function names.
- Separate logic into reusable functions.
- Avoid global variables whenever possible.

---

## PHP

- Organize code into reusable files.
- Use PDO for database interactions.
- Use prepared statements for all SQL queries.
- Separate business logic from presentation logic.
- Reuse helper functions where appropriate.

---

## SQL

- Normalize database tables.
- Use primary keys and foreign keys appropriately.
- Avoid duplicate data.
- Use appropriate data types.

---

# 5. Project Structure Rules

The AI must maintain the following folder organization.

```
BharatBuy/

api/
ajax/
css/
js/
images/
uploads/
includes/
database/
```

Files should only be placed in their appropriate directories.

---

# 6. Authentication Rules

The AI must:

- Hash passwords using `password_hash()`.
- Verify passwords using `password_verify()`.
- Use PHP sessions for authentication.
- Restrict access to protected pages.
- Redirect unauthorized users to the login page.

The AI must NOT:

- Store plain-text passwords.
- Store passwords in JavaScript.
- Expose session data unnecessarily.

---

# 7. Database Rules

The AI must:

- Use PDO.
- Use prepared statements.
- Validate all user inputs.
- Handle SQL errors gracefully.

The AI must NOT:

- Concatenate SQL queries directly.
- Use deprecated MySQL functions.
- Allow SQL injection vulnerabilities.

---

# 8. API Integration Rules

The AI must:

- Keep each API integration in a separate PHP file.
- Normalize data returned from different APIs before comparison.
- Handle API failures gracefully.
- Validate API responses before use.

The AI must NOT:

- Hardcode API keys in public files.
- Assume API responses are always valid.
- Crash the application if one API fails.

If one API fails, the application should continue displaying results from the remaining APIs.

---

# 9. Recommendation Engine Rules

The recommendation engine must consider multiple factors.

Default weights:

- Price
- Rating
- Review Count
- Seller Reliability

The algorithm should remain modular so weights can be adjusted easily.

The AI should never recommend a product solely because it is the cheapest.

---

# 10. CRUD Rules

Every CRUD module should follow the same pattern.

Create

- Validate input
- Insert data
- Return success message

Read

- Retrieve data
- Handle empty results

Update

- Validate ownership
- Update record
- Return confirmation

Delete

- Confirm existence
- Delete safely
- Return confirmation

---

# 11. Error Handling Rules

The AI must:

- Handle all expected errors gracefully.
- Display user-friendly error messages.
- Log technical errors when appropriate.
- Prevent application crashes.

Example:

Instead of

```
Fatal Error
```

Display

```
Unable to retrieve product information.
Please try again later.
```

---

# 12. Security Rules

The AI must implement:

- Password hashing
- Prepared statements
- Input sanitization
- Output escaping
- Session validation
- CSRF protection where applicable
- XSS prevention

The AI must never expose:

- Database credentials
- API keys
- Internal server paths
- Stack traces

---

# 13. UI/UX Rules

The interface should be:

- Clean
- Modern
- Responsive
- Consistent
- Easy to navigate

Pages should have:

- Consistent navigation
- Uniform color scheme
- Proper spacing
- Responsive layouts
- Clear typography

---

# 14. Performance Rules

The AI should:

- Minimize unnecessary API requests.
- Reuse database connections.
- Avoid duplicate queries.
- Optimize loops.
- Keep page loading times reasonable.

---

# 15. Chatbot Rules

The chatbot should:

- Answer predefined shopping-related questions.
- Help users navigate the application.
- Explain recommendations.
- Handle unknown questions politely.

The chatbot should not:

- Generate misleading information.
- Fabricate product prices.
- Pretend to know unavailable data.

---

# 16. Documentation Rules

Every PHP file should include:

- Purpose
- Author (optional)
- Description

Complex functions should include explanatory comments.

Variable names should be descriptive.

---

# 17. Code Quality Rules

The AI should generate code that is:

- Readable
- Modular
- Reusable
- Well-commented
- Consistently formatted
- Easy for beginner and intermediate PHP developers to understand

Avoid overly complex programming techniques that are unnecessary for a BCA Semester III project.

---

# 18. Future Compatibility Rules

The project should be structured so that future enhancements can be added without major architectural changes.

Examples:

- Additional shopping APIs
- AI-powered chatbot
- Email notifications
- Browser extension
- Mobile application
- Machine learning recommendation engine

---

# 19. AI Development Guidelines

The AI should:

- Generate complete and working code whenever possible.
- Explain complex logic when necessary.
- Follow the established folder structure.
- Keep modules independent and reusable.
- Prioritize maintainability over unnecessary complexity.

The AI should not:

- Introduce technologies outside the approved stack.
- Modify unrelated project files without reason.
- Generate placeholder code when a functional implementation is feasible.
- Skip validation or security practices.
- Assume API responses without checking for errors.

---

# 20. Final Development Principles

Throughout the development of BharatBuy, the AI must adhere to the following principles:

- Simplicity over unnecessary complexity.
- Security before convenience.
- Maintainability over shortcuts.
- Consistency across all modules.
- Modular architecture for easy expansion.
- User-friendly design and interactions.
- Robust error handling and graceful degradation.
- Clear, documented, and production-style code suitable for academic evaluation.

These rules serve as the foundation for developing a secure, scalable, and maintainable BharatBuy application while remaining fully aligned with the project's technology stack and educational objectives.