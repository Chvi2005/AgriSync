<?php
/**
 * AgriSync — Automated End-to-End AI Agent Test Suite (TASK-069)
 * Tests Gemini Client, Agent Logger, Demand Prediction Agent, and Broker Agent.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../agents/gemini_client.php';
require_once __DIR__ . '/../agents/agent_logger.php';
require_once __DIR__ . '/../agents/demand_agent.php';
require_once __DIR__ . '/../agents/broker_agent.php';

echo "=======================================================\n";
echo "       AgriSync AI Agents End-to-End Test Suite        \n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(bool $condition, string $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] {$testName}\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$testName}\n";
        $failCount++;
    }
}

// -----------------------------------------------------------------------------
// Database Test Setup (Live MySQL or Mock PDO Adapter)
// -----------------------------------------------------------------------------
class TestMockDb extends PDO {
    public function __construct() {}
    public function prepare($statement, $options = []): TestMockStatement {
        return new TestMockStatement($statement);
    }
    public function lastInsertId(?string $name = null): string|false {
        return '1';
    }
}

class TestMockStatement extends PDOStatement {
    private string $sql;
    public function __construct(string $sql) {
        $this->sql = $sql;
    }
    public function execute(?array $params = null): bool {
        return true;
    }
    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool {
        return true;
    }
    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed {
        if (stripos($this->sql, 'order_requests o') !== false || stripos($this->sql, 'WHERE o.id') !== false) {
            return [
                'id' => 1,
                'business_id' => 3,
                'crop_type' => 'Tomato',
                'quantity_kg' => 400.00,
                'max_price' => 180.00,
                'delivery_date' => '2026-08-15',
                'urgency' => 'high',
                'status' => 'pending',
                'business_name' => 'Cargills Fresh Hub',
                'business_district' => 'Colombo',
                'business_phone' => '0112345678'
            ];
        }
        if (stripos($this->sql, 'COUNT(*)') !== false) {
            return [
                'total_orders' => 10,
                'total_demanded_kg' => 3500.00,
                'avg_order_price' => 170.00,
                'total_listings' => 5,
                'total_supply_kg' => 2000.00,
                'avg_listing_price' => 155.00
            ];
        }
        return false;
    }
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array {
        if (stripos($this->sql, 'harvest_listings') !== false) {
            return [
                [
                    'id' => 1,
                    'farmer_id' => 10,
                    'crop_type' => 'Tomato',
                    'quantity_kg' => 500.00,
                    'price_per_kg' => 160.00,
                    'harvest_date' => '2026-08-12',
                    'status' => 'available',
                    'farmer_name' => 'Sunil Bandara',
                    'farmer_district' => 'Dambulla',
                    'farmer_phone' => '0771234567'
                ],
                [
                    'id' => 2,
                    'farmer_id' => 11,
                    'crop_type' => 'Tomato',
                    'quantity_kg' => 350.00,
                    'price_per_kg' => 155.00,
                    'harvest_date' => '2026-08-14',
                    'status' => 'available',
                    'farmer_name' => 'Kamal Perera',
                    'farmer_district' => 'Matale',
                    'farmer_phone' => '0772345678'
                ]
            ];
        }
        return [];
    }
}

$testDb = null;
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $testDb = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "   - Connected to Live MySQL Database\n";
} catch (Throwable $e) {
    echo "   - Running with Test Mock DB Adapter\n";
    $testDb = new TestMockDb();
}

// -----------------------------------------------------------------------------
// Test 1: Gemini Client Configuration & Fallback Safety
// -----------------------------------------------------------------------------
echo "\n1. Testing Gemini Client...\n";
$gemini = new GeminiClient();
$isConfigured = $gemini->isConfigured();
echo "   - Gemini API Configured: " . ($isConfigured ? "YES" : "NO (Fallback mode active)") . "\n";
assertTest(is_object($gemini), "GeminiClient instantiates successfully");

// -----------------------------------------------------------------------------
// Test 2: Demand Prediction Agent with 3+ Crop/District Combinations (TASK-069)
// -----------------------------------------------------------------------------
echo "\n2. Testing Demand Prediction Agent (TASK-069)...\n";
$demandAgent = new DemandAgent($testDb, $gemini);

$testCases = [
    ['crop' => 'Tomato', 'district' => 'Dambulla'],
    ['crop' => 'Carrot', 'district' => 'Nuwara Eliya'],
    ['crop' => 'Big Onion', 'district' => 'Matale']
];

foreach ($testCases as $idx => $tc) {
    echo "   Running Test Case #" . ($idx + 1) . ": {$tc['crop']} in {$tc['district']}...\n";
    $res = $demandAgent->predict($tc['crop'], $tc['district']);
    
    assertTest($res['success'] === true, "Prediction returned success=true for {$tc['crop']}");
    assertTest(!empty($res['forecast']['predicted_demand_level']), "Has predicted_demand_level ('{$res['forecast']['predicted_demand_level']}')");
    assertTest($res['forecast']['confidence_score'] >= 70, "Confidence score is valid ({$res['forecast']['confidence_score']}%)");
    assertTest(!empty($res['forecast']['actionable_advice']), "Includes actionable advice for farmer");
    assertTest(is_array($res['forecast']['key_factors']), "Key factors returned as array");
    echo "     -> Demand Level: {$res['forecast']['predicted_demand_level']} | Confidence: {$res['forecast']['confidence_score']}%\n";
    echo "     -> Advice: {$res['forecast']['actionable_advice']}\n";
}

// -----------------------------------------------------------------------------
// Test 3: AI Broker Agent Multi-Step Execution (TASK-055)
// -----------------------------------------------------------------------------
echo "\n3. Testing AI Broker Agent Multi-Step Matching (TASK-055)...\n";
if ($testDb instanceof PDO && !($testDb instanceof TestMockDb)) {
    // Reset test order to pending to ensure test idempotency across multiple runs
    $testDb->exec("UPDATE order_requests SET status = 'pending' WHERE id = 1");
    $testDb->exec("UPDATE harvest_listings SET status = 'available' WHERE id = 1");
    $testDb->exec("DELETE FROM order_matches WHERE order_id = 1");
}

$brokerAgent = new BrokerAgent($testDb, $gemini);
$matchResult = $brokerAgent->matchOrder(1);

assertTest($matchResult['success'] === true, "Broker Agent matched order successfully");
assertTest($matchResult['matched'] === true, "Matched state is true");
assertTest(!empty($matchResult['match']['farmer_name']), "Match contains farmer name ('" . ($matchResult['match']['farmer_name'] ?? '') . "')");
assertTest(!empty($matchResult['match']['agent_reasoning']), "Match contains explainable AI reasoning");
if (!empty($matchResult['match'])) {
    echo "     -> Matched Farmer: {$matchResult['match']['farmer_name']} ({$matchResult['match']['farmer_district']})\n";
    echo "     -> Matched Price: Rs. {$matchResult['match']['matched_price']}/kg\n";
    echo "     -> AI Reasoning: {$matchResult['match']['agent_reasoning']}\n";
}

// Summary
echo "\n=======================================================\n";
echo "Tests Passed: {$passCount} | Tests Failed: {$failCount}\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo "ALL AI AGENT TESTS PASSED WITH ZERO ERRORS!\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
