<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Hero Section -->
<section style="padding: 9rem 2rem 7rem 2rem; text-align: center; background: radial-gradient(circle at top, var(--primary-light) 0%, transparent 80%); border-bottom: 1px solid var(--border);">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 id="hero-title" style="font-size: 3.8rem; color: var(--text); font-family: 'Poppins', sans-serif; font-weight: 800; margin-bottom: 1.5rem; line-height: 1.15; letter-spacing: -0.02em;">Compare. Analyze.<br><span style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Shop Smarter.</span></h1>
        <p id="hero-desc" style="font-size: 1.3rem; color: var(--text-muted); max-width: 680px; margin: 0 auto 3rem; line-height: 1.65; font-weight: 450;">
            The intelligent shopping assistant that searches multiple online platforms simultaneously and recommends the absolute best deal using a smart scoring algorithm.
        </p>
        <?php if(!isLoggedIn()): ?>
            <a id="hero-btn" href="register.php" class="btn btn-primary" style="padding: 1.1rem 2.75rem; font-size: 1.1rem;">Get Started Now <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i></a>
        <?php else: ?>
            <a id="hero-btn" href="dashboard.php" class="btn btn-primary" style="padding: 1.1rem 2.75rem; font-size: 1.1rem;">Go to Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i></a>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section style="max-width: 1200px; margin: 0 auto; padding: 7rem 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 3rem; text-align: center;">
    <div style="background: var(--card-bg); padding: 3rem 2rem; border-radius: 20px; border: 1px solid var(--border); box-shadow: var(--shadow); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseenter="this.style.transform='translateY(-8px)'; this.style.boxShadow='var(--shadow-md)'; this.style.borderColor='rgba(255, 107, 0, 0.2)'" onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)'; this.style.borderColor='var(--border)'">
        <div style="background: var(--primary-light); width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: inset 0 2px 4px rgba(255, 107, 0, 0.05);">
            <i class="fas fa-chart-line fa-2x" style="color: var(--primary);"></i>
        </div>
        <h3 id="feature-title-1" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem; color: var(--text);">Smart Value Scoring</h3>
        <p id="feature-desc-1" style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem;">Our weighted engine processes price, merchant ratings, and customer reviews to calculate the absolute best product value.</p>
    </div>
    <div style="background: var(--card-bg); padding: 3rem 2rem; border-radius: 20px; border: 1px solid var(--border); box-shadow: var(--shadow); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseenter="this.style.transform='translateY(-8px)'; this.style.boxShadow='var(--shadow-md)'; this.style.borderColor='rgba(16, 185, 129, 0.2)'" onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)'; this.style.borderColor='var(--border)'">
        <div style="background: var(--accent-light); width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: inset 0 2px 4px rgba(16, 185, 129, 0.05);">
            <i class="fas fa-shield-alt fa-2x" style="color: var(--accent);"></i>
        </div>
        <h3 id="feature-title-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem; color: var(--text);">Multi-Site Integration</h3>
        <p id="feature-desc-2" style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem;">Aggregate product results from major e-commerce platforms like Amazon and eBay dynamically in a single interface.</p>
    </div>
    <div style="background: var(--card-bg); padding: 3rem 2rem; border-radius: 20px; border: 1px solid var(--border); box-shadow: var(--shadow); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseenter="this.style.transform='translateY(-8px)'; this.style.boxShadow='var(--shadow-md)'; this.style.borderColor='rgba(255, 107, 0, 0.2)'" onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)'; this.style.borderColor='var(--border)'">
        <div style="background: var(--primary-light); width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: inset 0 2px 4px rgba(255, 107, 0, 0.05);">
            <i class="fas fa-history fa-2x" style="color: var(--primary);"></i>
        </div>
        <h3 id="feature-title-3" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem; color: var(--text);">Search Tracking</h3>
        <p id="feature-desc-3" style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem;">Review your search logs and access previous product comparisons instantly without re-typing search keywords.</p>
    </div>
</section>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
