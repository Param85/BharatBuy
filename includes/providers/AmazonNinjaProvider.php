<?php
require_once __DIR__ . '/BaseProvider.php';

/**
 * AmazonNinjaProvider Class
 * 
 * Communicates with the OpenWeb Ninja Realtime Amazon Data Search API
 * specifically targeting Amazon India (country=in) for authentic products, INR pricing, and accurate photos.
 */
class AmazonNinjaProvider extends BaseProvider {
    
    public function __construct() {
        parent::__construct('AmazonNinjaProvider');
    }

    /**
     * Executes product searches on Amazon India via OpenWeb Ninja.
     */
    public function search($query) {
        $normalizedQuery = strtolower(trim($query));
        $cacheKey = 'search_in_' . md5($normalizedQuery);
        
        // 1. Check database cache
        $cached = $this->getCachedResponse($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // 2. Prepare API URL & Headers
        $apiUrl = $this->config['api_url'] ?? 'https://api.openwebninja.com/realtime-amazon-data/search';
        $apiKey = $this->config['api_key'] ?? '';
        
        if (empty($apiKey)) {
            error_log("AmazonNinjaProvider: API Key not configured. Skipping provider.");
            return [];
        }

        // Query Amazon India marketplace directly
        $url = $apiUrl . '?query=' . urlencode($query) . '&country=in';
        $headers = [
            'X-API-Key: ' . $apiKey,
            'Content-Type: application/json'
        ];

        // 3. Execute request
        $responseJson = $this->executeRequest($url, ['headers' => $headers]);
        if (!$responseJson) {
            return [];
        }

        $data = json_decode($responseJson, true);
        $normalizedProducts = [];

        // 4. Parse and normalize results
        if (isset($data['data']['products']) && is_array($data['data']['products'])) {
            foreach ($data['data']['products'] as $item) {
                // Parse current price (handling ₹, $, commas, text)
                $priceStr = $item['product_price'] ?? '';
                $cleanPrice = preg_replace('/[^0-9.]/', '', str_replace(',', '', $priceStr));
                $priceVal = (float)$cleanPrice;
                if ($priceVal <= 0) continue;

                // Parse original price
                $origPriceStr = $item['product_original_price'] ?? '';
                $cleanOrig = !empty($origPriceStr) ? preg_replace('/[^0-9.]/', '', str_replace(',', '', $origPriceStr)) : '';
                $origPriceVal = !empty($cleanOrig) ? (float)$cleanOrig : $priceVal;
                if ($origPriceVal < $priceVal) $origPriceVal = $priceVal;

                // Authentic official Amazon photo
                $imageUrl = $item['product_photo'] ?? $this->resolveFallbackImage($item['product_title']);

                // Rating & Reviews
                $rating = isset($item['product_star_rating']) ? (float)$item['product_star_rating'] : 4.3;
                $reviewCount = isset($item['product_num_ratings']) ? (int)$item['product_num_ratings'] : 120;

                // Discount percentage
                $discountPercentage = 0;
                if ($origPriceVal > $priceVal) {
                    $discountPercentage = (int)round((($origPriceVal - $priceVal) / $origPriceVal) * 100);
                }

                // Extract Brand from title
                $titleWords = explode(' ', trim($item['product_title']));
                $brand = !empty($titleWords[0]) ? $titleWords[0] : 'Amazon Brand';

                // Normalize product schema
                $normalizedProducts[] = [
                    'name' => $item['product_title'],
                    'brand' => $brand,
                    'category' => 'Electronics',
                    'image_url' => $imageUrl,
                    'current_price' => $priceVal,
                    'original_price' => $origPriceVal,
                    'currency' => 'INR', // Native INR pricing from Amazon India
                    'discount_rate' => $discountPercentage / 100.0,
                    'rating' => $rating,
                    'review_count' => $reviewCount,
                    'seller' => 'Amazon India',
                    'availability' => 1,
                    'website' => 'Amazon',
                    'product_url' => $item['product_url'] ?? 'https://www.amazon.in',
                    'description' => $item['product_title'],
                    'discount_percentage' => $discountPercentage
                ];
            }
        }

        // 5. Save results to cache
        $this->saveCachedResponse($cacheKey, $normalizedProducts);

        return $normalizedProducts;
    }
}
