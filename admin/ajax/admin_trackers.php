<?php
/**
 * Admin AJAX — Tracker Actions
 * Handles: deactivate_tracker
 */
require_once __DIR__ . '/../includes/admin_auth.php';
requireAdmin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'deactivate_tracker':
        $trackerId = (int)($_POST['tracker_id'] ?? 0);
        if ($trackerId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid tracker ID.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE product_trackers SET is_active = 0 WHERE id = ?");
            $stmt->execute([$trackerId]);

            logAdminAction($pdo, 'Deactivated tracker', 'tracker', $trackerId);
            echo json_encode(['success' => true, 'message' => 'Tracker deactivated successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
