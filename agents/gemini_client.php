<?php
/**
 * AgriSync — Gemini API Client Wrapper
 * Handles cURL communication with Google Gemini API with JSON mode, multi-model fallback chaining, and error recovery.
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}

class GeminiClient {
    private string $apiKey;
    private string $model;
    private int $timeout;
    private array $fallbackModels;

    /**
     * @param string|null $apiKey Custom API key or fallback to constant
     * @param string|null $model Custom model or fallback to constant
     * @param int $timeout Request timeout in seconds
     */
    public function __construct(?string $apiKey = null, ?string $model = null, int $timeout = 25) {
        $this->apiKey = $apiKey ?? (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '');
        $this->model = $model ?? (defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-2.5-flash');
        $this->timeout = $timeout;
        
        // Prioritized fallback models in case primary encounters 404, 429 rate limit, or maintenance
        $this->fallbackModels = [
            'gemini-2.5-flash',
            'gemini-flash-latest',
            'gemini-2.0-flash'
        ];
    }

    /**
     * Check if a valid API key is configured
     *
     * @return bool
     */
    public function isConfigured(): bool {
        return !empty($this->apiKey) && $this->apiKey !== 'YOUR_GEMINI_API_KEY';
    }

    /**
     * Send prompt to Gemini and return raw text output with automatic multi-model fallback
     *
     * @param string $prompt
     * @param array $options [temperature, maxTokens, systemInstruction]
     * @return array ['success' => bool, 'text' => string|null, 'error' => string|null, 'raw' => array|null, 'model_used' => string|null]
     */
    public function generateContent(string $prompt, array $options = []): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'text' => null,
                'error' => 'Gemini API key is not configured. Please set GEMINI_API_KEY in config/constants.php.',
                'raw' => null,
                'model_used' => null
            ];
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.2,
                'maxOutputTokens' => $options['maxTokens'] ?? 2048,
                'topP' => $options['topP'] ?? 0.95,
            ]
        ];

        if (isset($options['systemInstruction'])) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $options['systemInstruction']]
                ]
            ];
        }

        if (!empty($options['jsonMode'])) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

        // Build list of models to try (primary first, followed by fallbacks without duplicates)
        $modelsToTry = array_values(array_unique(array_merge([$this->model], $this->fallbackModels)));
        $lastError = 'Unknown error';
        $lastRaw = null;

        foreach ($modelsToTry as $targetModel) {
            $result = $this->executeCurlForModel($targetModel, $payload);
            if ($result['success']) {
                $result['model_used'] = $targetModel;
                return $result;
            }
            $lastError = $result['error'] ?? 'API call failed';
            $lastRaw = $result['raw'] ?? null;
        }

        return [
            'success' => false,
            'text' => null,
            'error' => "All Gemini model candidates failed. Last error: " . $lastError,
            'raw' => $lastRaw,
            'model_used' => null
        ];
    }

    /**
     * Send prompt to Gemini expecting structured JSON response
     *
     * @param string $prompt
     * @param array $options
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null, 'raw_text' => string|null, 'model_used' => string|null]
     */
    public function generateJSON(string $prompt, array $options = []): array {
        $options['jsonMode'] = true;
        $response = $this->generateContent($prompt, $options);

        if (!$response['success']) {
            return [
                'success' => false,
                'data' => null,
                'error' => $response['error'],
                'raw_text' => null,
                'model_used' => null
            ];
        }

        $rawText = $response['text'] ?? '';
        
        // Clean markdown code fence if wrapped
        $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
        $decoded = json_decode($cleanJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'Failed to parse Gemini response as JSON: ' . json_last_error_msg(),
                'raw_text' => $rawText,
                'model_used' => $response['model_used'] ?? null
            ];
        }

        return [
            'success' => true,
            'data' => $decoded,
            'error' => null,
            'raw_text' => $rawText,
            'model_used' => $response['model_used'] ?? null
        ];
    }

    /**
     * Execute cURL request for a specific model
     *
     * @param string $model
     * @param array $payload
     * @return array
     */
    private function executeCurlForModel(string $model, array $payload): array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($this->apiKey);
        $jsonPayload = json_encode($payload);

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonPayload)
                ],
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return [
                    'success' => false,
                    'text' => null,
                    'error' => "cURL Error for model {$model}: " . $curlError,
                    'raw' => null
                ];
            }
        } else {
            // Stream context fallback when ext-curl is not loaded
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n" .
                                "Content-Length: " . strlen($jsonPayload) . "\r\n",
                    'content' => $jsonPayload,
                    'timeout' => $this->timeout,
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ];
            $context = stream_context_create($opts);
            $result = @file_get_contents($url, false, $context);
            $httpCode = 200;
            if (isset($http_response_header) && preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0] ?? '', $matches)) {
                $httpCode = (int) $matches[1];
            }

            if ($result === false) {
                return [
                    'success' => false,
                    'text' => null,
                    'error' => "HTTP request failed for model {$model}.",
                    'raw' => null
                ];
            }
        }

        $responseArray = json_decode($result, true);

        if ($httpCode !== 200) {
            $apiErrorMsg = $responseArray['error']['message'] ?? "HTTP error {$httpCode} from model {$model}";
            return [
                'success' => false,
                'text' => null,
                'error' => $apiErrorMsg,
                'raw' => $responseArray
            ];
        }

        $candidateText = $responseArray['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($candidateText === null) {
            return [
                'success' => false,
                'text' => null,
                'error' => "Model {$model} returned an empty candidate response.",
                'raw' => $responseArray
            ];
        }

        return [
            'success' => true,
            'text' => $candidateText,
            'error' => null,
            'raw' => $responseArray
        ];
    }
}
