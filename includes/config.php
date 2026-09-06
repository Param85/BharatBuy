<?php
// Load .env file from the project root if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            $value = trim($value, '"\'');
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

define('APP_NAME', 'BharatBuy');
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_shopping_assistant');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change if you have a password


// Chatbot Configuration Constants
define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? $_SERVER['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE');
define('CHAT_HISTORY_LIMIT', 15);      // Conversation memory length in turns (pairs of user/bot messages)
define('CHAT_RATE_LIMIT_MAX', 10);     // Max requests per window duration
define('CHAT_RATE_LIMIT_WINDOW', 60);  // Rate limiting time window in seconds
define('CHAT_INPUT_MAX_LENGTH', 1000); // Maximum query length in characters

session_start();
