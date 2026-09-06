<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
redirectIfLoggedOut();

// Correct any saved items with the incorrect Unsplash beard photo ID
$pdo->exec("UPDATE wishlist SET image_url = 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=500&q=80' WHERE image_url LIKE '%photo-1521572267360-ee0c2909d518%'");
$pdo->exec("UPDATE product_prices SET image_url = 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=500&q=80' WHERE image_url LIKE '%photo-1521572267360-ee0c2909d518%'");

// Delete Item Logic (using safe casting & verification)
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $_SESSION['user_id']]);
    header("Location: wishlist.php");
    exit;
}

// Fetch Wishlist Items with product_prices links if available
$stmt = $pdo->prepare("
    SELECT w.*, p.id as product_id, p.image_url as latest_image_url
    FROM wishlist w
    LEFT JOIN product_prices p ON w.product_name = p.product_name AND w.website = p.website
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/dashboard.css?v=<?= time(); ?>">

<div class="header-container" style="max-width: 1200px; margin: 0 auto; padding: 3rem 1.5rem; width: 100%;">
    <h2 style="font-family: 'Poppins', sans-serif; font-weight: 700; margin-bottom: 2rem; color: var(--text);"><i class="fa-solid fa-heart" style="color: #ef4444; margin-right: 0.5rem;"></i> My Saved Wishlist</h2>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 5rem 2rem; background: var(--card-bg); border: 1px solid var(--border); box-shadow: var(--shadow); border-radius: 16px;">
            <i class="fa-regular fa-heart fa-3x" style="color: #cbd5e1; margin-bottom: 1.5rem;"></i>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 1.5rem;">Your wishlist is currently empty.</p>
            <a href="dashboard.php" class="btn btn-primary">Go Find Deals</a>
        </div>
    <?php else: ?>
        <div class="results-grid" style="padding: 0; margin: 0; width: 100%;">
            <?php foreach ($items as $item): ?>
                <?php
                $imgUrl = $item['image_url'];
                if (strpos($imgUrl, 'loremflickr.com') !== false) {
                    if (!empty($item['latest_image_url']) && strpos($item['latest_image_url'], 'loremflickr.com') === false) {
                        $imgUrl = $item['latest_image_url'];
                    } else {
                        $q = strtolower($item['product_name']);
                        if (strpos($q, 'shirt') !== false || strpos($q, 'clothing') !== false || strpos($q, 'apparel') !== false) {
                            $imgUrl = 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=500&q=80';
                        } elseif (strpos($q, 'shoe') !== false || strpos($q, 'sneaker') !== false || strpos($q, 'cleat') !== false || strpos($q, 'nike') !== false) {
                            $imgUrl = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80';
                        } elseif (strpos($q, 'watch') !== false) {
                            $imgUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80';
                        } else {
                            $imgUrl = 'https://placehold.co/500x500/f5f5f7/1d1d1f?text=' . urlencode($item['product_name']);
                        }
                        // Save repaired image back to DB
                        $updateStmt = $pdo->prepare("UPDATE wishlist SET image_url = ? WHERE id = ?");
                        $updateStmt->execute([$imgUrl, $item['id']]);
                    }
                }

                // If product_id is null, dynamically recreate the product in product_prices to enable the alert button
                if (!$item['product_id']) {
                    try {
                        $insertStmt = $pdo->prepare("
                            INSERT INTO product_prices (product_name, website, current_price, original_price, rating, review_count, image_url, product_url, category) 
                            VALUES (?, ?, ?, ?, 4.5, 100, ?, ?, 'General')
                        ");
                        $insertStmt->execute([
                            $item['product_name'],
                            $item['website'],
                            $item['current_price'],
                            $item['current_price'] * 1.2,
                            $imgUrl,
                            $item['product_url']
                        ]);
                        $item['product_id'] = $pdo->lastInsertId();
                    } catch (Exception $ex) {
                        // ignore and display clean
                    }
                }
                ?>
                <div class="product-card">
                    <span class="site-badge <?php echo 'badge-' . strtolower($item['website']); ?>"><?php echo htmlspecialchars($item['website']); ?></span>
                    <div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem; padding: 0.5rem; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;" onerror="this.onerror=null; this.src='https://placehold.co/500x500/f5f5f7/1d1d1f?text=Product';">
                    </div>
                    <small><?php echo htmlspecialchars($item['website']); ?></small>
                    <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                    <div class="price-tag" data-price-inr="<?php echo $item['current_price']; ?>"><script>document.write(formatCurrency(<?php echo $item['current_price']; ?>));</script><noscript>₹<?php echo number_format($item['current_price'], 2); ?></noscript></div>
                    <div style="margin-top: auto; display: flex; gap: 0.5rem; width: 100%;">
                        <a href="<?php echo htmlspecialchars($item['product_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="flex: 1; text-align: center; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                            Buy Now <i class="fa-solid fa-up-right-from-square" style="font-size: 0.8rem;"></i>
                        </a>
                        <?php if ($item['product_id']): ?>
                            <a href="product-details.php?id=<?php echo $item['product_id']; ?>" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary); display: flex; align-items: center; justify-content: center; width: 44px; padding: 0; transition: all 0.2s;" onmouseenter="this.style.background='var(--primary-light)'" onmouseleave="this.style.background='white'" title="Set Price Alert">
                                <i class="fa-solid fa-bell"></i>
                            </a>
                        <?php endif; ?>
                        <a href="wishlist.php?delete=<?php echo $item['id']; ?>" class="btn btn-outline" style="color: var(--error); border-color: var(--error); display: flex; align-items: center; justify-content: center; width: 44px; padding: 0; transition: all 0.2s;" onmouseenter="this.style.background='var(--error-light)'" onmouseleave="this.style.background='white'" title="Remove Item">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
