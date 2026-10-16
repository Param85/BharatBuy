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

## BharatBuy — Design, UX & Production Quality Guardrails

Use these rules whenever creating, modifying, refactoring, or reviewing anything in the BharatBuy project.

### 1. Avoid the "AI-generated / vibe-coded" look
BharatBuy should look like a deliberately designed, production-ready product, not an AI-generated template.
**Never use:**
- Purple or generic AI-style gradients
- Excessive gradients used only for visual decoration
- Pill-shaped buttons unless the shape has a genuine UX purpose
- Fake reviews, testimonials, ratings, or customer quotes
- Fake metrics, statistics, sales numbers, user counts, or revenue figures
- Fake customer counters
- Vague, generic, or exaggerated hero copy
- Emoji icons as a substitute for proper UI icons
- Overly decorative UI that does not serve a functional purpose
- Excessive rounded cards or "everything is a card" layouts
- Cursor-following effects or cursor animations
- Unnecessary typing effects
- Excessive glassmorphism
- Over-the-top scroll animations
- Animation added merely to make the site appear sophisticated

### 2. Copy must be specific and credible
Do not generate generic AI marketing copy. Write concise copy that communicates exactly what BharatBuy does, who it is for, and why a particular feature matters.
Every piece of visible content should have a purpose.
**Never invent:** Customers, Reviews, Testimonials, Partnerships, Certifications, Awards, Statistics, Business claims, Product claims, Performance claims.
If real information is unavailable, use neutral placeholders or omit the section rather than fabricating information.

### 3. Images and visual assets
Do not use obvious AI-generated or AI-slop imagery.
Prefer, in this order:
1. Real product photography/assets provided by the project
2. Properly licensed stock photography when genuinely necessary
3. Clean UI/product illustrations created specifically for the interface
4. Simple typography, layout, and whitespace when an image is unnecessary
Do not add decorative images simply to fill empty space. Images must support the product, not make the website look like a generic AI landing-page template.

### 4. Icons
Use a consistent professional icon system. Do not replace icons with random emojis (🛒, ⭐, 🔥, 🚀, 💰, etc.) unless an emoji is explicitly part of the product's intended content. Icons should have consistent sizing, stroke/fill treatment, alignment, and visual weight.

### 5. Animation and interaction
Animations must communicate state, hierarchy, or feedback. Prefer subtle transitions over attention-seeking effects.
**Do not use:**
- Cursor animations or cursor-following elements
- Excessive parallax
- Large entrance animations on every section
- Constant floating/bouncing elements
- Overly elaborate scroll-triggered animations
- Animation that delays access to content
- Animation that negatively affects performance or usability
When in doubt, use no animation.

### 6. Buttons and components
Buttons should communicate clear actions. Avoid unnecessarily pill-shaped buttons. Use shape, spacing, hierarchy, typography, and contrast to establish a visual system. Do not make every component look like a floating rounded card. Components should feel like cohesive parts of the same product.

### 7. Preserve BharatBuy's existing identity
When modifying the project:
- Inspect the existing design system before introducing new styles.
- Reuse existing components, tokens, typography, spacing, colors, and patterns where appropriate.
- Do not unnecessarily redesign working sections.
- Do not introduce a completely different visual language for a new page.
- Do not remove existing functionality merely to simplify implementation.
- Do not replace working components with generic AI-generated alternatives.
- Maintain consistency across desktop, tablet, and mobile.
Any new feature should feel native to BharatBuy.

### 8. Production-readiness checklist
BharatBuy is **not considered launch-ready** until the following are addressed:
- Custom production domain is connected
- Favicon is configured
- Page titles and metadata are configured appropriately
- No "Made with AI" / AI-builder branding remains
- Privacy Policy page exists
- Terms & Conditions page exists
- Navigation links work
- Buttons and CTAs work
- Forms have proper validation and error states
- Broken links are removed
- Empty states are handled
- Loading states are handled where necessary
- Error states are handled where necessary
- Responsive layouts work correctly
- No placeholder content remains unintentionally
- No fake reviews or fake metrics remain
- No development/debug UI is exposed to users
- No unnecessary console errors remain
- Images and assets load correctly
- Accessibility basics are respected
- SEO fundamentals are configured where relevant

### 9. Accuracy over speed
Do not make assumptions when implementing functionality.
Before changing code:
1. Inspect the relevant existing implementation.
2. Understand how the feature currently works.
3. Identify dependencies and side effects.
4. Make the smallest appropriate change.
5. Verify that existing functionality still works.
6. Check the affected UI at different viewport sizes.
7. Check for obvious console/runtime errors.
Do not claim something is implemented, fixed, tested, or production-ready unless it has actually been verified.

### 10. No fabricated completeness
Never hide missing functionality behind polished UI. If a backend integration, API, payment system, authentication flow, database operation, email service, or external dependency is not actually connected, do not present it as fully functional. Use an explicit appropriate state instead of pretending the functionality exists.

### 11. Final review before considering a task complete
Before finishing any task, perform a quick quality pass:
- **Design**: Intentionally designed vs AI-generated? Spacing, typography, colors, components consistent? No unnecessary gradients, pills, cards, animations?
- **Content**: Claims factual? No invented reviews, metrics, customers, partnerships? Specific copy?
- **Functionality**: Interactions work? Existing features intact? Loading/empty/error/success states handled?
- **Production**: No builder/AI branding? Favicon, metadata, policies present? No broken links or console errors?
- **Responsive UX**: Verified on desktop and mobile viewports?

### Core Principle
**Build BharatBuy like a real product, not like an AI-generated demo.**
Prefer **clarity → consistency → functionality → credibility → polish** over **visual novelty → excessive animation → decorative effects → generic marketing copy**.
When there is a conflict between making something look impressive and making it accurate, usable, maintainable, and credible, always prioritize accuracy, usability, maintainability, and credibility.
