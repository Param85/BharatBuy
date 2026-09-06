<?php
require_once __DIR__ . '/BaseProvider.php';

/**
 * DummyJsonProvider Class
 * 
 * Retrieves real product feeds from DummyJSON Products API.
 */
class DummyJsonProvider extends BaseProvider {

    public function __construct() {
        parent::__construct('DummyJsonProvider');
    }

    public function search($query) {
        $normalizedQuery = strtolower(trim($query));
        $cacheKey = 'search_' . md5($normalizedQuery);

        $cached = $this->getCachedResponse($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $url = $this->config['api_url'] . '?q=' . urlencode($query);
        $responseJson = $this->executeRequest($url);
        if (!$responseJson) {
            return [];
        }

        $data = json_decode($responseJson, true);
        $normalizedProducts = [];

        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $item) {
                $price = (float)$item['price'];
                $discount = isset($item['discountPercentage']) ? (float)$item['discountPercentage'] : 0.0;
                
                // Calculate original price
                $origPrice = $price;
                if ($discount > 0) {
                    $origPrice = round($price / (1 - ($discount / 100)), 2);
                }

                // Setup review volume simulation
                $reviews = isset($item['stock']) ? ($item['stock'] * 4 + 12) : 150;

                $normalizedProducts[] = [
                    'name' => $item['title'],
                    'brand' => $item['brand'] ?? 'Generic',
                    'category' => $item['category'] ?? 'Electronics',
                    'image_url' => $item['thumbnail'] ?? $this->resolveFallbackImage($item['title']),
                    'current_price' => $price,
                    'original_price' => $origPrice,
                    'discount_rate' => (int)round($discount),
                    'rating' => isset($item['rating']) ? (float)$item['rating'] : 4.2,
                    'review_count' => $reviews,
                    'seller' => 'DummyJSON Direct',
                    'availability' => 1,
                    'website' => 'DummyJSON',
                    'product_url' => 'https://dummyjson.com/products/' . ($item['id'] ?? 1),
                    'description' => $item['description'] ?? $item['title'],
                    'discount_percentage' => (int)round($discount)
                ];
            }
        }

        $this->saveCachedResponse($cacheKey, $normalizedProducts);
        return $normalizedProducts;
    }
}
