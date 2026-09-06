# BharatBuy Google Gemini API Chatbot Integration Document

This document outlines the architecture, setup instructions, validation flow, security measures, testing guide, and fallback mechanism for the production-quality Google Gemini API chatbot integration in the **BharatBuy** (formerly Smart Shopping Assistant) application.

---

## 1. How the Architecture Works

The chatbot uses a secure, decoupled, layered architecture:

```
chatbot.js (Frontend)
    ↓  [AJAX GET with product_id]
ajax/chatbot.php (Controller)
    ↓  [Performs Rate-Limiting, Sanitization, Selective Context & Memory Loading]
GeminiService.php (API Client)
    ↓  [cURL POST Payload]
Google Gemini API (Model: gemini-flash-latest)
```

* **Frontend (`js/chatbot.js`)**: Captures user queries, attaches active page references, locks UI inputs during processing, shows animated typing feedback, escapes HTML text to prevent XSS, and parses markdown layouts.
* **Controller (`ajax/chatbot.php`)**: Manages security, rate limiting, and database loading. If a query is shopping-related, it queries product lists, wishlist items, price trackers, and price histories. For general questions, it skips database operations to improve speed.
* **API Client (`includes/GeminiService.php`)**: Connects to the Google Gemini API using native PHP cURL, using the fast, reliable `gemini-flash-latest` model. Capped at 10 seconds total execution time and 5 seconds connection time to prevent backend blocking.

---

## 2. API Key & Constant Configuration

### Constant Declarations
Configure chatbot behavior parameters in [includes/config.php](file:///c:/xampp/htdocs/BharatBuy/includes/config.php):

```php
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AQ.Ab8...Key...');
define('CHAT_HISTORY_LIMIT', 15);      // Conversation memory length in turns (pairs of user/bot messages)
define('CHAT_RATE_LIMIT_MAX', 10);     // Max requests per window duration
define('CHAT_RATE_LIMIT_WINDOW', 60);  // Rate limiting time window in seconds
define('CHAT_INPUT_MAX_LENGTH', 1000); // Maximum query length in characters
```

*Note: In production environments, it is recommended to set a system environment variable `GEMINI_API_KEY` instead of hardcoding the value in PHP.*

---

## 3. Request Validation Flow

When a request arrives at `ajax/chatbot.php`, it undergoes several security and sanitization checks:

1. **UTF-8 Integrity**: Verifies query bytes represent valid UTF-8 structures.
2. **Size Constraints**: Rejects strings longer than 1000 characters.
3. **Empty Check**: Blocks whitespace-only or empty messages.
4. **Control Characters Block**: Filters invisible ASCII control characters (byte ranges `0x00`-`0x1F` and `0x7F`).
5. **Prompt Injection Guard**: Uses regular expression checks to block typical system override prompts (e.g. asking to ignore system commands, reveal keys, or print filesystem details), returning an immediate mock denial response.
6. **Rate-Limiting Check**: Evaluates timestamps of previous requests stored in the session. If user exceeds 10 queries per minute, a throttling warning is returned immediately.

---

## 4. Security & Content Safety Measures

* **API Key Protection**: Kept strictly server-side inside PHP context and never exposed.
* **SQL Injection Immunity**: Prepared statements are used for all queries on the `product_prices`, `product_trackers`, `wishlist`, `search_history`, and `chatbot_logs` tables.
* **XSS Defenses**: All Gemini outputs are HTML-escaped on the frontend before markdown transformations are executed. Preformatted HTML links from fallback database queries bypass escaping to preserve button renders.
* **Prompt Injection Defenses**: Explicit system prompts instruct the model to politely decline requests to reveal configuration keys, alter instructions, or assume other AI roles.
* **Content Safety**: Explicit system instructions filter out queries involving violence, self-harm, cyberattacks, phishing, database hacking, or malware.
* **Disclaimers**: Safety disclaimers are returned for health queries and finance suggestions.

---

## 5. Telemetry & Auditing Logs

All requests are logged to the `chatbot_logs` database table to track system health:

```sql
CREATE TABLE IF NOT EXISTS chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    source VARCHAR(20) NOT NULL, -- 'gemini', 'fallback', 'rate_limit', 'rejected'
    response_time_ms INT DEFAULT 0,
    error_message TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 6. Conversation Memory

Lightweight, session-based conversation memory is tracked inside `$_SESSION['chat_history']`. The controller loads and maintains a configurable memory buffer (the last `CHAT_HISTORY_LIMIT` turns). Old turns are sliced out automatically, enabling the assistant to understand follow-up questions (e.g., *"Why?"* in reference to a laptop comparison) while maintaining prompt efficiency.

---

## 7. Markdown & Formatting Layouts

The JS formatter transforms markdown responses from Gemini into responsive HTML:
* **Bold & Italic**: Formatted to `<b>` and `<i>`.
* **Bullet & Numbered Lists**: Formatted using `<ul>`/`<ol>` and `<li>` styles.
* **Inline Code**: Wrapped in border-accent code badges.
* **Multiline Code Blocks**: Formatted using styled dark-themed syntax blocks (`<pre><code>`) with overflow scroll support.
* **Tables**: Formats standard markdown table grids (`|`) into HTML tables with zebra-striping.
* **Links**: Converts `[text](url)` to safe anchor links.

---

## 8. Fallback Mechanism

If the API fails due to timeouts, rate limits, or network errors, it defaults to the rule-based chatbot:
1. Logs the exception and error details in the database log.
2. Executes rule-based lookup: queries database matching terms and recommends products with computed scores, or returns static keyword responses.
3. Sets `"source": "fallback"` in the JSON payload, prompting the frontend to render the response directly as HTML.

---

## 9. Integration Testing

Run automated tests via the XAMPP PHP runner:

```powershell
C:\xampp\php\php.exe C:\Users\DELL\.gemini\antigravity-ide\brain\86f3b54b-4578-4f17-805f-8eee0210033f\scratch\test_chatbot.php
```

Tests cover: file structures, API key settings, Gemini handshake output matching, database queries, and static rule matching fallback logic.
