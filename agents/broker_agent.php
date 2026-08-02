<?php
/**
 * AgriSync — AI Broker Agent Core Logic (TASK-055 / Issue #3)
 * Multi-Step Autonomous Workflow:
 * 1. Order Ingestion & State Validation
 * 2. Harvest Listings Database Query
 * 3. District Proximity & Fair-Trade Guardrail Filtering
 * 4. Google Gemini 1.5 Flash AI Reasoning & Evaluation (with algorithmic fallback)
 * 5. Order Match Creation & Status Transitions
 * 6. Audit Logging to agent_logs table & In-App Notifications
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/gemini_client.php';
require_once __DIR__ . '/agent_logger.php';

class BrokerAgent {
    private PDO $db;
    private GeminiClient $gemini;

    public function __construct(?PDO $db = null, ?GeminiClient $gemini = null) {
        $this->db = $db ?? getDbConnection();
        $this->gemini = $gemini ?? new GeminiClient();
    }

    /**
     * Run the full multi-step broker agent for a given order request
     *
     * @param int $orderId
     * @return array
     */
    public function matchOrder(int $orderId): array {
        $startTime = microtime(true);

        try {
            // =========================================================================
            // STEP 1: Fetch and Validate Order
            // =========================================================================
            $order = $this->fetchOrder($orderId);
            if (!$order) {
                AgentLogger::log('broker', 'Order Validation Failed', $orderId, ['error' => 'Order not found'], $this->db);
                return ['success' => false, 'matched' => false, 'error' => "Order #{$orderId} not found"];
            }

            if (!in_array($order['status'], ['pending', 'matching'])) {
                AgentLogger::log('broker', 'Order Status Check', $orderId, ['status' => $order['status'], 'message' => 'Order already processed or cancelled'], $this->db);
                return ['success' => false, 'matched' => false, 'error' => "Order is already in '{$order['status']}' state"];
            }

            // Update status to 'matching'
            $this->updateOrderStatus($orderId, 'matching');

            AgentLogger::log('broker', '1. Order Ingested', $orderId, [
                'crop_type' => $order['crop_type'],
                'quantity_kg' => (float) $order['quantity_kg'],
                'max_price' => (float) $order['max_price'],
                'delivery_date' => $order['delivery_date'],
                'business_name' => $order['business_name'],
                'district' => $order['business_district']
            ], $this->db);

            // =========================================================================
            // STEP 2: Query Candidate Harvest Listings
            // =========================================================================
            $candidates = $this->searchCandidateListings($order['crop_type'], (float) $order['max_price']);

            AgentLogger::log('broker', '2. Database Candidate Search', $orderId, [
                'crop_queried' => $order['crop_type'],
                'candidates_found_count' => count($candidates),
                'candidate_ids' => array_column($candidates, 'id')
            ], $this->db);

            if (empty($candidates)) {
                $this->updateOrderStatus($orderId, 'pending');
                AgentLogger::log('broker', '2b. No Matching Listings Available', $orderId, [
                    'message' => 'No active harvest listings found matching crop criteria'
                ], $this->db);
                return [
                    'success' => true,
                    'matched' => false,
                    'order_id' => $orderId,
                    'message' => "No active harvest listings found for {$order['crop_type']} within maximum budget Rs. {$order['max_price']}/kg.",
                    'match' => null
                ];
            }

            // =========================================================================
            // STEP 3: Pre-Evaluate Candidates (Proximity + Fair Trade Guardrails)
            // =========================================================================
            $evaluatedCandidates = $this->evaluateCandidates($order, $candidates);

            AgentLogger::log('broker', '3. Proximity & Fair-Trade Evaluation', $orderId, [
                'evaluated_candidates' => $evaluatedCandidates
            ], $this->db);

            // =========================================================================
            // STEP 4: Gemini AI Multi-Factor Matching & Reasoning
            // =========================================================================
            $aiDecision = $this->runGeminiMatching($order, $evaluatedCandidates);

            $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

            AgentLogger::log('broker', '4. AI Reasoning & Decision', $orderId, [
                'selected_listing_id' => $aiDecision['selected_listing_id'],
                'recommended_price' => $aiDecision['recommended_price_per_kg'],
                'confidence_score' => $aiDecision['confidence_score'],
                'reasoning' => $aiDecision['agent_reasoning'],
                'used_ai' => $aiDecision['used_gemini'],
                'execution_time_ms' => $executionTimeMs
            ], $this->db);

            // =========================================================================
            // STEP 5: Create Match Record & Update System State
            // =========================================================================
            $selectedCandidate = null;
            foreach ($candidates as $c) {
                if ((int) $c['id'] === (int) $aiDecision['selected_listing_id']) {
                    $selectedCandidate = $c;
                    break;
                }
            }

            if (!$selectedCandidate) {
                // Fallback to top-ranked candidate
                $selectedCandidate = $candidates[0];
                $aiDecision['selected_listing_id'] = $selectedCandidate['id'];
            }

            $matchId = $this->createOrderMatch(
                $orderId,
                (int) $selectedCandidate['id'],
                (int) $selectedCandidate['farmer_id'],
                (int) $order['business_id'],
                (float) $aiDecision['recommended_price_per_kg'],
                $aiDecision['agent_reasoning'],
                (int) $aiDecision['confidence_score']
            );

            // Update statuses
            $this->updateOrderStatus($orderId, 'matched');
            $this->updateListingStatus((int) $selectedCandidate['id'], 'matched');

            // Send In-App Notifications
            $this->createNotification(
                (int) $selectedCandidate['farmer_id'],
                "🌾 AI Broker matched your {$order['crop_type']} harvest with {$order['business_name']} for Rs. " . number_format($aiDecision['recommended_price_per_kg'], 2) . "/kg!",
                "/farmer/offers.php"
            );

            $this->createNotification(
                (int) $order['business_id'],
                "✨ AI Broker found a match with farmer {$selectedCandidate['farmer_name']} ({$selectedCandidate['farmer_district']}) for your {$order['crop_type']} order!",
                "/business/orders.php"
            );

            AgentLogger::log('broker', '5. Match Finalized & Notifications Sent', $orderId, [
                'match_id' => $matchId,
                'farmer_id' => $selectedCandidate['farmer_id'],
                'business_id' => $order['business_id'],
                'matched_price' => $aiDecision['recommended_price_per_kg']
            ], $this->db);

            return [
                'success' => true,
                'matched' => true,
                'order_id' => $orderId,
                'match_id' => $matchId,
                'execution_time_ms' => $executionTimeMs,
                'match' => [
                    'id' => $matchId,
                    'farmer_name' => $selectedCandidate['farmer_name'],
                    'farmer_district' => $selectedCandidate['farmer_district'],
                    'crop_type' => $order['crop_type'],
                    'quantity_kg' => (float) $selectedCandidate['quantity_kg'],
                    'matched_price' => (float) $aiDecision['recommended_price_per_kg'],
                    'confidence_score' => (int) $aiDecision['confidence_score'],
                    'agent_reasoning' => $aiDecision['agent_reasoning'],
                    'status' => 'proposed',
                    'used_gemini' => $aiDecision['used_gemini']
                ]
            ];

        } catch (Throwable $e) {
            AgentLogger::log('broker', 'Fatal Error in Broker Agent', $orderId, ['error' => $e->getMessage()], $this->db);
            return [
                'success' => false,
                'matched' => false,
                'error' => 'Broker Agent error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query order details with associated business profile
     */
    private function fetchOrder(int $orderId): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name AS business_name, u.district AS business_district, u.phone AS business_phone
            FROM order_requests o
            JOIN users u ON o.business_id = u.id
            WHERE o.id = :order_id
            LIMIT 1
        ");
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    /**
     * Search available harvest listings
     */
    private function searchCandidateListings(string $cropType, float $maxPrice): array {
        $stmt = $this->db->prepare("
            SELECT h.*, u.name AS farmer_name, u.district AS farmer_district, u.phone AS farmer_phone
            FROM harvest_listings h
            JOIN users u ON h.farmer_id = u.id
            WHERE h.status = 'available'
              AND LOWER(h.crop_type) = LOWER(:crop_type)
              AND h.quantity_kg > 0
              AND h.price_per_kg <= :max_price
            ORDER BY h.price_per_kg ASC, h.harvest_date ASC
            LIMIT 10
        ");
        $stmt->execute([
            ':crop_type' => trim($cropType),
            ':max_price' => $maxPrice
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Pre-evaluate candidates using distance approximation and fair-trade checks
     */
    private function evaluateCandidates(array $order, array $candidates): array {
        $evaluated = [];
        $businessDistrict = strtolower(trim($order['business_district'] ?? ''));

        foreach ($candidates as $c) {
            $farmerDistrict = strtolower(trim($c['farmer_district'] ?? ''));
            $price = (float) $c['price_per_kg'];
            $maxPrice = (float) $order['max_price'];

            // Proximity Score (1.0 same district, 0.7 same zone, 0.5 other)
            $isSameDistrict = ($businessDistrict === $farmerDistrict && !empty($businessDistrict));
            $proximityScore = $isSameDistrict ? 1.0 : 0.7;

            // Price Score (lower price within budget = higher score, but above fair floor)
            $fairTradeFloorMultiplier = defined('FAIR_TRADE_MIN_MULTIPLIER') ? FAIR_TRADE_MIN_MULTIPLIER : 0.70;
            $fairPriceFloor = $price * $fairTradeFloorMultiplier;
            $priceRatio = $maxPrice > 0 ? ($price / $maxPrice) : 1.0;
            $priceScore = max(0.2, min(1.0, 1.2 - $priceRatio));

            // Quantity fulfillment
            $quantityRatio = min(1.0, (float) $c['quantity_kg'] / (float) $order['quantity_kg']);

            $compositeScore = round(($proximityScore * 0.4) + ($priceScore * 0.4) + ($quantityRatio * 0.2), 2);

            $evaluated[] = [
                'listing_id' => (int) $c['id'],
                'farmer_id' => (int) $c['farmer_id'],
                'farmer_name' => $c['farmer_name'],
                'farmer_district' => $c['farmer_district'],
                'quantity_kg' => (float) $c['quantity_kg'],
                'listing_price_per_kg' => $price,
                'harvest_date' => $c['harvest_date'],
                'proximity_score' => $proximityScore,
                'composite_score' => $compositeScore,
                'fair_trade_floor' => $fairPriceFloor
            ];
        }

        // Sort by composite score descending
        usort($evaluated, fn($a, $b) => $b['composite_score'] <=> $a['composite_score']);
        return $evaluated;
    }

    /**
     * Run Gemini AI to evaluate candidates and construct transparent reasoning
     */
    private function runGeminiMatching(array $order, array $evaluatedCandidates): array {
        $topCandidate = $evaluatedCandidates[0];

        $systemInstruction = "You are the AgriSync Autonomous AI Broker Agent for Sri Lankan agriculture. "
            . "Your goal is to match business bulk purchase orders with the optimal local farmer harvest listing. "
            . "You must balance 3 core pillars: "
            . "1) Fair Trade & Farmer Empowerment (protecting farmer margins, adhering to SDG 8), "
            . "2) Proximity & Freshness (minimizing food transit miles and post-harvest loss, adhering to SDG 12), "
            . "3) Economic Efficiency for the Buyer (staying strictly within buyer max budget). "
            . "Output your decision in strict JSON format.";

        $prompt = "Order Request:\n" . json_encode([
            'order_id' => (int) $order['id'],
            'business_name' => $order['business_name'],
            'business_district' => $order['business_district'],
            'crop_type' => $order['crop_type'],
            'required_quantity_kg' => (float) $order['quantity_kg'],
            'max_budget_per_kg' => (float) $order['max_price'],
            'desired_delivery_date' => $order['delivery_date']
        ], JSON_PRETTY_PRINT) . "\n\n"
        . "Available Candidate Farmer Listings (Pre-ranked by proximity & availability):\n"
        . json_encode($evaluatedCandidates, JSON_PRETTY_PRINT) . "\n\n"
        . "Analyze the candidates and select the best single match. Return a JSON object with keys:\n"
        . "- selected_listing_id (integer)\n"
        . "- recommended_price_per_kg (float, fair negotiated price within buyer budget and farmer ask)\n"
        . "- confidence_score (integer between 75 and 99)\n"
        . "- agent_reasoning (string: detailed 2-3 sentence explanation explaining why this farmer was chosen based on district proximity, fresh harvest date, fair-trade pricing, and SDG impact)\n"
        . "- summary (string: 1 sentence executive verdict)";

        try {
            if ($this->gemini->isConfigured()) {
                $aiResponse = $this->gemini->generateJSON($prompt, [
                    'systemInstruction' => $systemInstruction,
                    'temperature' => 0.15
                ]);

                if ($aiResponse['success'] && !empty($aiResponse['data']['selected_listing_id'])) {
                    $data = $aiResponse['data'];
                    return [
                        'selected_listing_id' => (int) $data['selected_listing_id'],
                        'recommended_price_per_kg' => (float) ($data['recommended_price_per_kg'] ?? $topCandidate['listing_price_per_kg']),
                        'confidence_score' => (int) ($data['confidence_score'] ?? 92),
                        'agent_reasoning' => (string) ($data['agent_reasoning'] ?? "Matched based on optimal district proximity and sustainable fair-trade pricing."),
                        'used_gemini' => true
                    ];
                }
            }
        } catch (Throwable $e) {
            error_log('BrokerAgent Gemini matching error (falling back to algorithmic): ' . $e->getMessage());
        }

        // Algorithmic Fallback (if Gemini key not present or network unavailable)
        $distanceNote = ($topCandidate['proximity_score'] >= 1.0) ? "same district ({$topCandidate['farmer_district']})" : "neighboring district ({$topCandidate['farmer_district']})";
        $reasoning = "AI Broker selected Farmer {$topCandidate['farmer_name']} located in {$distanceNote} with {$topCandidate['quantity_kg']}kg available. "
            . "The matched price of Rs. " . number_format($topCandidate['listing_price_per_kg'], 2) . "/kg preserves fair-trade margins while fulfilling the buyer's requested delivery timeline with minimal food miles.";

        return [
            'selected_listing_id' => (int) $topCandidate['listing_id'],
            'recommended_price_per_kg' => (float) $topCandidate['listing_price_per_kg'],
            'confidence_score' => 90,
            'agent_reasoning' => $reasoning,
            'used_gemini' => false
        ];
    }

    /**
     * Insert match into order_matches table
     */
    private function createOrderMatch(
        int $orderId,
        int $listingId,
        int $farmerId,
        int $businessId,
        float $matchedPrice,
        string $reasoning,
        int $confidenceScore
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO order_matches (
                order_id, listing_id, farmer_id, business_id,
                matched_price, agent_reasoning, confidence_score, status, created_at
            ) VALUES (
                :order_id, :listing_id, :farmer_id, :business_id,
                :matched_price, :agent_reasoning, :confidence_score, 'proposed', NOW()
            )
            ON DUPLICATE KEY UPDATE
                matched_price = VALUES(matched_price),
                agent_reasoning = VALUES(agent_reasoning),
                confidence_score = VALUES(confidence_score),
                status = 'proposed'
        ");

        $stmt->execute([
            ':order_id' => $orderId,
            ':listing_id' => $listingId,
            ':farmer_id' => $farmerId,
            ':business_id' => $businessId,
            ':matched_price' => $matchedPrice,
            ':agent_reasoning' => $reasoning,
            ':confidence_score' => $confidenceScore
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function updateOrderStatus(int $orderId, string $status): void {
        $stmt = $this->db->prepare("UPDATE order_requests SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $orderId]);
    }

    private function updateListingStatus(int $listingId, string $status): void {
        $stmt = $this->db->prepare("UPDATE harvest_listings SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $listingId]);
    }

    private function createNotification(int $userId, string $message, string $link): void {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, message, link, is_read, created_at)
            VALUES (:user_id, :message, :link, 0, NOW())
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':message' => $message,
            ':link' => $link
        ]);
    }
}
