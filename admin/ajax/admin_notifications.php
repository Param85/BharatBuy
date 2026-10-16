<?php
/**
 * Admin AJAX - Notification Actions
 * Handles: clear_user_notifications
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

    case 'clear_user_notifications':
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM user_notifications WHERE user_id = ?");
            $stmt->execute([$userId]);

            logAdminAction($pdo, 'Cleared notifications', 'user', $userId);
            echo json_encode(['success' => true, 'message' => 'Notifications cleared for user.']);
        } catch (PDOException $e) {
            error_log("admin_notifications error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An internal database error occurred while clearing notifications.']);
        }
        break;


    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
