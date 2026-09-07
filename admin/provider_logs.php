<?php
/**
 * Admin — Provider Health Logs
 * Browse provider_logs with filters.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;
$providerFilter = trim($_GET['provider'] ?? '');
$statusFilter = trim($_GET['status_range'] ?? '');

$where = [];
$params = [];
if ($providerFilter !== '') {
    $where[] = "provider_name = ?";
    $params[] = $providerFilter;
}
if ($statusFilter === '2xx') { $where[] = "status >= 200 AND status < 300"; }
elseif ($statusFilter === '4xx') { $where[] = "status >= 400 AND status < 500"; }
elseif ($statusFilter === '5xx') { $where[] = "status >= 500"; }

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM provider_logs $whereClause");
    $countStmt->execute($params);
    $totalLogs = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalLogs / $perPage));

    $stmt = $pdo->prepare("SELECT * FROM provider_logs $whereClause ORDER BY logged_at DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    $providers = $pdo->query("SELECT DISTINCT provider_name FROM provider_logs ORDER BY provider_name")->fetchAll(PDO::FETCH_COLUMN);

    // Summary stats
    $avgTime = $pdo->query("SELECT ROUND(AVG(response_time_ms)) FROM provider_logs")->fetchColumn() ?: 0;
    $errorCount = $pdo->query("SELECT COUNT(*) FROM provider_logs WHERE status >= 400")->fetchColumn();
} catch (PDOException $e) {
    $logs = [];
    $totalLogs = 0;
    $totalPages = 1;
    $providers = [];
    $avgTime = 0;
    $errorCount = 0;
}
?>

<div class="admin-page-header">
    <h1>Provider Health</h1>
    <p>Monitor API provider response times, status codes, and error rates</p>
</div>

<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-server"></i></div>
        <div class="stat-value"><?= number_format($totalLogs); ?></div>
        <div class="stat-label">Total Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-value"><?= $avgTime; ?>ms</div>
        <div class="stat-label">Avg Response Time</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-value"><?= number_format($errorCount); ?></div>
        <div class="stat-label">Total Errors</div>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-list-check" style="color: var(--admin-success); margin-right: 0.5rem;"></i>Request Logs</h3>
        <div class="admin-table-actions">
            <form method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <select name="provider" class="admin-filter-select">
                    <option value="">All Providers</option>
                    <?php foreach ($providers as $p): ?>
                        <option value="<?= htmlspecialchars($p); ?>" <?= $providerFilter === $p ? 'selected' : ''; ?>><?= htmlspecialchars($p); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status_range" class="admin-filter-select">
                    <option value="">All Status</option>
                    <option value="2xx" <?= $statusFilter === '2xx' ? 'selected' : ''; ?>>2xx Success</option>
                    <option value="4xx" <?= $statusFilter === '4xx' ? 'selected' : ''; ?>>4xx Client Error</option>
                    <option value="5xx" <?= $statusFilter === '5xx' ? 'selected' : ''; ?>>5xx Server Error</option>
                </select>
                <button type="submit" class="admin-btn admin-btn-ghost admin-btn-sm"><i class="fa-solid fa-filter"></i></button>
            </form>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th>Error Message</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5"><div class="admin-empty-state"><i class="fa-solid fa-server"></i><p>No logs found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($log['provider_name']); ?></td>
                            <td>
                                <?php
                                $status = (int)$log['status'];
                                if ($status >= 200 && $status < 300) $badgeClass = 'success';
                                elseif ($status >= 400 && $status < 500) $badgeClass = 'warning';
                                else $badgeClass = 'danger';
                                ?>
                                <span class="admin-badge <?= $badgeClass; ?>"><?= $status; ?></span>
                            </td>
                            <td><?= $log['response_time_ms']; ?>ms</td>
                            <td>
                                <?php if ($log['error_message']): ?>
                                    <span class="truncate text-danger" style="display:inline-block; max-width: 280px;" title="<?= htmlspecialchars($log['error_message']); ?>"><?= htmlspecialchars($log['error_message']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= date('M j, g:i:s A', strtotime($log['logged_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="admin-pagination">
        <?php if ($page > 1): ?><a href="?page=<?= $page - 1; ?>&provider=<?= urlencode($providerFilter); ?>&status_range=<?= urlencode($statusFilter); ?>">&laquo; Prev</a>
        <?php else: ?><span class="disabled">&laquo; Prev</span><?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?><span class="active"><?= $i; ?></span>
            <?php else: ?><a href="?page=<?= $i; ?>&provider=<?= urlencode($providerFilter); ?>&status_range=<?= urlencode($statusFilter); ?>"><?= $i; ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="?page=<?= $page + 1; ?>&provider=<?= urlencode($providerFilter); ?>&status_range=<?= urlencode($statusFilter); ?>">Next &raquo;</a>
        <?php else: ?><span class="disabled">Next &raquo;</span><?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
