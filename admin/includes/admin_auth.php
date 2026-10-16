<?php
/**
 * Admin Authentication Middleware
 * Include this at the top of every admin page.
 * Verifies user is logged in AND has is_admin = 1.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/session.php';

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        $isAjax = (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
            (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/ajax/') !== false)
        );

        if ($isAjax) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized: Administrator access required.']);
            exit();
        }

        header("Location: login.php");
        exit();
    }
}


/**
 * Log an admin action to the audit trail.
 */
function logAdminAction($pdo, $action, $targetType = null, $targetId = null, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action, $targetType, $targetId, $details]);
    } catch (PDOException $e) {
        // Silently fail - don't break the operation for a log write failure
    }
}
