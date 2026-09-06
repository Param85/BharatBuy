<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$enabled = isset($_POST['enabled']) ? (int)$_POST['enabled'] : 1;

try {
    $stmt = $pdo->prepare("UPDATE users SET alerts_enabled = ? WHERE id = ?");
    $stmt->execute([$enabled, $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
