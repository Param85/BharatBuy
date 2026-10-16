<?php
require_once __DIR__ . '/config.php';

class GeminiService {
    private string $apiKey;
    
    // Ordered list of models to try for maximum availability and quota resilience
    private array $models = [
        'gemini-3.5-flash-lite',
        'gemini-flash-lite-latest',
        'gemini-3.1-flash-lite',
        'gemini-3.5-flash'
    ];

    public function __construct() {
        // Retrieve key from config constant
        $this->apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        
        // Throw exception if the key is empty or still the default placeholder
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            throw new Exception("Gemini API key is not configured.");
        }
    }

    /**
     * Sends a request to Google Gemini API with automatic multi-model failover.
     * 
     * @param string $userMessage The current message sent by the user
     * @param string $systemPrompt The system instruction (including guidelines and db context)
     * @param array $history Previous conversation history
     * @return string The generated response
     * @throws Exception On network or API errors
     */
    public function generateResponse(string $userMessage, string $systemPrompt, array $history = []): string {
        // Build contents payload
        $contents = [];

        // 1. Append history
        foreach ($history as $turn) {
            $role = ($turn['role'] === 'bot' || $turn['role'] === 'model') ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $turn['text']]
                ]
            ];
        }

        // 2. Append current user query
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        // 3. Assemble API payload
        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800
            ]
        ];

        // Try models with automatic failover
        $lastException = null;
        foreach ($this->models as $model) {
            try {
                return $this->sendPostRequest($model, $payload);
            } catch (Exception $e) {
                $lastException = $e;
                // If it's a 429 (quota limit) or 503 (server overloaded), fail over to next model
                if (strpos($e->getMessage(), '429') !== false || strpos($e->getMessage(), '503') !== false) {
                    continue;
                }
                // For other fatal errors, throw immediately
                throw $e;
            }
        }

        throw $lastException ?? new Exception("All Gemini models failed to respond.");
    }

    /**
     * Executes the POST request for a specific model using cURL.
     */
    private function sendPostRequest(string $model, array $payload): string {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        $jsonData = json_encode($payload);

        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception("Failed to initialize cURL session.");
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $this->apiKey
        ]);
        
        // Enforce strict timeouts (10s max total, 5s connection timeout)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        // Execute request
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception("cURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception("Gemini API HTTP Error Code: " . $httpCode . " (Model: {$model}). Response: " . $response);
        }

        $resultData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to parse Gemini response JSON.");
        }

        // Extract response content
        if (isset($resultData['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($resultData['candidates'][0]['content']['parts'][0]['text']);
        }

        // Handle API safety blocks or errors
        if (isset($resultData['promptFeedback']['blockReason'])) {
            throw new Exception("Gemini blocked prompt due to: " . $resultData['promptFeedback']['blockReason']);
        }

        throw new Exception("Invalid response structure from Gemini API. Full response: " . $response);
    }
}
