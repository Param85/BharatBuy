<?php
/**
 * Admin AJAX - User Management Actions
 * Handles: delete_user, toggle_admin
 */
require_once __DIR__ . '/../includes/admin_auth.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

validate_csrf_or_exit();

$action = $_POST['action'] ?? '';


switch ($action) {

    case 'delete_user':
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }
        // Prevent self-deletion
        if ($userId === (int)$_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
            exit;
        }
        try {
            // Fetch user info for logging
            $stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $target = $stmt->fetch();

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            logAdminAction($pdo, 'Deleted user', 'user', $userId, $target ? $target['email'] : 'Unknown');
            echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        } catch (PDOException $e) {
            error_log("admin_users error (delete): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An internal database error occurred while deleting user.']);
        }
        break;

    case 'toggle_admin':
        $userId = (int)($_POST['user_id'] ?? 0);
        $newStatus = (int)($_POST['new_status'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }
        // Prevent self-demotion
        if ($userId === (int)$_SESSION['user_id'] && $newStatus === 0) {
            echo json_encode(['success' => false, 'message' => 'You cannot revoke your own admin access.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);

            $actionText = $newStatus === 1 ? 'Promoted to admin' : 'Demoted from admin';
            logAdminAction($pdo, $actionText, 'user', $userId);
            echo json_encode(['success' => true, 'message' => $actionText . ' successfully.']);
        } catch (PDOException $e) {
            error_log("admin_users error (toggle_admin): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An internal database error occurred while updating role.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
