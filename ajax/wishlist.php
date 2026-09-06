<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $name = trim($data['name'] ?? '');
    $site = trim($data['site'] ?? '');
    $price = (float)($data['price'] ?? 0.0);
    $img = trim($data['img'] ?? '');
    $url = trim($data['url'] ?? '#');

    if (empty($name) || empty($site) || $price <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product details.']);
        exit;
    }

    // Prevent duplicate entries
    $check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_name = ? AND website = ?");
    $check->execute([$_SESSION['user_id'], $name, $site]);
    
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'This product is already in your wishlist.']);
        exit;
    }

    // Insert new item
    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_name, website, current_price, image_url, product_url) VALUES (?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        $_SESSION['user_id'],
        $name,
        $site,
        $price,
        $img,
        $url
    ]);
    
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Empty request body.']);
}
