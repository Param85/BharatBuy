<?php 
require_once __DIR__ . '/includes/header.php'; 
?>

<!-- Link to Homepage Dedicated Stylesheet (Emerald Ink & Champagne Theme) -->
<link rel="stylesheet" href="css/home.css?v=<?= time(); ?>">

<!-- 1. Hero Section -->
<section class="home-hero">
    <div class="home-hero-inner">
        <!-- Headline (Retaining #hero-title for multi-language switcher) -->
        <h1 id="hero-title" class="home-hero-title">
            Compare. Analyze.<br>
            <span class="hero-highlight">Shop Smarter.</span>
        </h1>

        <!-- Subtitle (Retaining #hero-desc) -->
        <p id="hero-desc" class="home-hero-desc">
            Compare real-time prices across Amazon, eBay, and Flipkart. Track price drops and discover authentic savings before you buy.
        </p>


        <!-- Hero Actions (Retaining #hero-btn for multi-language compatibility) -->
        <div class="hero-cta-row">
            <?php if(!isLoggedIn()): ?>
                <a id="hero-btn" href="register.php" class="btn-emerald-primary">
                    Get Started Free <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </a>
                <a href="#how-it-works" class="btn-champagne-secondary">
                    <i class="fa-solid fa-circle-play" style="color: #06433B;"></i> How It Works
                </a>
            <?php else: ?>
                <a id="hero-btn" href="dashboard.php" class="btn-emerald-primary">
                    Go to Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </a>
                <a href="#live-demo" class="btn-champagne-secondary">
                    <i class="fa-solid fa-sliders" style="color: #06433B;"></i> See Price Matrix
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 1.5 Recommended Categories Swipeable Scroller -->
<section class="category-carousel-section" aria-label="Recommended Categories">
    <div class="category-carousel-header">
        <div class="category-header-title">
            <div class="category-header-title-row">
                <h2>Recommended Categories</h2>
            </div>
            <span class="category-header-subtitle">Swipe or scroll to explore curated real-time multi-store price comparisons</span>
        </div>
        <div class="category-nav-arrows">
            <button type="button" class="category-arrow-btn" id="catScrollLeft" aria-label="Scroll categories left">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="category-arrow-btn" id="catScrollRight" aria-label="Scroll categories right">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="category-track-wrapper">
        <div class="category-track" id="categoryTrack">
            <a href="fashion.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div class="category-name">Fashion</div>
                <span class="category-tag">Apparel &amp; Shoes</span>
            </a>

            <a href="mobiles.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <div class="category-name">Mobiles</div>
                <span class="category-tag">Flagships &amp; 5G</span>
            </a>

            <a href="electronics.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-laptop"></i>
                </div>
                <div class="category-name">Electronics</div>
                <span class="category-tag">Laptops &amp; Gadgets</span>
            </a>

            <a href="beauty.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <div class="category-name">Beauty</div>
                <span class="category-tag">Glow &amp; Wellness</span>
            </a>

            <a href="home.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-couch"></i>
                </div>
                <div class="category-name">Home</div>
                <span class="category-tag">Decor &amp; Kitchen</span>
            </a>

            <a href="toys.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-shapes"></i>
                </div>
                <div class="category-name">Toys</div>
                <span class="category-tag">Games &amp; LEGO</span>
            </a>

            <a href="appliances.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-blender"></i>
                </div>
                <div class="category-name">Appliances</div>
                <span class="category-tag">Smart Kitchen &amp; AC</span>
            </a>

            <a href="groceries.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <div class="category-name">Groceries</div>
                <span class="category-tag">Essentials &amp; Daily</span>
            </a>

            <a href="sports.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-dumbbell"></i>
                </div>
                <div class="category-name">Sports</div>
                <span class="category-tag">Fitness &amp; Workout</span>
            </a>

            <a href="books.php" class="category-card">
                <div class="category-icon-box">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="category-name">Books</div>
                <span class="category-tag">Bestsellers &amp; Novels</span>
            </a>
        </div>
    </div>

</section>

<!-- 2. Core Platform Capabilities Highlights -->
<section class="trust-metrics-section" aria-label="Core Capabilities">
    <div class="metrics-card">
        <div class="metric-item">
            <div class="metric-number"><i class="fa-solid fa-store" style="font-size: 1.5rem; color: #06433B;"></i></div>
            <div class="metric-label">Multi-Store Product Search</div>
        </div>
        <div class="metric-item">
            <div class="metric-number"><i class="fa-solid fa-scale-balanced" style="font-size: 1.5rem; color: #06433B;"></i></div>
            <div class="metric-label">Weighted Value Scoring Engine</div>
        </div>
        <div class="metric-item">
            <div class="metric-number"><i class="fa-solid fa-bell" style="font-size: 1.5rem; color: #06433B;"></i></div>
            <div class="metric-label">Automated Price Drop Alerts</div>
        </div>
        <div class="metric-item">
            <div class="metric-number"><i class="fa-solid fa-coins" style="font-size: 1.5rem; color: #06433B;"></i></div>
            <div class="metric-label">Real-Time Currency &amp; Localization</div>
        </div>
    </div>
</section>

<!-- 3. Supported Marketplaces Marquee -->
<section class="retailers-strip-section">
    <div class="retailers-heading">Scans & Aggregates Real-Time Data From Top Marketplaces</div>
    <div class="retailers-grid">
        <div class="retailer-badge"><i class="fa-brands fa-amazon" style="color: #FF9900;"></i> Amazon India</div>
        <div class="retailer-badge"><i class="fa-brands fa-ebay" style="color: #E53238;"></i> eBay Global</div>
        <div class="retailer-badge"><i class="fa-solid fa-bag-shopping" style="color: #2874F0;"></i> Flipkart</div>
        <div class="retailer-badge"><i class="fa-solid fa-plug" style="color: #00E676;"></i> Croma Retail</div>
        <div class="retailer-badge"><i class="fa-solid fa-mobile-screen" style="color: #E21B23;"></i> Reliance Digital</div>
        <div class="retailer-badge"><i class="fa-solid fa-tags" style="color: #9C27B0;"></i> Tata CLiQ</div>
    </div>
</section>

<!-- 4. Interactive Live Comparison Simulation Showcase -->
<section id="live-demo" class="showcase-section">
    <div class="section-header-center">
        <h2 class="section-title">See BharatBuy Intelligence in Action</h2>
        <p class="section-subtitle">Our weighted scoring engine evaluates merchant prices, seller credibility ratings, and discount depth in real-time to find the best available offer.</p>
    </div>

    <!-- Category Tabs -->
    <div class="showcase-tabs" role="tablist" aria-label="Product Showcase Categories">
        <button type="button" class="showcase-tab active" role="tab" aria-selected="true" onclick="switchShowcase('phone')"><i class="fa-solid fa-mobile"></i> Smartphones</button>
        <button type="button" class="showcase-tab" role="tab" aria-selected="false" onclick="switchShowcase('laptop')"><i class="fa-solid fa-laptop"></i> Laptops</button>
        <button type="button" class="showcase-tab" role="tab" aria-selected="false" onclick="switchShowcase('audio')"><i class="fa-solid fa-headphones"></i> Audio</button>
        <button type="button" class="showcase-tab" role="tab" aria-selected="false" onclick="switchShowcase('console')"><i class="fa-solid fa-gamepad"></i> Gaming</button>
    </div>

    <!-- Simulated Comparison Card -->
    <div class="comparison-showcase-card" id="showcaseContainer">
        <!-- Left Column: Product Info -->
        <div class="product-preview-pane">
            <div class="preview-badge-row">
                <span class="preview-badge preview-badge-success" id="showcaseBadge"><i class="fa-solid fa-check"></i> Highest Value Score</span>
            </div>
            <img id="showcaseImg" src="https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&q=80" alt="Apple iPhone 16 Pro" class="product-preview-img">
            <h3 class="preview-product-name" id="showcaseTitle">Apple iPhone 16 Pro (128GB, Natural Titanium)</h3>
            <div class="preview-rating-row" id="showcaseRating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-stroke"></i>
                <span style="color: var(--text); font-weight: 700; margin-left: 0.3rem;">4.8</span>
                <span style="color: var(--text-muted); font-size: 0.82rem;">(3,410 verified reviews)</span>
            </div>
        </div>

        <!-- Right Column: Cross-Platform Prices -->
        <div>
            <div class="matrix-heading-row">
                <div>
                    <h3 class="matrix-title">Multi-Store Price Matrix</h3>
                    <p class="matrix-subtitle">Real-time normalized comparison across authorized retailers</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                    <button type="button" class="btn-copy" data-copy="<?= htmlspecialchars('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/BharatBuy/index.php#live-demo'); ?>" title="Copy link to this live comparison">
                        <i class="fa-regular fa-copy"></i> Share
                    </button>
                    <!-- 18. Last Updated Date Badge -->
                    <span class="matrix-live-badge"><i class="fa-solid fa-circle-dot"></i> Live Rates &middot; Verified <?= date('M d, Y'); ?></span>
                </div>
            </div>

            <div class="matrix-table-header" aria-hidden="true">
                <span>Retailer &amp; Fulfillment</span>
                <span>Live Price &amp; Savings</span>
            </div>

            <div class="deals-preview-list" id="showcaseDealsList">
                <!-- Injected via JavaScript -->
            </div>

            <!-- Best Deal Callout -->
            <div class="best-deal-recommendation-bar" id="showcaseRecommendation">
                <div class="recommendation-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div class="recommendation-text" id="showcaseRecText">
                    <strong>BharatBuy Recommendation:</strong> Buy from <strong>Amazon India</strong> to save <strong>₹7,000</strong>. Value Score: <strong>96/100</strong> (Top-rated seller, fastest delivery).
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. How It Works Workflow Section -->
<section id="how-it-works" class="workflow-section">
    <div class="section-header-center">
        <h2 class="section-title">How BharatBuy Saves You Money</h2>
        <p class="section-subtitle">No more opening 20 browser tabs. Find authentic deals and protect your budget with three simple steps.</p>
    </div>

    <div class="workflow-steps">
        <!-- Step 1 -->
        <div class="workflow-step">
            <div class="workflow-step-num">01</div>
            <div class="workflow-step-body">
                <h3>Universal Multi-Search</h3>
                <p>Type any gadget name or paste a direct product link from Amazon or eBay. Our distributed crawler initializes simultaneously across all connected providers.</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="workflow-step">
            <div class="workflow-step-num">02</div>
            <div class="workflow-step-body">
                <h3>Weighted Scoring</h3>
                <p>We normalize foreign exchange rates (USD to INR), verify merchant feedback ratings, and weigh discount depth to compute an unbiased Best Deal score.</p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="workflow-step">
            <div class="workflow-step-num">03</div>
            <div class="workflow-step-body">
                <h3>Automated Price Tracker</h3>
                <p>Set a target budget price. BharatBuy continuously checks price fluctuations in the background and sends an email the moment it drops.</p>
            </div>
        </div>
    </div>
</section>

<!-- 6. Features Grid (Preserving IDs: feature-title-1/2/3 for translations) -->
<section class="features-bento-section">
    <div class="section-header-center">
        <h2 class="section-title">Built for Modern Smart Shoppers</h2>
        <p class="section-subtitle">Every feature is designed to eliminate buyer regret and maximize your purchasing power.</p>
    </div>

    <div class="bento-grid">
        <!-- Card 1: Smart Value Scoring -->
        <div class="bento-card">
            <h3 id="feature-title-1"><i class="fas fa-chart-line" style="color: var(--primary); margin-right: 0.5rem;"></i> Smart Value Scoring</h3>
            <p id="feature-desc-1">Our weighted engine evaluates merchant reputation, pricing differences, and historical trends to determine optimal purchase value.</p>
        </div>

        <!-- Card 2: Multi-Site Integration -->
        <div class="bento-card">
            <h3 id="feature-title-2"><i class="fas fa-shield-alt" style="color: var(--primary); margin-right: 0.5rem;"></i> Multi-Site Integration</h3>
            <p id="feature-desc-2">Aggregate product results from major e-commerce platforms like Amazon and eBay dynamically in a single interface.</p>
        </div>

        <!-- Card 3: Search Tracking -->
        <div class="bento-card">
            <h3 id="feature-title-3"><i class="fas fa-history" style="color: var(--primary); margin-right: 0.5rem;"></i> Search Tracking</h3>
            <p id="feature-desc-3">Review your search logs and access previous product comparisons instantly without re-typing search keywords.</p>
        </div>

        <!-- Card 4: Price Drop Tracking Alerts -->
        <div class="bento-card">
            <h3><i class="fa-solid fa-bell" style="color: var(--primary); margin-right: 0.5rem;"></i> Automated Price Alerts</h3>
            <p>Save products to your watchlist with custom target prices. Receive automatic high-priority email alerts with direct one-click purchase links.</p>
        </div>

        <!-- Card 5: Currency & International Localization -->
        <div class="bento-card">
            <h3><i class="fa-solid fa-globe" style="color: var(--primary); margin-right: 0.5rem;"></i> Dynamic Currency &amp; Localization</h3>
            <p>Toggle currencies between INR (₹), USD ($), EUR (€), and GBP (£) with live conversion, alongside 5 international languages.</p>
        </div>
    </div>
</section>

<!-- 7. Gemini AI Shopping Assistant Spotlight -->
<section class="ai-spotlight-section">
    <div class="ai-spotlight-card">
        <div class="ai-card-content">
            <h3>Ask Your Personal AI Shopping Advisor</h3>
            <p>Confused between multiple variants or technical specifications? Chat with our integrated assistant right in the corner for instant advice, pros/cons, and buying guides.</p>
            <button type="button" class="btn-champagne-secondary" onclick="openChatbotWidget()" style="cursor: pointer;">
                <i class="fa-solid fa-comments" style="color: #06433B;"></i> Ask Shopping Assistant
            </button>
        </div>

        <div class="ai-chat-preview-box">
            <div class="chat-bubble-user">
                Should I buy iPhone 16 Pro or Samsung S24 Ultra for photography?
            </div>
            <div class="chat-bubble-bot">
                <i class="fa-solid fa-robot" style="color: #F8E7C9; margin-right: 0.35rem;"></i>
                <strong>BharatBuy AI:</strong> The <strong>S24 Ultra</strong> wins on optical zoom (up to 100x Space Zoom and dedicated 5x telephoto). However, the <strong>iPhone 16 Pro</strong> delivers superior 4K120fps video and faster shutter response for candid portraits. Check the live comparison table above for current store discounts!
            </div>
        </div>
    </div>
</section>

<!-- 19. Frequently Asked Questions (Expandable FAQ) -->
<section class="faq-section" aria-label="Frequently Asked Questions">
    <div class="section-header-center">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <p class="section-subtitle">Everything you need to know about how BharatBuy discovers genuine deals and automates price tracking.</p>
    </div>

    <div class="faq-container">
        <details class="faq-item" open>
            <summary class="faq-question">
                <span>How does BharatBuy compare prices across Amazon, eBay, and Flipkart?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </summary>
            <div class="faq-answer">
                <p>BharatBuy runs distributed background crawlers and official provider APIs to scan products simultaneously. We normalize prices across currencies (USD to INR), verify delivery timelines, and compute an objective Value Score to identify genuine offers.</p>
            </div>
        </details>

        <details class="faq-item">
            <summary class="faq-question">
                <span>Are price drop alerts and wishlist tracking free of charge?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </summary>
            <div class="faq-answer">
                <p>Yes. You can save items to your personal watchlist and configure budget targets for free. When an authorized retailer drops their price to or below your target, our system immediately sends an automated email notification with direct purchase links.</p>
            </div>
        </details>

        <details class="faq-item">
            <summary class="faq-question">
                <span>Does BharatBuy support real-time foreign currency conversion?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </summary>
            <div class="faq-answer">
                <p>Yes. In the Settings dropdown, you can switch between INR (₹), USD ($), EUR (€), and GBP (£). Foreign merchant listings like eBay Global are dynamically converted using live bank rates so you see your true landed cost.</p>
            </div>
        </details>

        <details class="faq-item">
            <summary class="faq-question">
                <span>How is the BharatBuy "Value Score" calculated?</span>
                <i class="fa-solid fa-chevron-down faq-icon"></i>
            </summary>
            <div class="faq-answer">
                <p>Our scoring model evaluates net price (50%), merchant seller rating &amp; verified reviews (25%), delivery speed (15%), and discount depth compared to 30-day historical averages (10%).</p>
            </div>
        </details>
    </div>
</section>

<!-- 8. Final High-Conversion CTA Banner -->
<section class="cta-banner-section">
    <div class="cta-banner-card">
        <h2>Ready to Compare Prices Before You Buy?</h2>
        <p>Search across top online stores, review price trends, and receive automated alerts the moment prices drop.</p>
        <div style="display: flex; justify-content: center; gap: 1.25rem; flex-wrap: wrap;">
            <?php if(!isLoggedIn()): ?>
                <a href="register.php" class="btn-emerald-primary">
                    Create Free Account <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </a>
                <a href="login.php" class="btn-champagne-secondary">
                    Sign In
                </a>
            <?php else: ?>
                <a href="dashboard.php" class="btn-emerald-primary">
                    Open Search Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Interactive Scripts for the Homepage -->
<script>
// Trending Search Trigger
function searchTrending(keyword) {
    const input = document.getElementById('homeSearchInput');
    if (input) {
        input.value = keyword;
        document.getElementById('homeSearchForm').submit();
    }
}

// Open Chatbot trigger
function openChatbotWidget() {
    const triggerBtn = document.getElementById('chatbot-toggle-btn') || document.querySelector('.chatbot-trigger');
    if (triggerBtn) {
        triggerBtn.click();
    } else {
        alert('Click the assistant icon in the bottom right corner of the screen!');
    }
}

// Interactive Simulation Showcase Data
const SHOWCASE_DATA = {
    phone: {
        badge: '<i class="fa-solid fa-check"></i> Highest Value Score',
        img: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&q=80',
        title: 'Apple iPhone 16 Pro (128GB, Natural Titanium)',
        ratingStars: 4.8,
        reviewsCount: '3,410',
        deals: [
            { store: 'Amazon India', icon: 'fa-brands fa-amazon', color: '#FF9900', price: '₹1,19,900', status: 'In Stock · Free 1-Day Delivery', isBest: true, tag: 'Lowest Price (Save ₹7,000)' },
            { store: 'Flipkart', icon: 'fa-solid fa-bag-shopping', color: '#2874F0', price: '₹1,24,900', status: 'In Stock · Bank Offer Available', isBest: false, tag: '+ ₹5,000' },
            { store: 'Croma Retail', icon: 'fa-solid fa-plug', color: '#00E676', price: '₹1,26,900', status: 'Available in local stores', isBest: false, tag: '+ ₹7,000' }
        ],
        recommendation: '<strong>BharatBuy Recommendation:</strong> Buy from <strong>Amazon India</strong> to save <strong>₹7,000</strong>. Value Score: <strong>96/100</strong> (Top-rated merchant, fastest delivery, official Apple warranty).'
    },
    laptop: {
        badge: '<i class="fa-solid fa-bolt"></i> Best Value Recommendation',
        img: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&q=80',
        title: 'Apple MacBook Air 13" (M3 Chip, 16GB RAM, 512GB SSD)',
        ratingStars: 4.9,
        reviewsCount: '1,890',
        deals: [
            { store: 'Flipkart', icon: 'fa-solid fa-bag-shopping', color: '#2874F0', price: '₹1,24,990', status: 'Super Saver · In Stock', isBest: true, tag: 'Best Deal (Save ₹10,000)' },
            { store: 'Amazon India', icon: 'fa-brands fa-amazon', color: '#FF9900', price: '₹1,32,900', status: 'Prime Shipping Available', isBest: false, tag: '+ ₹7,910' },
            { store: 'Reliance Digital', icon: 'fa-solid fa-mobile-screen', color: '#E21B23', price: '₹1,34,900', status: 'Standard Delivery', isBest: false, tag: '+ ₹9,910' }
        ],
        recommendation: '<strong>BharatBuy Recommendation:</strong> <strong>Flipkart</strong> offers the lowest net price with flat bank discount. Value Score: <strong>94/100</strong>.'
    },
    audio: {
        badge: '<i class="fa-solid fa-tag"></i> 30-Day Low Price',
        img: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80',
        title: 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones',
        ratingStars: 4.7,
        reviewsCount: '5,120',
        deals: [
            { store: 'Amazon India', icon: 'fa-brands fa-amazon', color: '#FF9900', price: '₹26,990', status: 'Lightning Deal · 31% Off', isBest: true, tag: 'Lowest in 30 Days' },
            { store: 'Croma Retail', icon: 'fa-solid fa-plug', color: '#00E676', price: '₹29,990', status: 'Store Pickup Available', isBest: false, tag: '+ ₹3,000' },
            { store: 'Tata CLiQ', icon: 'fa-solid fa-tags', color: '#9C27B0', price: '₹31,490', status: 'Standard Delivery', isBest: false, tag: '+ ₹4,500' }
        ],
        recommendation: '<strong>BharatBuy Recommendation:</strong> <strong>Amazon</strong> has dropped to its 30-day low of <strong>₹26,990</strong>. Value Score: <strong>98/100</strong>.'
    },
    console: {
        badge: '<i class="fa-solid fa-gamepad"></i> High Demand',
        img: 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=400&q=80',
        title: 'Sony PlayStation 5 Slim Console (1TB Digital Edition)',
        ratingStars: 4.8,
        reviewsCount: '8,430',
        deals: [
            { store: 'Amazon India', icon: 'fa-brands fa-amazon', color: '#FF9900', price: '₹44,990', status: 'Direct Official Sony Distributor', isBest: true, tag: 'Official Sony Bundle' },
            { store: 'Flipkart', icon: 'fa-solid fa-bag-shopping', color: '#2874F0', price: '₹45,990', status: 'Authorized Retailer', isBest: false, tag: '+ ₹1,000' },
            { store: 'eBay Global', icon: 'fa-brands fa-ebay', color: '#E53238', price: '₹48,500', status: 'International Import', isBest: false, tag: '+ ₹3,510' }
        ],
        recommendation: '<strong>BharatBuy Recommendation:</strong> Buy from <strong>Amazon India</strong> with zero import risk and instant 1-year Sony India warranty. Value Score: <strong>95/100</strong>.'
    }
};

function switchShowcase(categoryKey) {
    // Switch active tab styling & ARIA state
    const tabs = document.querySelectorAll('.showcase-tab');
    tabs.forEach(tab => {
        const isTarget = tab.getAttribute('onclick') && tab.getAttribute('onclick').includes("'" + categoryKey + "'");
        if (isTarget) {
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
        } else {
            tab.classList.remove('active');
            tab.setAttribute('aria-selected', 'false');
        }
    });

    const data = SHOWCASE_DATA[categoryKey];
    if (!data) return;

    const listContainer = document.getElementById('showcaseDealsList');
    if (listContainer) {
        listContainer.innerHTML = `
            <div class="skeleton-card" style="margin-bottom: 0.65rem; padding: 0.95rem;">
                <div style="display: flex; gap: 0.85rem; align-items: center;">
                    <div class="skeleton skeleton-avatar"></div>
                    <div style="flex: 1;">
                        <div class="skeleton skeleton-title" style="height: 0.95rem; width: 35%;"></div>
                        <div class="skeleton skeleton-text" style="height: 0.75rem; width: 55%; margin: 0;"></div>
                    </div>
                    <div class="skeleton" style="width: 75px; height: 1.4rem; border-radius: 4px;"></div>
                </div>
            </div>
            <div class="skeleton-card" style="padding: 0.95rem;">
                <div style="display: flex; gap: 0.85rem; align-items: center;">
                    <div class="skeleton skeleton-avatar"></div>
                    <div style="flex: 1;">
                        <div class="skeleton skeleton-title" style="height: 0.95rem; width: 35%;"></div>
                        <div class="skeleton skeleton-text" style="height: 0.75rem; width: 55%; margin: 0;"></div>
                    </div>
                    <div class="skeleton" style="width: 75px; height: 1.4rem; border-radius: 4px;"></div>
                </div>
            </div>
        `;
    }

    setTimeout(() => {
        // Update product preview
        document.getElementById('showcaseBadge').innerHTML = data.badge;
        document.getElementById('showcaseImg').src = data.img;
        document.getElementById('showcaseTitle').innerText = data.title;
        document.getElementById('showcaseRating').innerHTML = `
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star-half-stroke"></i>
            <span style="color: var(--text); font-weight: 700; margin-left: 0.3rem;">${data.ratingStars}</span>
            <span style="color: var(--text-muted); font-size: 0.82rem;">(${data.reviewsCount} merchant ratings)</span>
        `;

        // Render deals
        listContainer.innerHTML = '';

        data.deals.forEach(deal => {
            const row = document.createElement('div');
            row.className = 'deal-item-row' + (deal.isBest ? ' best-deal' : '');
            row.innerHTML = `
                <div class="deal-store-col">
                    <div class="deal-store-icon" style="color: ${deal.color};">
                        <i class="${deal.icon}"></i>
                    </div>
                    <div class="deal-store-info">
                        <h4>${deal.store}</h4>
                        <span>${deal.status}</span>
                    </div>
                </div>
                <div class="deal-price-col">
                    <div class="deal-price-val ${deal.isBest ? 'highlight' : ''}">${deal.price}</div>
                    <span class="deal-tag-pill ${deal.isBest ? 'tag-best' : 'tag-diff'}">${deal.tag}</span>
                </div>
            `;
            listContainer.appendChild(row);
        });

        // Update recommendation text
        document.getElementById('showcaseRecText').innerHTML = data.recommendation;
    }, 140);
}

// Initialize showcase with default 'phone' category
document.addEventListener('DOMContentLoaded', function() {
    switchShowcase('phone');

    // Category Carousel Swiper & Moving Horizontal Line Logic
    const catTrack = document.getElementById('categoryTrack');
    const catLeftBtn = document.getElementById('catScrollLeft');
    const catRightBtn = document.getElementById('catScrollRight');

    if (catTrack) {
        // Arrow button navigation
        if (catLeftBtn) {
            catLeftBtn.addEventListener('click', function() {
                catTrack.scrollBy({ left: -260, behavior: 'smooth' });
            });
        }
        if (catRightBtn) {
            catRightBtn.addEventListener('click', function() {
                catTrack.scrollBy({ left: 260, behavior: 'smooth' });
            });
        }

        // Desktop mouse drag-to-swipe handling
        let isDown = false;
        let startX = 0;
        let initialScrollLeft = 0;
        let dragged = false;

        catTrack.addEventListener('mousedown', function(e) {
            isDown = true;
            dragged = false;
            startX = e.pageX - catTrack.offsetLeft;
            initialScrollLeft = catTrack.scrollLeft;
        });

        window.addEventListener('mouseup', function() {
            isDown = false;
        });

        catTrack.addEventListener('mousemove', function(e) {
            if (!isDown) return;
            e.preventDefault();
            const currentX = e.pageX - catTrack.offsetLeft;
            const walk = (currentX - startX) * 1.5;
            if (Math.abs(walk) > 4) dragged = true;
            catTrack.scrollLeft = initialScrollLeft - walk;
        });

        // Prevent accidental link navigation while dragging
        catTrack.querySelectorAll('.category-card').forEach(function(card) {
            card.addEventListener('click', function(e) {
                if (dragged) {
                    e.preventDefault();
                }
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
