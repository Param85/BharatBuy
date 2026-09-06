<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/scraper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// 1. Log to Search History
$stmt = $pdo->prepare("INSERT INTO search_history (user_id, product_name) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $q]);

// 2. Run real provider search and DB persistence
$strictness = $_GET['strictness'] ?? 'strict';
$isStrict = ($strictness === 'strict');
$products = searchProvidersAndStore($q, $isStrict);

// 3. Format result set for search.js frontend
$formatted = [];
foreach ($products as $p) {
    $formatted[] = [
        'id' => (int)$p['id'],
        'name' => $p['product_name'],
        'price' => (float)$p['current_price'],
        'rating' => (float)$p['rating'],
        'reviews' => (int)$p['review_count'],
        'site' => $p['website'],
        'img' => $p['image_url'],
        'url' => $p['product_url'],
        'score' => (float)$p['score']
    ];
}

echo json_encode($formatted);
