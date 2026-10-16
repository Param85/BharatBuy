<?php
/**
 * Admin - Chatbot Analytics
 * Browse chatbot_logs with summary cards.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;
$sourceFilter = trim($_GET['source'] ?? '');

$where = [];
$params = [];
if ($sourceFilter !== '') {
    $where[] = "source = ?";
    $params[] = $sourceFilter;
}
$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM chatbot_logs $whereClause");
    $countStmt->execute($params);
    $totalLogs = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalLogs / $perPage));

    $stmt = $pdo->prepare("SELECT cl.*, u.full_name as user_name FROM chatbot_logs cl LEFT JOIN users u ON cl.user_id = u.id $whereClause ORDER BY cl.created_at DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    // Summary
    $totalRequests = $pdo->query("SELECT COUNT(*) FROM chatbot_logs")->fetchColumn();
    $avgResponseTime = $pdo->query("SELECT ROUND(AVG(response_time_ms)) FROM chatbot_logs WHERE response_time_ms > 0")->fetchColumn() ?: 0;
    $errorRate = $totalRequests > 0 ? round(($pdo->query("SELECT COUNT(*) FROM chatbot_logs WHERE error_message IS NOT NULL AND error_message != ''")->fetchColumn() / $totalRequests) * 100, 1) : 0;
    $fallbackRate = $totalRequests > 0 ? round(($pdo->query("SELECT COUNT(*) FROM chatbot_logs WHERE source = 'fallback'")->fetchColumn() / $totalRequests) * 100, 1) : 0;
} catch (PDOException $e) {
    $logs = [];
    $totalLogs = $totalRequests = 0;
    $totalPages = 1;
    $avgResponseTime = $errorRate = $fallbackRate = 0;
}
?>

<div class="admin-page-header">
    <h1>Chatbot Analytics</h1>
    <p>Gemini API performance metrics and chatbot interaction logs</p>
</div>

<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fa-solid fa-comments"></i></div>
        <div class="stat-value"><?= number_format($totalRequests); ?></div>
        <div class="stat-label">Total Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-gauge-high"></i></div>
        <div class="stat-value"><?= $avgResponseTime; ?>ms</div>
        <div class="stat-label">Avg Response Time</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="fa-solid fa-bug"></i></div>
        <div class="stat-value"><?= $errorRate; ?>%</div>
        <div class="stat-label">Error Rate</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-rotate-left"></i></div>
        <div class="stat-value"><?= $fallbackRate; ?>%</div>
        <div class="stat-label">Fallback Rate</div>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-scroll" style="color: var(--admin-warning); margin-right: 0.5rem;"></i>Interaction Logs</h3>
        <div class="admin-table-actions">
            <form method="GET" style="display: flex; gap: 0.5rem;">
                <select name="source" class="admin-filter-select">
                    <option value="">All Sources</option>
                    <option value="gemini" <?= $sourceFilter === 'gemini' ? 'selected' : ''; ?>>Gemini</option>
                    <option value="fallback" <?= $sourceFilter === 'fallback' ? 'selected' : ''; ?>>Fallback</option>
                    <option value="rate_limit" <?= $sourceFilter === 'rate_limit' ? 'selected' : ''; ?>>Rate Limit</option>
                    <option value="rejected" <?= $sourceFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <button type="submit" class="admin-btn admin-btn-ghost admin-btn-sm"><i class="fa-solid fa-filter"></i></button>
            </form>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Source</th>
                    <th>Response Time</th>
                    <th>Error</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5"><div class="admin-empty-state"><i class="fa-solid fa-comments"></i><p>No chatbot logs found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= $log['user_name'] ? htmlspecialchars($log['user_name']) : '<span class="text-muted">Guest</span>'; ?></td>
                            <td>
                                <?php
                                $sourceColors = ['gemini' => 'primary', 'fallback' => 'warning', 'rate_limit' => 'danger', 'rejected' => 'neutral'];
                                $badge = $sourceColors[$log['source']] ?? 'neutral';
                                ?>
                                <span class="admin-badge <?= $badge; ?>"><?= htmlspecialchars($log['source']); ?></span>
                            </td>
                            <td><?= $log['response_time_ms']; ?>ms</td>
                            <td>
                                <?php if ($log['error_message']): ?>
                                    <span class="truncate text-danger" style="display:inline-block; max-width: 250px;" title="<?= htmlspecialchars($log['error_message']); ?>"><?= htmlspecialchars($log['error_message']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
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
        <?php if ($page > 1): ?><a href="?page=<?= $page - 1; ?>&source=<?= urlencode($sourceFilter); ?>">&laquo; Prev</a>
        <?php else: ?><span class="disabled">&laquo; Prev</span><?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?><span class="active"><?= $i; ?></span>
            <?php else: ?><a href="?page=<?= $i; ?>&source=<?= urlencode($sourceFilter); ?>"><?= $i; ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="?page=<?= $page + 1; ?>&source=<?= urlencode($sourceFilter); ?>">Next &raquo;</a>
        <?php else: ?><span class="disabled">Next &raquo;</span><?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
