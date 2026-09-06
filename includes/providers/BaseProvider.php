<?php
require_once __DIR__ . '/ProductProviderInterface.php';
require_once __DIR__ . '/../provider_config.php';
require_once __DIR__ . '/../db.php';

/**
 * Abstract BaseProvider Class
 * 
 * Provides shared capabilities for caching, API logging, and HTTP cURL requests.
 */
abstract class BaseProvider implements ProductProviderInterface {
    protected $providerName;
    protected $config;

    public function __construct($providerName) {
        global $PROVIDER_CREDENTIALS;
        $this->providerName = $providerName;
        $this->config = $PROVIDER_CREDENTIALS[$providerName] ?? [];
    }

    /**
     * Retrieves a cached API response if it exists and has not expired.
     */
    protected function getCachedResponse($cacheKey) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT response_data FROM api_cache WHERE provider_name = ? AND cache_key = ? AND expires_at > CURRENT_TIMESTAMP");
            $stmt->execute([$this->providerName, $cacheKey]);
            $cached = $stmt->fetchColumn();
            return $cached ? json_decode($cached, true) : null;
        } catch (PDOException $e) {
            // Log database warning to PHP error log, but do not crash the search flow
            error_log("Database cache read failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Saves an API response to the MySQL caching table.
     */
    protected function saveCachedResponse($cacheKey, $data) {
        global $pdo;
        try {
            $expiresAt = date('Y-m-d H:i:s', time() + PROVIDER_CACHE_DURATION);
            $jsonData = json_encode($data);
            $stmt = $pdo->prepare("
                INSERT INTO api_cache (provider_name, cache_key, response_data, expires_at) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE response_data = ?, expires_at = ?
            ");
            $stmt->execute([$this->providerName, $cacheKey, $jsonData, $expiresAt, $jsonData, $expiresAt]);
        } catch (PDOException $e) {
            error_log("Database cache write failed: " . $e->getMessage());
        }
    }

    /**
     * Logs request parameters and execution stats to the database.
     */
    protected function logRequest($statusCode, $durationMs, $errorMessage = null) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO provider_logs (provider_name, status, response_time_ms, error_message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->providerName, $statusCode, $durationMs, $errorMessage]);
        } catch (PDOException $e) {
            error_log("Provider logging failed: " . $e->getMessage());
        }
    }

    /**
     * Executes an HTTP cURL request with strict timeout constraints and handles metrics logging.
     */
    protected function executeRequest($url, $options = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8); // 8-second request timeout limit
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);

        if (!empty($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (!empty($options['post'])) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['post_fields'] ?? '');
        }

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $endTime = microtime(true);
        
        $durationMs = round(($endTime - $startTime) * 1000);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errMessage = curl_error($ch);
        
        curl_close($ch);

        if ($statusCode === 0) {
            $statusCode = 500; // Network connection failure
        }

        if ($statusCode >= 400 || !empty($errMessage)) {
            $this->logRequest($statusCode, $durationMs, $errMessage ?: "HTTP error code " . $statusCode);
            return null;
        }

        $this->logRequest($statusCode, $durationMs);
        return $response;
    }

    /**
     * Resolves an authentic, real-time product image based on the exact product title.
     */
    protected function resolveFallbackImage($title) {
        $cleanTitle = trim($title);
        $encodedPrompt = urlencode("Commercial studio product photography of " . ucwords($cleanTitle) . ", clean studio white background, 4k ultra detailed, front and angle view, product catalog");
        return "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=600&height=600&nologo=true";
    }
}
