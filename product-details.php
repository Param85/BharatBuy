<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';
redirectIfLoggedOut();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/api/recommendation.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM product_prices WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<div class="main-container"><div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> Product not found. <a href="dashboard.php">Go back to dashboard</a></div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Handle Track / Untrack Price Alerts
$message = '';
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'track') {
        $target_price = (float)$_POST['target_price'];
        if ($target_price > 0) {
            $stmt = $pdo->prepare("INSERT INTO product_trackers (user_id, product_id, target_price) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE target_price = ?, is_active = 1");
            $stmt->execute([$_SESSION['user_id'], $product_id, $target_price, $target_price]);
            $message = '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Price alert saved successfully! We will notify you at ' . htmlspecialchars($_SESSION['email'] ?? 'your email') . ' when the price drops.</div>';
        }
    } elseif ($_POST['action'] === 'untrack') {
        $stmt = $pdo->prepare("DELETE FROM product_trackers WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $message = '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Price alert cancelled.</div>';
    }
}

// Fetch current user tracking state
$stmt = $pdo->prepare("SELECT target_price FROM product_trackers WHERE user_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['user_id'], $product_id]);
$tracking_price = $stmt->fetchColumn();

// Fetch price history points for chart
$stmt = $pdo->prepare("SELECT price, recorded_at FROM price_history WHERE product_id = ? ORDER BY recorded_at ASC");
$stmt->execute([$product_id]);
$history = $stmt->fetchAll();

// If product has <= 1 price history point, automatically seed realistic historical points
if (count($history) <= 1) {
    seedProductPriceHistory($pdo, $product_id, (float)$product['current_price'], (float)$product['original_price'], (float)$product['discount_rate']);
    $stmt->execute([$product_id]);
    $history = $stmt->fetchAll();
}

// Calculate stats
$stats = getProductPriceStats($product_id);

// Recalculate recommendation score details
// Find the cheapest product in the same category for comparison
$stmt = $pdo->prepare("SELECT MIN(current_price) FROM product_prices WHERE category = ?");
$stmt->execute([$product['category']]);
$min_price_in_cat = (float)$stmt->fetchColumn();
if ($min_price_in_cat <= 0) {
    $min_price_in_cat = (float)$product['current_price'];
}

$score_data = getDetailedRecommendationScore(
    (float)$product['current_price'],
    (float)$product['rating'],
    (int)$product['review_count'],
    $min_price_in_cat,
    (int)$product['discount_rate'],
    (bool)$product['is_out_of_stock']
);
$score = $score_data['score'];
$reasons = $score_data['reasons'];

// Format data for Chart.js
$chart_labels = [];
$chart_values = [];
foreach ($history as $point) {
    $chart_labels[] = date('M d', strtotime($point['recorded_at']));
    $chart_values[] = (float)$point['price'];
}

// Fetch similar products
$similar_products = getSimilarProducts($product_id, $product['category']);
?>

<link rel="stylesheet" href="css/dashboard.css?v=<?= time(); ?>">
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-container">
    
    <a href="dashboard.php" class="btn btn-outline" style="margin-bottom: 2rem;"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    
    <?php if(!empty($message)) echo $message; ?>

    <div class="details-layout" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 4rem;">
        
        <!-- Left: Product Image -->
        <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow); position: relative; height: fit-content;">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 12px;" onerror="this.onerror=null; this.src='https://placehold.co/500x500/f5f5f7/1d1d1f?text=Product';">
            <span class="site-badge <?php echo 'badge-' . strtolower($product['website']); ?>"><?php echo htmlspecialchars($product['website']); ?></span>
        </div>

        <!-- Right: Meta, Price, Ratings, Tracking -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div>
                <small style="text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--primary); font-size: 0.85rem;"><?php echo htmlspecialchars($product['category']); ?></small>
                <h1 style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 2.25rem; color: var(--text); line-height: 1.25; margin-top: 0.25rem;"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            </div>

            <!-- Price & Discounts -->
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div class="price-tag" style="font-size: 2.5rem; margin: 0;" data-price-inr="<?php echo $product['current_price']; ?>"><script>document.write(formatCurrency(<?php echo $product['current_price']; ?>));</script><noscript>₹<?php echo number_format($product['current_price'], 2); ?></noscript></div>
                <?php if($product['original_price'] > $product['current_price']): ?>
                    <span style="text-decoration: line-through; color: var(--text-muted); font-size: 1.25rem; font-weight: 600;" data-price-inr="<?php echo $product['original_price']; ?>"><script>document.write(formatCurrency(<?php echo $product['original_price']; ?>));</script><noscript>₹<?php echo number_format($product['original_price'], 2); ?></noscript></span>
                    <span style="background: var(--error-light); color: var(--error); padding: 0.3rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.95rem;">-<?php echo $product['discount_rate']; ?>% OFF</span>
                <?php endif; ?>
            </div>

            <!-- Ratings -->
            <div class="rating-stars" style="margin: 0; font-size: 1.25rem;">
                <span>
                    <?php
                    $ratingVal = (float)$product['rating'];
                    $fullStars = floor($ratingVal);
                    $halfStar = $ratingVal % 1 >= 0.5 ? 1 : 0;
                    $emptyStars = 5 - $fullStars - $halfStar;
                    echo str_repeat('<i class="fa-solid fa-star"></i>', $fullStars) . 
                         ($halfStar ? '<i class="fa-solid fa-star-half-stroke"></i>' : '') . 
                         str_repeat('<i class="fa-regular fa-star"></i>', $emptyStars);
                    ?>
                </span>
                <small style="font-size: 0.9rem;">(<?php echo number_format($product['review_count']); ?> reviews)</small>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border);">

            <!-- Value Score reasoning -->
            <div style="background: var(--card-bg); border: 1px solid var(--border); padding: 1.5rem; border-radius: 16px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <h3 style="font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 700;"><i class="fa-solid fa-bolt" style="color: var(--primary);"></i> Value Score</h3>
                    <span style="font-weight: 800; color: var(--primary); font-size: 1.25rem; background: var(--primary-light); padding: 0.25rem 0.75rem; border-radius: 8px;"><?php echo $score; ?>/100</span>
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9rem; color: var(--text-muted); margin-top: 1rem;">
                    <?php foreach($reasons as $reason): ?>
                        <li style="display: flex; gap: 0.5rem; align-items: flex-start;">
                            <i class="fa-solid fa-circle-check" style="color: var(--accent); margin-top: 3px;"></i>
                            <span><?php echo htmlspecialchars($reason); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Price Alert Tracker Box -->
            <div style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--card-bg) 100%); border: 1px solid var(--champagne-border, #E8CD9E); padding: 2rem; border-radius: 20px; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.15rem; color: var(--primary);"><i class="fa-solid fa-bell" style="margin-right: 0.5rem;"></i> Track Price Alerts</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; margin: 0;">We'll monitor this product and send you an instant email alert when the price drops below your target price.</p>
                
                <?php if ($tracking_price): ?>
                    <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <small style="color: var(--text-muted); display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Active Alert Target</small>
                            <span style="font-weight: 700; color: var(--text); font-size: 1.15rem;" data-price-inr="<?php echo $tracking_price; ?>"><script>document.write(formatCurrency(<?php echo $tracking_price; ?>));</script><noscript>₹<?php echo number_format($tracking_price, 2); ?></noscript></span>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="untrack">
                            <button type="submit" class="btn btn-outline" style="color: var(--error); border-color: var(--error); padding: 0.5rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-bell-slash"></i> Cancel Alert</button>
                        </form>
                    </div>
                <?php else: ?>
                    <form method="POST" style="display: flex; gap: 0.5rem; align-items: center; width: 100%;">
                        <input type="hidden" name="action" value="track">
                        <div style="position: relative; flex: 1;">
                            <span id="alert-currency-prefix" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--text-muted);">₹</span>
                            <input type="number" step="0.01" name="target_price" value="<?php echo $product['current_price']; ?>" required style="width: 100%; padding: 0.75rem 1rem 0.75rem 24px; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; font-weight: 600; background: var(--card-bg); color: var(--text);">
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 0.95rem; white-space: nowrap;"><i class="fa-solid fa-bell"></i> Set Alert</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- eBay specific metadata -->
            <?php if ($product['website'] === 'eBay'): ?>
                <div style="background: var(--card-bg); border: 1px solid var(--border); padding: 1.5rem; border-radius: 16px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 0.75rem;">
                    <h3 style="font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--text); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 0.25rem;">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary); margin-right: 0.5rem;"></i> eBay Listing Details
                    </h3>
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1.5rem; font-size: 0.95rem;">
                        <span style="color: var(--text-muted); font-weight: 500;">Seller:</span>
                        <span style="color: var(--text); font-weight: 600;"><?php echo htmlspecialchars($product['seller_name'] ?? 'N/A'); ?></span>

                        <span style="color: var(--text-muted); font-weight: 500;">Condition:</span>
                        <span style="color: var(--text); font-weight: 600;"><?php echo htmlspecialchars($product['item_condition'] ?? 'N/A'); ?></span>

                        <span style="color: var(--text-muted); font-weight: 500;">eBay Item ID:</span>
                        <span style="color: var(--text); font-weight: 600; font-family: monospace;"><?php echo htmlspecialchars($product['item_id'] ?? 'N/A'); ?></span>

                        <span style="color: var(--text-muted); font-weight: 500;">Shipping:</span>
                        <span style="color: var(--text); font-weight: 600;"><?php echo htmlspecialchars($product['shipping_info'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Buy Button & Wishlist -->
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <a href="<?php echo htmlspecialchars($product['product_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="flex: 2; padding: 1rem; justify-content: center; font-size: 1.1rem;">
                    Go to Deal Store <i class="fa-solid fa-up-right-from-square"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Analytics Dashboard: Lowest, Highest, Average Cards -->
    <div style="margin-bottom: 4rem;">
        <h2 style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 1.5rem; color: var(--text);"><i class="fa-solid fa-chart-simple" style="color: var(--primary); margin-right: 0.5rem;"></i> Price Analytics</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            
            <!-- Lowest -->
            <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
                <div style="background: var(--accent-light); color: var(--accent-hover); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-arrow-down"></i>
                </div>
                <div>
                    <small style="color: var(--text-muted); text-transform: uppercase; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em;">Lowest Price</small>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text); margin-top: 0.15rem;" data-price-inr="<?php echo $stats['lowest']; ?>"><script>document.write(formatCurrency(<?php echo $stats['lowest']; ?>));</script><noscript>₹<?php echo number_format($stats['lowest'], 2); ?></noscript></h3>
                </div>
            </div>

            <!-- Highest -->
            <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
                <div style="background: var(--error-light); color: var(--error); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-arrow-up"></i>
                </div>
                <div>
                    <small style="color: var(--text-muted); text-transform: uppercase; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em;">Highest Price</small>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text); margin-top: 0.15rem;" data-price-inr="<?php echo $stats['highest']; ?>"><script>document.write(formatCurrency(<?php echo $stats['highest']; ?>));</script><noscript>₹<?php echo number_format($stats['highest'], 2); ?></noscript></h3>
                </div>
            </div>

            <!-- Average -->
            <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
                <div style="background: var(--primary-light); color: var(--primary); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <small style="color: var(--text-muted); text-transform: uppercase; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em;">Average Price</small>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text); margin-top: 0.15rem;" data-price-inr="<?php echo $stats['average']; ?>"><script>document.write(formatCurrency(<?php echo $stats['average']; ?>));</script><noscript>₹<?php echo number_format($stats['average'], 2); ?></noscript></h3>
                </div>
            </div>

        </div>

        <!-- Chart.js line chart card -->
        <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow);">
            <h3 style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.15rem; margin-bottom: 1.5rem; color: var(--text);">Historical Price Variation Timeline</h3>
            <div style="width: 100%; height: 350px;">
                <canvas id="priceHistoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Description Details -->
    <div style="margin-bottom: 4rem; background: var(--card-bg); border: 1px solid var(--border); padding: 2.5rem; border-radius: 20px; box-shadow: var(--shadow);">
        <h2 style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 1rem; color: var(--text);">Product Overview</h2>
        <p style="color: var(--text-muted); line-height: 1.7; font-size: 1rem; white-space: pre-line;"><?php echo htmlspecialchars($product['description']); ?></p>
    </div>

    <!-- Similar Products Grid -->
    <div>
        <h2 style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 1.5rem; color: var(--text);"><i class="fa-solid fa-tags" style="color: var(--primary); margin-right: 0.5rem;"></i> Similar Products Compared</h2>
        <div class="results-grid" style="padding: 0; width: 100%;">
            <?php foreach($similar_products as $sim): ?>
                <div class="product-card">
                    <span class="site-badge <?php echo 'badge-' . strtolower($sim['website']); ?>"><?php echo htmlspecialchars($sim['website']); ?></span>
                    <div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; background: var(--card-bg); border: 1px solid var(--border); margin-bottom: 1rem; padding: 0.5rem; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($sim['image_url']); ?>" alt="<?php echo htmlspecialchars($sim['product_name']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null; this.src='https://placehold.co/500x500/f5f5f7/1d1d1f?text=Product';">
                    </div>
                    <small><?php echo htmlspecialchars($sim['website']); ?></small>
                    <h3><?php echo htmlspecialchars($sim['product_name']); ?></h3>
                    <div class="price-tag" data-price-inr="<?php echo $sim['current_price']; ?>"><script>document.write(formatCurrency(<?php echo $sim['current_price']; ?>));</script><noscript>₹<?php echo number_format($sim['current_price'], 2); ?></noscript></div>
                    <div style="margin-top: auto; display: flex; gap: 0.5rem; width: 100%;">
                        <a href="product-details.php?id=<?php echo $sim['id']; ?>" class="btn btn-primary" style="flex:1; text-align:center;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- Chart initialization script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('priceHistoryChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // PHP arrays mapped to JS arrays
    const labels = <?php echo json_encode($chart_labels); ?>;
    const data = <?php echo json_encode($chart_values); ?>;
    
    // Fallback if data is empty or single item to draw baseline
    if(data.length === 0) {
        const currentPrice = <?php echo (float)$product['current_price']; ?>;
        labels.push('Current Price');
        data.push(currentPrice);
    }
    if(data.length === 1) {
        labels.unshift('Initial Check');
        data.unshift(data[0]);
    }
    
    const savedCurrency = localStorage.getItem('currency') || 'INR';
    const curSymbol = (savedCurrency === 'USD') ? '$' : (savedCurrency === 'EUR' ? '€' : (savedCurrency === 'GBP' ? '£' : '₹'));
    const curRate = (savedCurrency === 'USD') ? 0.0105 : (savedCurrency === 'EUR' ? 0.0097 : (savedCurrency === 'GBP' ? 0.0082 : 1));
    const convertedData = data.map(val => val * curRate);

    // Gradient fill with Emerald Ink & Champagne
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(6, 67, 59, 0.32)');
    gradient.addColorStop(1, 'rgba(248, 231, 201, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `Price (${curSymbol})`,
                data: convertedData,
                borderColor: '#06433B',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#06433B',
                pointBorderColor: '#F8E7C9',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 14, weight: '600' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' Price: ' + curSymbol + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)'
                    },
                    ticks: {
                        color: '#64748b',
                        callback: function(value) {
                            return curSymbol + value.toLocaleString(undefined, { maximumFractionDigits: 0 });
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b'
                    }
                }
            }
        }
    });
});
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
