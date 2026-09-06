<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/providers/EbayProvider.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) {
    echo json_encode(['error' => 'Query too short']);
    exit;
}

$provider = new EbayProvider();
$results = $provider->search($q);
echo json_encode($results);
