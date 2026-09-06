<?php
require_once __DIR__ . '/BaseProvider.php';

/**
 * FakeStoreProvider Class
 * 
 * Communicates with the Fake Store API and filters catalog results locally.
 */
class FakeStoreProvider extends BaseProvider {

    public function __construct() {
        parent::__construct('FakeStoreProvider');
    }

    public function search($query) {
        $normalizedQuery = strtolower(trim($query));
        $cacheKey = 'search_' . md5($normalizedQuery);

        $cached = $this->getCachedResponse($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Fetch complete catalog from FakeStore
        $responseJson = $this->executeRequest($this->config['api_url']);
        if (!$responseJson) {
            return [];
        }

        $allProducts = json_decode($responseJson, true);
        $normalizedProducts = [];

        if (is_array($allProducts)) {
            foreach ($allProducts as $item) {
                $title = strtolower($item['title'] ?? '');
                $category = strtolower($item['category'] ?? '');
                $desc = strtolower($item['description'] ?? '');

                // Filter locally by search keywords
                if (strpos($title, $normalizedQuery) !== false || 
                    strpos($category, $normalizedQuery) !== false || 
                    strpos($desc, $normalizedQuery) !== false) {
                    
                    $price = (float)($item['price'] ?? 0.0);
                    if ($price <= 0) continue;

                    $rating = isset($item['rating']['rate']) ? (float)$item['rating']['rate'] : 4.0;
                    $reviews = isset($item['rating']['count']) ? (int)$item['rating']['count'] : 50;

                    $normalizedProducts[] = [
                        'name' => $item['title'],
                        'brand' => 'Generic',
                        'category' => $item['category'] ?? 'General',
                        'image_url' => $item['image'] ?? $this->resolveFallbackImage($item['title']),
                        'current_price' => $price,
                        'original_price' => $price,
                        'discount_rate' => 0,
                        'rating' => $rating,
                        'review_count' => $reviews,
                        'seller' => 'FakeStore Merchant',
                        'availability' => 1,
                        'website' => 'FakeStore',
                        'product_url' => 'https://fakestoreapi.com/products/' . ($item['id'] ?? 1),
                        'description' => $item['description'] ?? $item['title'],
                        'discount_percentage' => 0
                    ];
                }
            }
        }

        $this->saveCachedResponse($cacheKey, $normalizedProducts);
        return $normalizedProducts;
    }
}
