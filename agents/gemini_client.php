<?php
/**
 * AgriSync — Gemini API Client Wrapper
 * Handles cURL communication with Google Gemini 1.5 Flash API with JSON mode and fallback safeguards.
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}

class GeminiClient {
    private string $apiKey;
    private string $model;
    private string $apiUrl;
    private int $timeout;

    /**
     * @param string|null $apiKey Custom API key or fallback to constant
     * @param string|null $model Custom model or fallback to constant
     * @param int $timeout Request timeout in seconds
     */
    public function __construct(?string $apiKey = null, ?string $model = null, int $timeout = 25) {
        $this->apiKey = $apiKey ?? (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '');
        $this->model = $model ?? (defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-1.5-flash');
        $this->timeout = $timeout;
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
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
     * Send prompt to Gemini and return raw text output
     *
     * @param string $prompt
     * @param array $options [temperature, maxTokens, systemInstruction]
     * @return array ['success' => bool, 'text' => string|null, 'error' => string|null, 'raw' => array|null]
     */
    public function generateContent(string $prompt, array $options = []): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'text' => null,
                'error' => 'Gemini API key is not configured. Please set GEMINI_API_KEY in config/constants.php.',
                'raw' => null
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

        return $this->executeCurl($payload);
    }

    /**
     * Send prompt to Gemini expecting structured JSON response
     *
     * @param string $prompt
     * @param array $options
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null, 'raw_text' => string|null]
     */
    public function generateJSON(string $prompt, array $options = []): array {
        $options['jsonMode'] = true;
        $response = $this->generateContent($prompt, $options);

        if (!$response['success']) {
            return [
                'success' => false,
                'data' => null,
                'error' => $response['error'],
                'raw_text' => null
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
                'raw_text' => $rawText
            ];
        }

        return [
            'success' => true,
            'data' => $decoded,
            'error' => null,
            'raw_text' => $rawText
        ];
    }

    /**
     * Execute cURL request to Gemini endpoint
     *
     * @param array $payload
     * @return array
     */
    private function executeCurl(array $payload): array {
        $url = $this->apiUrl . '?key=' . urlencode($this->apiKey);
        $jsonPayload = json_encode($payload);

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
                'error' => 'cURL Error communicating with Gemini API: ' . $curlError,
                'raw' => null
            ];
        }

        $responseArray = json_decode($result, true);

        if ($httpCode !== 200) {
            $apiErrorMsg = $responseArray['error']['message'] ?? "HTTP error {$httpCode} from Gemini API";
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
                'error' => 'Gemini API returned an empty or invalid candidate response.',
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
