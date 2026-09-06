<?php
require_once __DIR__ . '/db.php';

/**
 * Fetches the live USD to INR exchange rate with a 12-hour database cache layer.
 * Falls back to 95.22 if the external API is unreachable.
 *
 * @return float Current exchange rate.
 */
function getUsdToInrRate() {
    global $pdo;
    
    $cache_key = 'usd_to_inr';
    $provider_name = 'ExchangeRateAPI';
    $fallback_rate = 95.22;
    
    // 1. Try to read active cache from DB
    try {
        $stmt = $pdo->prepare("
            SELECT response_data 
            FROM api_cache 
            WHERE provider_name = ? AND cache_key = ? AND expires_at > CURRENT_TIMESTAMP
            LIMIT 1
        ");
        $stmt->execute([$provider_name, $cache_key]);
        $cached_val = $stmt->fetchColumn();
        if ($cached_val !== false) {
            $rate = (float)$cached_val;
            if ($rate > 0) {
                return $rate;
            }
        }
    } catch (PDOException $e) {
        error_log("Database cache read failed in getUsdToInrRate: " . $e->getMessage());
    }
    
    // 2. Fetch fresh exchange rates from API
    try {
        // Use a 3-second timeout for the HTTP request to keep search fast
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 3
            ]
        ]);
        
        $response = file_get_contents('https://open.er-api.com/v6/latest/USD', false, $ctx);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['result']) && $data['result'] === 'success' && isset($data['rates']['INR'])) {
                $rate = (float)$data['rates']['INR'];
                
                if ($rate > 0) {
                    // Update cache in database for 12 hours (43200 seconds)
                    $expires_at = date('Y-m-d H:i:s', time() + 43200);
                    
                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO api_cache (provider_name, cache_key, response_data, expires_at) 
                            VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                                response_data = VALUES(response_data), 
                                expires_at = VALUES(expires_at)
                        ");
                        $stmt->execute([$provider_name, $cache_key, (string)$rate, $expires_at]);
                    } catch (PDOException $dbEx) {
                        error_log("Database cache write failed in getUsdToInrRate: " . $dbEx->getMessage());
                    }
                    
                    return $rate;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Failed to fetch fresh exchange rate from API: " . $e->getMessage());
    }
    
    // 3. Return fallback rate if everything fails
    return $fallback_rate;
}
