<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mailer.php';

header('Content-Type: application/json');

// This check-alerts cron script can run in background without session if called via CLI or secured webhook
$is_cli = (php_sapi_name() === 'cli');
$secret_token = defined('CRON_SECRET_KEY') ? CRON_SECRET_KEY : 'bharatbuy_cron_sec_87e2b8310';
$provided_token = (string)($_GET['token'] ?? $_SERVER['HTTP_X_CRON_TOKEN'] ?? '');

if (!$is_cli && (empty($provided_token) || !hash_equals($secret_token, $provided_token))) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}


try {
    // Select active alerts where the current price is less than or equal to target_price, and alerts are enabled by the user
    $stmt = $pdo->prepare("
        SELECT t.id as tracker_id, t.target_price, u.id as user_id, u.email, u.full_name, 
               p.id as product_id, p.product_name, p.current_price, p.original_price, p.product_url
        FROM product_trackers t
        JOIN users u ON t.user_id = u.id
        JOIN product_prices p ON t.product_id = p.id
        WHERE t.is_active = 1 AND p.current_price <= t.target_price AND u.alerts_enabled = 1
    ");
    $stmt->execute();
    $alerts_to_send = $stmt->fetchAll();

    $sent_count = 0;
    foreach ($alerts_to_send as $alert) {
        // Send HTML email alert
        $sent = sendPriceAlertEmail(
            $alert['email'],
            $alert['full_name'],
            $alert['product_name'],
            (float)$alert['original_price'],
            (float)$alert['current_price'],
            $alert['product_url']
        );

        // Insert database notification
        $stmtNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, title, message, product_id) VALUES (?, ?, ?, ?)");
        $stmtNotif->execute([
            $alert['user_id'],
            'Price Drop Alert',
            $alert['product_name'] . ' is now down to ₹' . number_format($alert['current_price'], 2),
            $alert['product_id']
        ]);
        
        // Deactivate alert after sending to avoid double notifications
        $update = $pdo->prepare("UPDATE product_trackers SET is_active = 0 WHERE id = ?");
        $update->execute([$alert['tracker_id']]);
        
        $sent_count++;
    }

    echo json_encode([
        'success' => true,
        'checked_count' => count($alerts_to_send),
        'sent_count' => $sent_count
    ]);
} catch (Exception $e) {
    error_log("check-alerts cron error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An internal error occurred while processing alerts.'
    ]);
}

