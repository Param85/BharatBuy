<?php 
require_once __DIR__ . '/includes/header.php'; 
?>

<!-- Link to Homepage Dedicated Stylesheet (Emerald Ink & Champagne Theme) -->
<link rel="stylesheet" href="css/home.css?v=<?= time(); ?>">

<!-- 1. Hero Section -->
<section class="home-hero">
    <div class="home-hero-glow-1"></div>
    <div class="home-hero-glow-2"></div>

    <div class="home-hero-inner">
        <!-- Live Status Pill Badge -->
        <div class="announcement-badge">
            <span class="badge-pulse"></span>
            <span>Next-Gen Cross-Store Shopping Intelligence</span>
            <i class="fa-solid fa-arrow-trend-up" style="color: #06433B;"></i>
        </div>

        <!-- Headline (Retaining #hero-title for multi-language switcher) -->
        <h1 id="hero-title" class="home-hero-title">
            Compare. Analyze.<br>
            <span class="gradient-text">Shop Smarter.</span>
        </h1>

        <!-- Subtitle (Retaining #hero-desc) -->
        <p id="hero-desc" class="home-hero-desc">
            The intelligent shopping assistant that searches multiple online platforms simultaneously and recommends the absolute best deal using a smart scoring algorithm.
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
                    <i class="fa-solid fa-sliders" style="color: #06433B;"></i> Explore Live Demo
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
                <span class="category-pulse-dot" aria-hidden="true"></span>
                <h3>Recommended Categories</h3>
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
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
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

    <!-- Dynamic Moving Horizontal Line Indicator (Moves on Swipe/Scroll) -->
    <div class="category-swipe-line-track" aria-hidden="true">
        <div class="category-swipe-line-thumb" id="categoryLineThumb"></div>
    </div>
</section>

<!-- 2. Trust Metrics Bar -->
<section class="trust-metrics-section">
    <div class="metrics-glass-card">
        <div class="metric-item">
            <div class="metric-number">₹<span>2.4</span> Cr+</div>
            <div class="metric-label">Shopper Savings Generated</div>
        </div>
        <div class="metric-item">
            <div class="metric-number"><span>180</span>K+</div>
            <div class="metric-label">Live Products Indexed</div>
        </div>
        <div class="metric-item">
            <div class="metric-number"><span>4.9</span> ★</div>
            <div class="metric-label">Average User Satisfaction</div>
        </div>
        <div class="metric-item">
            <div class="metric-number">&lt; <span>1.2</span>s</div>
            <div class="metric-label">Multi-Store Scan Latency</div>
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
        <span class="section-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Live Demonstration</span>
        <h2 class="section-title">See BharatBuy Intelligence in Action</h2>
        <p class="section-subtitle">Our weighted scoring engine analyzes merchant prices, seller credibility ratings, and warranty conditions in real-time to uncover the true best value.</p>
    </div>

    <!-- Category Tabs -->
    <div class="showcase-tabs">
        <button type="button" class="showcase-tab active" onclick="switchShowcase('phone')"><i class="fa-solid fa-mobile"></i> Smartphones</button>
        <button type="button" class="showcase-tab" onclick="switchShowcase('laptop')"><i class="fa-solid fa-laptop"></i> Laptops</button>
        <button type="button" class="showcase-tab" onclick="switchShowcase('audio')"><i class="fa-solid fa-headphones"></i> Audio</button>
        <button type="button" class="showcase-tab" onclick="switchShowcase('console')"><i class="fa-solid fa-gamepad"></i> Gaming</button>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h4 style="font-family: 'Poppins', sans-serif; font-size: 1.15rem; font-weight: 700; color: var(--text); margin: 0;">Multi-Store Price Matrix</h4>
                <span style="font-size: 0.8rem; color: #06433B; font-weight: 700;"><i class="fa-solid fa-circle-dot"></i> Live Prices Updated</span>
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
        <span class="section-badge"><i class="fa-solid fa-diagram-project"></i> Simple 3-Step Process</span>
        <h2 class="section-title">How BharatBuy Saves You Money</h2>
        <p class="section-subtitle">No more opening 20 browser tabs. Find authentic deals and protect your budget with three simple steps.</p>
    </div>

    <div class="steps-container">
        <!-- Step 1 -->
        <div class="step-card">
            <div class="step-number-bubble">1</div>
            <div class="step-icon-wrap">
                <i class="fa-solid fa-magnifying-glass-location"></i>
            </div>
            <h3>Universal Multi-Search</h3>
            <p>Type any gadget name or paste a direct product link from Amazon or eBay. Our distributed crawler initializes simultaneously across all connected providers.</p>
        </div>

        <!-- Step 2 -->
        <div class="step-card">
            <div class="step-number-bubble">2</div>
            <div class="step-icon-wrap">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <h3>AI Weighted Scoring</h3>
            <p>We normalize foreign exchange rates (USD to INR), verify merchant feedback ratings, and weigh discount depth to compute an unbiased Best Deal score.</p>
        </div>

        <!-- Step 3 -->
        <div class="step-card">
            <div class="step-number-bubble">3</div>
            <div class="step-icon-wrap">
                <i class="fa-solid fa-bell-concierge"></i>
            </div>
            <h3>Automated Price Tracker</h3>
            <p>Not ready to buy today? Set a target budget price. BharatBuy continuously checks price fluctuations in the background and sends an email the moment it drops.</p>
        </div>
    </div>
</section>

<!-- 6. Features Bento Grid (Preserving IDs: feature-title-1/2/3 for translations) -->
<section class="features-bento-section">
    <div class="section-header-center">
        <span class="section-badge"><i class="fa-solid fa-cubes"></i> Core Capabilities</span>
        <h2 class="section-title">Built for Modern Smart Shoppers</h2>
        <p class="section-subtitle">Every feature is designed to eliminate buyer regret and maximize your purchasing power.</p>
    </div>

    <div class="bento-grid">
        <!-- Card 1: Retains #feature-title-1 and #feature-desc-1 -->
        <div class="bento-card bento-card-large">
            <div class="bento-icon-box">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 id="feature-title-1">Smart Value Scoring</h3>
            <p id="feature-desc-1">Our weighted engine processes price, merchant ratings, and customer reviews to calculate the absolute best product value.</p>
            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <span class="trending-chip" style="cursor: default;"><i class="fa-solid fa-shield-halved" style="color: #06433B;"></i> Fraud Resistant</span>
                <span class="trending-chip" style="cursor: default;"><i class="fa-solid fa-bolt" style="color: #06433B;"></i> Sub-second Scoring</span>
                <span class="trending-chip" style="cursor: default;"><i class="fa-solid fa-calculator" style="color: #06433B;"></i> Multi-attribute Weighting</span>
            </div>
        </div>

        <!-- Card 2: Retains #feature-title-2 and #feature-desc-2 -->
        <div class="bento-card">
            <div class="bento-icon-box">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 id="feature-title-2">Multi-Site Integration</h3>
            <p id="feature-desc-2">Aggregate product results from major e-commerce platforms like Amazon and eBay dynamically in a single interface.</p>
        </div>

        <!-- Card 3: Retains #feature-title-3 and #feature-desc-3 -->
        <div class="bento-card">
            <div class="bento-icon-box">
                <i class="fas fa-history"></i>
            </div>
            <h3 id="feature-title-3">Search Tracking</h3>
            <p id="feature-desc-3">Review your search logs and access previous product comparisons instantly without re-typing search keywords.</p>
        </div>

        <!-- Card 4: Price Drop Tracking Alerts -->
        <div class="bento-card">
            <div class="bento-icon-box">
                <i class="fa-solid fa-bell"></i>
            </div>
            <h3>Automated Price Alerts</h3>
            <p>Save products to your watchlist with custom target prices. Receive automatic high-priority email alerts with direct one-click purchase links.</p>
        </div>

        <!-- Card 5: Currency & International Localization -->
        <div class="bento-card">
            <div class="bento-icon-box">
                <i class="fa-solid fa-globe"></i>
            </div>
            <h3>Dynamic Currency & Localization</h3>
            <p>Seamlessly toggle currencies between INR (₹), USD ($), EUR (€), and GBP (£) with live conversion, alongside 5 international languages.</p>
        </div>
    </div>
</section>

<!-- 7. Gemini AI Shopping Assistant Spotlight -->
<section class="ai-spotlight-section">
    <div class="ai-spotlight-card">
        <div class="ai-card-content">
            <span class="preview-badge" style="background: rgba(248, 231, 201, 0.2); color: #F8E7C9; margin-bottom: 1rem; display: inline-block; border: 1px solid rgba(248, 231, 201, 0.3);">
                <i class="fa-solid fa-sparkles"></i> Powered by Google Gemini AI
            </span>
            <h3>Ask Your Personal AI Shopping Advisor</h3>
            <p>Confused between multiple variants or technical specifications? Chat with our integrated assistant right in the corner for instant advice, pros/cons, and buying guides.</p>
            <button type="button" class="btn-champagne-secondary" onclick="openChatbotWidget()" style="cursor: pointer;">
                <i class="fa-solid fa-comments" style="color: #06433B;"></i> Try Chatbot Now
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

<!-- 8. Final High-Conversion CTA Banner -->
<section class="cta-banner-section">
    <div class="cta-banner-card">
        <h2>Ready to Stop Overpaying for Tech?</h2>
        <p>Join smart Indian shoppers who save money and time on every purchase with BharatBuy's intelligent multi-store price engine.</p>
        <div style="display: flex; justify-content: center; gap: 1.25rem; flex-wrap: wrap;">
            <?php if(!isLoggedIn()): ?>
                <a href="register.php" class="btn-emerald-primary" style="padding: 1.1rem 2.5rem; font-size: 1.05rem;">
                    Create Free Account <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </a>
                <a href="login.php" class="btn-champagne-secondary" style="padding: 1.1rem 2.25rem;">
                    Sign In
                </a>
            <?php else: ?>
                <a href="dashboard.php" class="btn-emerald-primary" style="padding: 1.1rem 2.5rem; font-size: 1.05rem;">
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
        badge: '<i class="fa-solid fa-fire"></i> Hot Price Drop',
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
    // Switch active tab styling
    document.querySelectorAll('.showcase-tab').forEach(tab => tab.classList.remove('active'));
    if (window.event && window.event.currentTarget) {
        window.event.currentTarget.classList.add('active');
    }

    const data = SHOWCASE_DATA[categoryKey];
    if (!data) return;

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
        <span style="color: var(--text-muted); font-size: 0.82rem;">(${data.reviewsCount} verified reviews)</span>
    `;

    // Render deals
    const listContainer = document.getElementById('showcaseDealsList');
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
}

// Initialize showcase with default 'phone' category
document.addEventListener('DOMContentLoaded', function() {
    switchShowcase('phone');

    // Category Carousel Swiper & Moving Horizontal Line Logic
    const catTrack = document.getElementById('categoryTrack');
    const catLineThumb = document.getElementById('categoryLineThumb');
    const catLeftBtn = document.getElementById('catScrollLeft');
    const catRightBtn = document.getElementById('catScrollRight');

    if (catTrack && catLineThumb) {
        function updateSwipeLine() {
            const maxScroll = catTrack.scrollWidth - catTrack.clientWidth;
            if (maxScroll <= 0) {
                catLineThumb.style.width = '100%';
                catLineThumb.style.left = '0%';
                return;
            }

            // Calculate dynamic thumb width (ratio of viewport to total scrollable width)
            const viewportRatio = catTrack.clientWidth / catTrack.scrollWidth;
            const thumbWidthPercent = Math.max(16, Math.min(38, viewportRatio * 100));
            catLineThumb.style.width = thumbWidthPercent + '%';

            // Calculate progress percentage and position thumb along the line
            const scrollFraction = Math.max(0, Math.min(1, catTrack.scrollLeft / maxScroll));
            const maxLeftPercent = 100 - thumbWidthPercent;
            catLineThumb.style.left = (scrollFraction * maxLeftPercent) + '%';
        }

        // Real-time synchronization on scroll (touch swiping, trackpad, mouse wheel)
        catTrack.addEventListener('scroll', updateSwipeLine, { passive: true });
        window.addEventListener('resize', updateSwipeLine);
        setTimeout(updateSwipeLine, 100);

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
