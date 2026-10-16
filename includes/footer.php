    </main>
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Col 1: Brand Info -->
                <div class="footer-col brand-col">
                    <a href="index.php" class="footer-logo">
                        <img src="assets/images/logo.png?v=3" alt="BharatBuy" style="height: 38px; width: auto; object-fit: contain;">
                    </a>
                    <p class="footer-desc">
                        Independent price discovery and deal tracking across leading online retailers in India and worldwide.
                    </p>
                </div>

                <!-- Col 2: Navigation -->
                <div class="footer-col">
                    <h3 class="footer-heading">Explore</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="dashboard.php">Search Dashboard</a></li>
                        <li><a href="category.php?cat=mobiles">Mobiles</a></li>
                        <li><a href="category.php?cat=electronics">Electronics</a></li>
                        <li><a href="category.php?cat=fashion">Fashion</a></li>
                        <?php if(isLoggedIn()): ?>
                            <li><a href="wishlist.php">Saved Wishlist</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Col 3: Legal & Policies -->
                <div class="footer-col">
                    <h3 class="footer-heading">Legal &amp; Trust</h3>
                    <ul class="footer-links">
                        <li><a href="privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="terms-and-conditions.php">Terms &amp; Conditions</a></li>
                    </ul>
                </div>

                <!-- Col 4: Platform Features -->
                <div class="footer-col">
                    <h3 class="footer-heading">Platform</h3>
                    <ul class="footer-links">
                        <li><span class="footer-feature-item"><i class="fa-solid fa-store" style="color: var(--primary);"></i> Multi-Store Search</span></li>
                        <li><span class="footer-feature-item"><i class="fa-solid fa-bell" style="color: var(--primary);"></i> Price Drop Alerts</span></li>
                        <li><span class="footer-feature-item"><i class="fa-solid fa-coins" style="color: var(--primary);"></i> Real-Time FX Conversion</span></li>
                        <li><span class="footer-feature-item"><i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> Transparent Value Score</span></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> BharatBuy. All rights reserved.</p>
                <p class="footer-disclaimer">Product names, trademarks, and merchant logos are the property of their respective owners.</p>
            </div>
    <!-- 4. Back to Top Button -->
    <button id="back-to-top-btn" aria-label="Back to top" title="Back to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- 2. Simple Cookie Banner -->
    <div id="bb-cookie-banner" class="bb-cookie-banner" role="dialog" aria-label="Cookie consent notice">
        <div class="bb-cookie-header">
            <i class="fa-solid fa-cookie-bite"></i>
            <span>Privacy &amp; Cookie Preferences</span>
        </div>
        <p class="bb-cookie-text">
            BharatBuy uses strictly necessary session cookies to maintain your currency preferences, saved trackers, and authentication state. We never sell personal data or use invasive third-party ad trackers.
        </p>
        <div class="bb-cookie-actions">
            <button type="button" id="cookie-accept-all" class="btn btn-sm btn-primary">Accept All</button>
            <button type="button" id="cookie-accept-essential" class="btn btn-sm btn-secondary">Essential Only</button>
            <a href="privacy-policy.php" style="font-size: 0.8rem; color: var(--text-muted); text-decoration: underline; margin-left: auto;">Privacy Policy</a>
        </div>
    </div>

    <!-- 17. Accessible Confirmation Modal -->
    <div id="bb-confirm-modal" class="bb-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="bb-modal-title">
        <div class="bb-modal-card">
            <div class="bb-modal-header">
                <div class="bb-modal-icon"><i class="fa-solid fa-circle-question" id="bb-modal-icon"></i></div>
                <h3 class="bb-modal-title" id="bb-modal-title">Confirm Action</h3>
            </div>
            <div class="bb-modal-body" id="bb-modal-message">Are you sure you want to proceed?</div>
            <div class="bb-modal-actions">
                <button type="button" class="btn btn-secondary" id="bb-modal-cancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="bb-modal-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Global Platform Utilities & Chatbot Scripts -->
    <script src="js/platform-features.js?v=<?= time(); ?>"></script>
    <script src="js/chatbot.js?v=<?= time(); ?>"></script>
</body>
</html>
