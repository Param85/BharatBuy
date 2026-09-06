<?php
require_once __DIR__ . '/BaseProvider.php';

/**
 * EbayProvider Class
 * 
 * Communicates with the official eBay Browse API using OAuth 2.0.
 */
class EbayProvider extends BaseProvider {
    
    public function __construct() {
        parent::__construct('EbayProvider');
    }

    /**
     * Executes product searches on the eBay Browse API.
     */
    public function search($query) {
        $normalizedQuery = strtolower(trim($query));
        $cacheKey = 'search_' . md5($normalizedQuery);
        
        // 1. Check database cache
        $cached = $this->getCachedResponse($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // 2. Obtain OAuth Access Token
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            error_log("EbayProvider: Failed to obtain access token. Skipping provider.");
            return [];
        }

        // 3. Prepare eBay Search API URL
        $url = $this->config['api_url'] . '?q=' . urlencode($query) . '&limit=5';
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'X-EBAY-C-MARKETPLACE-ID: EBAY_US'
        ];

        // 4. Execute request
        $responseJson = $this->executeRequest($url, ['headers' => $headers]);
        if (!$responseJson) {
            return [];
        }

        $data = json_decode($responseJson, true);
        $normalizedProducts = [];

        // 5. Parse and normalize results
        if (isset($data['itemSummaries']) && is_array($data['itemSummaries'])) {
            foreach ($data['itemSummaries'] as $item) {
                // Safely extract price
                $priceVal = isset($item['price']['value']) ? (float)$item['price']['value'] : 0.0;
                if ($priceVal <= 0) continue;

                // Extract image
                $imageUrl = isset($item['image']['imageUrl']) ? $item['image']['imageUrl'] : $this->resolveFallbackImage($item['title']);

                // Extract category name
                $categoryName = 'Electronics';
                if (isset($item['categories'][0]['categoryName'])) {
                    $categoryName = $item['categories'][0]['categoryName'];
                }

                // Extract seller username/score
                $sellerName = $item['seller']['username'] ?? (isset($item['seller']['feedbackScore']) ? "eBay Seller (Feedback: {$item['seller']['feedbackScore']})" : "eBay Seller");

                // Extract condition
                $condition = $item['condition'] ?? 'N/A';

                // Extract eBay Item ID
                $itemId = $item['itemId'] ?? 'N/A';

                // Extract shipping info
                $shippingCostVal = isset($item['shippingOptions'][0]['shippingCost']['value']) ? (float)$item['shippingOptions'][0]['shippingCost']['value'] : null;
                $shippingCostCurrency = isset($item['shippingOptions'][0]['shippingCost']['currency']) ? $item['shippingOptions'][0]['shippingCost']['currency'] : 'USD';
                
                if ($shippingCostVal === 0.0) {
                    $shippingInfo = "Free Shipping";
                } elseif ($shippingCostVal > 0) {
                    $shippingInfo = $shippingCostCurrency . ' ' . number_format($shippingCostVal, 2);
                } else {
                    $shippingInfo = "Standard Shipping";
                }

                // Normalize product schema matching instructions
                $normalizedProducts[] = [
                    'name' => $item['title'],
                    'brand' => $item['brand'] ?? 'Generic',
                    'category' => $categoryName,
                    'image_url' => $imageUrl,
                    'current_price' => $priceVal,
                    'original_price' => $priceVal,
                    'discount_rate' => 0,
                    'rating' => 4.4, // Standard default rating for summarized items
                    'review_count' => 85,
                    'seller' => $sellerName,
                    'seller_name' => $sellerName,
                    'item_condition' => $condition,
                    'item_id' => $itemId,
                    'shipping_info' => $shippingInfo,
                    'availability' => 1,
                    'website' => 'eBay',
                    'product_url' => $item['itemWebUrl'] ?? '#',
                    'description' => $item['shortDescription'] ?? $item['title'],
                    'discount_percentage' => 0
                ];
            }
        }

        // 6. Save results to cache
        $this->saveCachedResponse($cacheKey, $normalizedProducts);

        return $normalizedProducts;
    }

    /**
     * Obtains the application-access OAuth token from eBay, utilizing DB token caching.
     */
    private function getAccessToken() {
        $cacheKey = 'oauth_token';
        
        // 1. Check if token is cached
        $cachedToken = $this->getCachedResponse($cacheKey);
        if ($cachedToken && isset($cachedToken['access_token'])) {
            return $cachedToken['access_token'];
        }

        $clientId = $this->config['client_id'] ?? '';
        $clientSecret = $this->config['client_secret'] ?? '';

        // If credentials are placeholders, do not perform HTTP request
        if ($clientId === 'YOUR_EBAY_CLIENT_ID' || empty($clientId) || empty($clientSecret)) {
            error_log("EbayProvider: App credentials are not configured. Run with placeholders bypassed.");
            return null;
        }

        // 2. Make credentials request to eBay token endpoint
        $authHeader = 'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret);
        $headers = [
            $authHeader,
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $postFields = 'grant_type=client_credentials&scope=https://api.ebay.com/oauth/api_scope';

        $responseJson = $this->executeRequest($this->config['oauth_url'], [
            'headers' => $headers,
            'post' => true,
            'post_fields' => $postFields
        ]);

        if (!$responseJson) {
            return null;
        }

        $tokenData = json_decode($responseJson, true);
        if (isset($tokenData['access_token'])) {
            // Save token to cache (with a safety margin of 120 seconds before expiration)
            $expiresIn = isset($tokenData['expires_in']) ? (int)$tokenData['expires_in'] : 7200;
            
            // Override the default PROVIDER_CACHE_DURATION to save oauth token specifically
            global $pdo;
            $expiresAt = date('Y-m-d H:i:s', time() + ($expiresIn - 120));
            $jsonData = json_encode(['access_token' => $tokenData['access_token']]);
            $stmt = $pdo->prepare("
                INSERT INTO api_cache (provider_name, cache_key, response_data, expires_at) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE response_data = ?, expires_at = ?
            ");
            $stmt->execute([$this->providerName, $cacheKey, $jsonData, $expiresAt, $jsonData, $expiresAt]);

            return $tokenData['access_token'];
        }

        return null;
    }
}
