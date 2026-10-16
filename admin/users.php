<?php
/**
 * Admin - User Management
 * Paginated user table with search, admin toggle, and delete.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Search filter
$search = trim($_GET['search'] ?? '');
$whereClause = '';
$params = [];
if ($search !== '') {
    $whereClause = "WHERE full_name LIKE ? OR email LIKE ?";
    $params = ["%$search%", "%$search%"];
}

try {
    // Total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM users $whereClause");
    $countStmt->execute($params);
    $totalUsers = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalUsers / $perPage));

    // Fetch users
    $stmt = $pdo->prepare("SELECT u.*, 
        (SELECT COUNT(*) FROM wishlist WHERE user_id = u.id) as wishlist_count,
        (SELECT COUNT(*) FROM product_trackers WHERE user_id = u.id) as tracker_count,
        (SELECT COUNT(*) FROM search_history WHERE user_id = u.id) as search_count
        FROM users u $whereClause ORDER BY u.id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $totalUsers = 0;
    $totalPages = 1;
}
?>

<div class="admin-page-header">
    <h1>User Management</h1>
    <p>View, manage, and moderate registered user accounts</p>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-users" style="color: var(--admin-primary-hover); margin-right: 0.5rem;"></i>All Users (<?= number_format($totalUsers); ?>)</h3>
        <div class="admin-table-actions">
            <form method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="search" class="admin-search-input" placeholder="Search by name or email..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="admin-btn admin-btn-ghost admin-btn-sm"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table" id="usersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Wishlists</th>
                    <th>Trackers</th>
                    <th>Searches</th>
                    <th>Alerts</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="10"><div class="admin-empty-state"><i class="fa-solid fa-users-slash"></i><p>No users found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr id="user-row-<?= $u['id']; ?>">
                            <td class="fw-bold">#<?= $u['id']; ?></td>
                            <td><?= htmlspecialchars($u['full_name']); ?></td>
                            <td><span class="truncate" style="display:inline-block;"><?= htmlspecialchars($u['email']); ?></span></td>
                            <td><?= $u['wishlist_count']; ?></td>
                            <td><?= $u['tracker_count']; ?></td>
                            <td><?= $u['search_count']; ?></td>
                            <td>
                                <?php if ((int)$u['alerts_enabled'] === 1): ?>
                                    <span class="admin-badge success">On</span>
                                <?php else: ?>
                                    <span class="admin-badge neutral">Off</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)($u['is_admin'] ?? 0) === 1): ?>
                                    <span class="admin-badge primary">Admin</span>
                                <?php else: ?>
                                    <span class="admin-badge neutral">User</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($u['created_at'])); ?></td>
                            <td style="white-space: nowrap;">
                                <?php if ((int)($u['is_admin'] ?? 0) === 1): ?>
                                    <button class="admin-btn-icon" title="Demote from Admin" onclick="toggleAdmin(<?= $u['id']; ?>, 0)">
                                        <i class="fa-solid fa-user-minus"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="admin-btn-icon" title="Promote to Admin" onclick="toggleAdmin(<?= $u['id']; ?>, 1)">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="admin-btn-icon danger" title="Delete User" 
                                    data-id="<?= (int)$u['id']; ?>" 
                                    data-name="<?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                    onclick="deleteUser(this.getAttribute('data-id'), this.getAttribute('data-name'))">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
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
            <a href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>">&laquo; Prev</a>
        <?php else: ?>
            <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?>
                <span class="active"><?= $i; ?></span>
            <?php else: ?>
                <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>">Next &raquo;</a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function deleteUser(userId, userName) {
    showConfirmModal(
        'Delete User',
        `Are you sure you want to permanently delete "${userName}"? This action cannot be undone.`,
        async function() {
            const result = await adminFetch('ajax/admin_users.php', { action: 'delete_user', user_id: userId });
            if (result.success) {
                const row = document.getElementById('user-row-' + userId);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    row.style.transition = 'all 0.3s ease';
                    setTimeout(() => row.remove(), 300);
                }
                showAdminToast(result.message, 'success');
            } else {
                showAdminToast(result.message, 'error');
            }
        }
    );
}

function toggleAdmin(userId, newStatus) {
    const actionText = newStatus === 1 ? 'promote this user to admin' : 'revoke admin access for this user';
    showConfirmModal(
        newStatus === 1 ? 'Promote to Admin' : 'Revoke Admin',
        `Are you sure you want to ${actionText}?`,
        async function() {
            const result = await adminFetch('ajax/admin_users.php', { action: 'toggle_admin', user_id: userId, new_status: newStatus });
            if (result.success) {
                showAdminToast(result.message, 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showAdminToast(result.message, 'error');
            }
        }
    );
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
