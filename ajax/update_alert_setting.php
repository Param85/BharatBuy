<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$enabled = isset($_POST['enabled']) ? ((int)$_POST['enabled'] === 1 ? 1 : 0) : 1;

try {
    $stmt = $pdo->prepare("UPDATE users SET alerts_enabled = ? WHERE id = ?");
    $stmt->execute([$enabled, $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("update_alert_setting error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while updating alert preferences.']);
}

