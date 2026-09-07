<?php
/**
 * Admin — Activity Audit Log
 * Read-only log of all admin actions.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

try {
    $totalLogs = $pdo->query("SELECT COUNT(*) FROM admin_activity_log")->fetchColumn();
    $totalPages = max(1, ceil($totalLogs / $perPage));

    $stmt = $pdo->prepare("SELECT al.*, u.full_name as admin_name 
        FROM admin_activity_log al 
        JOIN users u ON al.admin_id = u.id 
        ORDER BY al.created_at DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute();
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = [];
    $totalLogs = 0;
    $totalPages = 1;
}
?>

<div class="admin-page-header">
    <h1>Activity Log</h1>
    <p>Complete audit trail of all administrative actions performed on the platform</p>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-clipboard-list" style="color: var(--admin-primary-hover); margin-right: 0.5rem;"></i>Audit Trail (<?= number_format($totalLogs); ?>)</h3>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Target ID</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="7"><div class="admin-empty-state"><i class="fa-solid fa-clipboard-list"></i><p>No activity logged yet. Actions will appear here as admins perform operations.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="fw-bold">#<?= $log['id']; ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($log['admin_name']); ?></td>
                            <td><span class="admin-badge primary"><?= htmlspecialchars($log['action']); ?></span></td>
                            <td>
                                <?php if ($log['target_type']): ?>
                                    <span class="admin-badge neutral"><?= htmlspecialchars($log['target_type']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $log['target_id'] ? '#' . $log['target_id'] : '—'; ?></td>
                            <td>
                                <?php if ($log['details']): ?>
                                    <span class="truncate" style="display:inline-block; max-width: 200px;" title="<?= htmlspecialchars($log['details']); ?>"><?= htmlspecialchars($log['details']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= date('M j, g:i:s A', strtotime($log['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="admin-pagination">
        <?php if ($page > 1): ?><a href="?page=<?= $page - 1; ?>">&laquo; Prev</a>
        <?php else: ?><span class="disabled">&laquo; Prev</span><?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?><span class="active"><?= $i; ?></span>
            <?php else: ?><a href="?page=<?= $i; ?>"><?= $i; ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="?page=<?= $page + 1; ?>">Next &raquo;</a>
        <?php else: ?><span class="disabled">Next &raquo;</span><?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
