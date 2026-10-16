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

$tracker_id = isset($_POST['tracker_id']) ? (int)$_POST['tracker_id'] : 0;


try {
    $stmtDetail = $pdo->prepare("
        SELECT t.target_price, t.product_id, u.email, u.full_name, p.product_name, p.current_price, p.original_price, p.product_url
        FROM product_trackers t
        JOIN users u ON t.user_id = u.id
        JOIN product_prices p ON t.product_id = p.id
        WHERE t.id = ? AND t.user_id = ? AND t.is_active = 1
    ");
    $stmtDetail->execute([$tracker_id, $_SESSION['user_id']]);
    $alert = $stmtDetail->fetch();

    if ($alert) {
        require_once __DIR__ . '/../includes/mailer.php';
        $old_price_est = ($alert['original_price'] > $alert['current_price']) ? $alert['original_price'] : $alert['target_price'];
        sendPriceAlertEmail(
            $alert['email'],
            $alert['full_name'],
            $alert['product_name'],
            $old_price_est,
            $alert['current_price'],
            $alert['product_url']
        );

        // Insert database notification
        $stmtNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, title, message, product_id) VALUES (?, ?, ?, ?)");
        $stmtNotif->execute([
            $_SESSION['user_id'],
            'Price Drop Alert',
            $alert['product_name'] . ' is now down to ₹' . number_format($alert['current_price'], 2),
            $alert['product_id']
        ]);
    }

    $stmt = $pdo->prepare("UPDATE product_trackers SET is_active = 0 WHERE id = ? AND user_id = ?");
    $stmt->execute([$tracker_id, $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("deactivate_tracker error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while updating tracker.']);
}

