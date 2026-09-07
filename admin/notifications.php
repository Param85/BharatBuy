<?php
/**
 * Admin — Notification Log
 * Browse user_notifications with search.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];
if ($search !== '') {
    $where[] = "(n.title LIKE ? OR u.full_name LIKE ?)";
    $params = ["%$search%", "%$search%"];
}
$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_notifications n JOIN users u ON n.user_id = u.id $whereClause");
    $countStmt->execute($params);
    $totalNotifs = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalNotifs / $perPage));

    $stmt = $pdo->prepare("SELECT n.*, u.full_name as user_name, p.product_name
        FROM user_notifications n
        JOIN users u ON n.user_id = u.id
        LEFT JOIN product_prices p ON n.product_id = p.id
        $whereClause ORDER BY n.created_at DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $notifications = $stmt->fetchAll();
} catch (PDOException $e) {
    $notifications = [];
    $totalNotifs = 0;
    $totalPages = 1;
}
?>

<div class="admin-page-header">
    <h1>Notification Log</h1>
    <p>Browse all notifications sent to users across the platform</p>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-envelope" style="color: var(--admin-info); margin-right: 0.5rem;"></i>Notifications (<?= number_format($totalNotifs); ?>)</h3>
        <div class="admin-table-actions">
            <form method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="search" class="admin-search-input" placeholder="Search by title or user..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="admin-btn admin-btn-ghost admin-btn-sm"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Product</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notifications)): ?>
                    <tr><td colspan="6"><div class="admin-empty-state"><i class="fa-solid fa-envelope-open"></i><p>No notifications found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <tr>
                            <td class="fw-bold">#<?= $n['id']; ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($n['user_name']); ?></td>
                            <td><span class="admin-badge warning"><?= htmlspecialchars($n['title']); ?></span></td>
                            <td><span class="truncate" style="display:inline-block; max-width: 250px;" title="<?= htmlspecialchars($n['message']); ?>"><?= htmlspecialchars($n['message']); ?></span></td>
                            <td class="text-muted"><?= $n['product_name'] ? htmlspecialchars($n['product_name']) : '—'; ?></td>
                            <td class="text-muted"><?= date('M j, g:i A', strtotime($n['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="admin-pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>">&laquo; Prev</a>
        <?php else: ?>
            <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?><span class="active"><?= $i; ?></span>
            <?php else: ?><a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>">Next &raquo;</a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
