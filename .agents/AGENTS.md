# BharatBuy Custom Rules

This file documents the workspace rules and guidelines learned during development.

## PHP Environment Configurations (.env)
- **Always Overwrite Server Variables**: When parsing `.env` files manually, always overwrite the variables in `$_ENV`, `$_SERVER`, and via `putenv()`. Do not skip loading keys if they already exist in `$_SERVER`, as local web servers (like Apache/XAMPP) may pre-populate placeholder keys that will block custom user settings from being loaded.

## Email Operations (PHPMailer)
- **UTF-8 Character Set**: Always set `$mail->CharSet = 'UTF-8';` when using PHPMailer to prevent emojis or non-ASCII characters in subjects or headers from displaying as garbled text (e.g., `formatted as ðŸ”¥`).

## Notification Systems
- **Notification Synchronization**: Avoid race conditions where client-side toast notifications auto-deactivate tracker records in the database before the backend mail engine can process them. Ensure the deactivation script (`deactivate_tracker.php`) triggers the email notification before marking the tracker inactive.
- **Hoisting / Scoping Guard**: Always declare JavaScript DOM elements and array data variables at the top of the file before they are referenced in callback functions or execution loops.

## Text-to-Speech (TTS) & Chatbot Intelligence
- **Preserve Numbers During Emoji Sanitization**: In JavaScript text-to-speech sanitization, never use `\p{Emoji_Component}` in regex replacements. In the Unicode specification, ASCII digits `0-9`, `#`, and `*` have `Emoji_Component=Yes` (for keycap sequences `0️⃣`-`9️⃣`), which inadvertently deletes all numeric characters. Use `\p{Extended_Pictographic}` instead.
- **Market & Temporal Grounding**: Always dynamically inject the current year and date into LLM chatbot system instructions, explicitly instructing the model to treat currently active product generations (e.g., iPhone 17 series) as officially released and available products rather than unreleased rumors.

