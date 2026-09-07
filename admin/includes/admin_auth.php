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
        // Silently fail — don't break the operation for a log write failure
    }
}
