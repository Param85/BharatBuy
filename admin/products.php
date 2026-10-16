<?php
/**
 * Admin - Product Catalog Browser
 * Browse all product_prices with search, filter, and price history.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filters
$search = trim($_GET['search'] ?? '');
$website = trim($_GET['website'] ?? '');
$stock = trim($_GET['stock'] ?? '');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "product_name LIKE ?";
    $params[] = "%$search%";
}
if ($website !== '') {
    $where[] = "website = ?";
    $params[] = $website;
}
if ($stock === 'in') {
    $where[] = "is_out_of_stock = 0";
} elseif ($stock === 'out') {
    $where[] = "is_out_of_stock = 1";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM product_prices $whereClause");
    $countStmt->execute($params);
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalProducts / $perPage));

    $stmt = $pdo->prepare("SELECT * FROM product_prices $whereClause ORDER BY id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Get distinct websites for filter
    $websites = $pdo->query("SELECT DISTINCT website FROM product_prices ORDER BY website")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $products = [];
    $totalProducts = 0;
    $totalPages = 1;
    $websites = [];
}
?>

<div class="admin-page-header">
    <h1>Product Catalog</h1>
    <p>Browse and inspect all tracked product listings across platforms</p>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-box-open" style="color: var(--admin-orange); margin-right: 0.5rem;"></i>Products (<?= number_format($totalProducts); ?>)</h3>
        <div class="admin-table-actions">
            <form method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="text" name="search" class="admin-search-input" placeholder="Search products..." value="<?= htmlspecialchars($search); ?>">
                <select name="website" class="admin-filter-select">
                    <option value="">All Platforms</option>
                    <?php foreach ($websites as $w): ?>
                        <option value="<?= htmlspecialchars($w); ?>" <?= $website === $w ? 'selected' : ''; ?>><?= htmlspecialchars($w); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="stock" class="admin-filter-select">
                    <option value="">All Stock</option>
                    <option value="in" <?= $stock === 'in' ? 'selected' : ''; ?>>In Stock</option>
                    <option value="out" <?= $stock === 'out' ? 'selected' : ''; ?>>Out of Stock</option>
                </select>
                <button type="submit" class="admin-btn admin-btn-ghost admin-btn-sm"><i class="fa-solid fa-filter"></i> Filter</button>
            </form>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Platform</th>
                    <th>Current Price</th>
                    <th>Original</th>
                    <th>Discount</th>
                    <th>Rating</th>
                    <th>Reviews</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="11"><div class="admin-empty-state"><i class="fa-solid fa-box-open"></i><p>No products found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="fw-bold">#<?= $p['id']; ?></td>
                            <td><span class="truncate" style="display:inline-block; max-width: 200px;" title="<?= htmlspecialchars($p['product_name']); ?>"><?= htmlspecialchars($p['product_name']); ?></span></td>
                            <td><span class="admin-badge info"><?= htmlspecialchars($p['website']); ?></span></td>
                            <td class="fw-bold">₹<?= number_format($p['current_price'], 2); ?></td>
                            <td class="text-muted">₹<?= number_format($p['original_price'], 2); ?></td>
                            <td>
                                <?php if ((int)$p['discount_rate'] > 0): ?>
                                    <span class="admin-badge success"><?= $p['discount_rate']; ?>% off</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((float)$p['rating'] > 0): ?>
                                    <i class="fa-solid fa-star" style="color: #F59E0B; font-size: 0.75rem;"></i> <?= $p['rating']; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($p['review_count']); ?></td>
                            <td>
                                <?php if ((int)$p['is_out_of_stock'] === 0): ?>
                                    <span class="admin-badge success">In Stock</span>
                                <?php else: ?>
                                    <span class="admin-badge danger">Out</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="text-muted"><?= htmlspecialchars($p['category']); ?></span></td>
                            <td class="text-muted"><?= date('M j', strtotime($p['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="admin-pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>&website=<?= urlencode($website); ?>&stock=<?= urlencode($stock); ?>">&laquo; Prev</a>
        <?php else: ?>
            <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?>
                <span class="active"><?= $i; ?></span>
            <?php else: ?>
                <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>&website=<?= urlencode($website); ?>&stock=<?= urlencode($stock); ?>"><?= $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>&website=<?= urlencode($website); ?>&stock=<?= urlencode($stock); ?>">Next &raquo;</a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
