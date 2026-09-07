<?php
/**
 * Admin AJAX — Notification Actions
 * Handles: clear_user_notifications
 */
require_once __DIR__ . '/../includes/admin_auth.php';
requireAdmin();

header('Content-Type: application/json');

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
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
