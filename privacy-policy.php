<?php
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-container" style="max-width: 880px; margin: 0 auto; padding: 2.5rem 1.5rem 4rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 1.5rem;">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span class="current">Privacy Policy</span>
    </nav>

    <article style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 2.5rem; box-shadow: var(--shadow-sm); line-height: 1.7; color: var(--text);">
        <div class="legal-doc-header">
            <h1>Privacy Policy</h1>
            <p>Effective date: October 2026</p>
        </div>

        <section style="margin-bottom: 2rem;">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">1. Overview</h2>
            <p>BharatBuy ("we", "our", or "the platform") is an online shopping comparison and price tracking utility. We respect your privacy and are committed to protecting the personal data you share when using our services. This Privacy Policy describes how we collect, handle, and safeguard your information.</p>
        </section>

        <section style="margin-bottom: 2rem;">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">2. Information We Collect</h2>
            <p>We only collect information necessary to provide product comparison and notification services:</p>
            <ul style="padding-left: 1.5rem; margin: 0.75rem 0; display: flex; flex-direction: column; gap: 0.4rem;">
                <li><strong>Account Information:</strong> When you register an account, we collect your name, email address, and an encrypted password.</li>
                <li><strong>Price Trackers &amp; Wishlist:</strong> Products you actively save to your wishlist or select for target price drop notifications.</li>
                <li><strong>Search &amp; Navigation Preferences:</strong> Search queries submitted on the platform to display recent search history, along with client-side UI preferences such as preferred currency, language, and theme mode.</li>
            </ul>
        </section>

        <section style="margin-bottom: 2rem;">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">3. How We Use Your Information</h2>
            <p>Your information is used strictly to deliver and improve our comparison services:</p>
            <ul style="padding-left: 1.5rem; margin: 0.75rem 0; display: flex; flex-direction: column; gap: 0.4rem;">
                <li>Sending transactional email notifications when a tracked product drops below your specified target price.</li>
                <li>Displaying personalized price conversions and localized language settings across user sessions.</li>
                <li>Authenticating user sessions and safeguarding account security.</li>
                <li>Aggregating anonymous analytics to optimize search performance and multi-store crawling reliability.</li>
            </ul>
        </section>

        <section style="margin-bottom: 2rem;">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">4. Third-Party Marketplaces &amp; Outbound Links</h2>
            <p>BharatBuy indexes publicly available product listings and pricing data from third-party e-commerce retailers, including Amazon India, eBay Global, Flipkart, Croma, and others. When you click an external link to view or purchase a product, you are redirected to the respective third-party retailer's website. We do not control and are not responsible for the privacy practices, tracking mechanisms, or data collection policies of external merchants.</p>
        </section>

        <section style="margin-bottom: 2rem;">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">5. Local Storage and Cookies</h2>
            <p>We use standard session cookies for secure user authentication. We also use browser local storage (<code>localStorage</code>) to preserve your interface preferences (light/dark appearance, chosen currency, and language) locally on your device without transmitting these preferences to third-party advertisers.</p>
        </section>

        <section style="margin-bottom: 2rem;">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">6. Data Retention &amp; User Rights</h2>
            <p>You have full control over your stored data on BharatBuy:</p>
            <ul style="padding-left: 1.5rem; margin: 0.75rem 0; display: flex; flex-direction: column; gap: 0.4rem;">
                <li>You can remove items from your wishlist or cancel active price trackers at any time directly through your dashboard.</li>
                <li>Passwords are securely stored using one-way cryptographic hashing (bcrypt) and cannot be retrieved in plaintext.</li>
                <li>You can request account deletion or data clarification by contacting our platform administrator.</li>
            </ul>
        </section>

        <section>
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem;">7. Updates to This Policy</h2>
            <p>We may update this Privacy Policy periodically to reflect enhancements in our platform architecture or applicable regulatory requirements. Updates will be published directly on this page with a revised effective date.</p>
        </section>
    </article>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
