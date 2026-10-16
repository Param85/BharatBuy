<?php
/**
 * Admin - Price Tracker Overview
 * Browse all product_trackers joined with users and products.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$statusFilter = trim($_GET['status'] ?? '');

$where = [];
$params = [];
if ($statusFilter === 'active') { $where[] = "t.is_active = 1"; }
elseif ($statusFilter === 'inactive') { $where[] = "t.is_active = 0"; }

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM product_trackers t $whereClause");
    $countStmt->execute($params);
    $totalTrackers = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalTrackers / $perPage));

    $stmt = $pdo->prepare("SELECT t.*, u.full_name as user_name, u.email as user_email, p.product_name, p.current_price
        FROM product_trackers t
        JOIN users u ON t.user_id = u.id
        JOIN product_prices p ON t.product_id = p.id
        $whereClause ORDER BY t.id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $trackers = $stmt->fetchAll();
} catch (PDOException $e) {
    $trackers = [];
    $totalTrackers = 0;
    $totalPages = 1;
}
?>

<div class="admin-page-header">
    <h1>Price Trackers</h1>
    <p>Overview of all user-created price tracking alerts</p>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-bell" style="color: var(--admin-success); margin-right: 0.5rem;"></i>Trackers (<?= number_format($totalTrackers); ?>)</h3>
        <div class="admin-table-actions">
            <form method="GET" style="display: flex; gap: 0.5rem;">
                <select name="status" class="admin-filter-select">
                    <option value="">All Status</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <button type="submit" class="admin-btn admin-btn-ghost admin-btn-sm"><i class="fa-solid fa-filter"></i></button>
            </form>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Target Price</th>
                    <th>Current Price</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trackers)): ?>
                    <tr><td colspan="8"><div class="admin-empty-state"><i class="fa-solid fa-bell-slash"></i><p>No trackers found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($trackers as $t): ?>
                        <tr id="tracker-row-<?= $t['id']; ?>">
                            <td class="fw-bold">#<?= $t['id']; ?></td>
                            <td>
                                <div style="line-height: 1.4;">
                                    <span style="font-weight: 600; color: var(--admin-text);"><?= htmlspecialchars($t['user_name']); ?></span><br>
                                    <span class="text-muted" style="font-size: 0.78rem;"><?= htmlspecialchars($t['user_email']); ?></span>
                                </div>
                            </td>
                            <td><span class="truncate" style="display:inline-block; max-width: 200px;" title="<?= htmlspecialchars($t['product_name']); ?>"><?= htmlspecialchars($t['product_name']); ?></span></td>
                            <td class="fw-bold text-success">₹<?= number_format($t['target_price'], 2); ?></td>
                            <td>₹<?= number_format($t['current_price'], 2); ?></td>
                            <td>
                                <?php if ((int)$t['is_active'] === 1): ?>
                                    <span class="admin-badge success">Active</span>
                                <?php else: ?>
                                    <span class="admin-badge neutral">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= date('M j, Y', strtotime($t['created_at'])); ?></td>
                            <td>
                                <?php if ((int)$t['is_active'] === 1): ?>
                                    <button class="admin-btn-icon danger" title="Deactivate" onclick="deactivateTracker(<?= $t['id']; ?>)">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="admin-pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1; ?>&status=<?= urlencode($statusFilter); ?>">&laquo; Prev</a>
        <?php else: ?>
            <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?><span class="active"><?= $i; ?></span>
            <?php else: ?><a href="?page=<?= $i; ?>&status=<?= urlencode($statusFilter); ?>"><?= $i; ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&status=<?= urlencode($statusFilter); ?>">Next &raquo;</a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function deactivateTracker(trackerId) {
    showConfirmModal(
        'Deactivate Tracker',
        'Are you sure you want to force-deactivate this price tracker?',
        async function() {
            const result = await adminFetch('ajax/admin_trackers.php', { action: 'deactivate_tracker', tracker_id: trackerId });
            if (result.success) {
                showAdminToast(result.message, 'success');
                setTimeout(() => location.reload(), 600);
            } else {
                showAdminToast(result.message, 'error');
            }
        }
    );
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
