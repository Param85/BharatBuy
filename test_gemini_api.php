<?php
require_once 'includes/db.php';
require_once 'includes/GeminiService.php';

try {
    $gemini = new GeminiService();
    echo "Service initialized.\n";
    $response = $gemini->generateResponse("Hii", "You are a helpful assistant.");
    echo "Response: " . $response . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
