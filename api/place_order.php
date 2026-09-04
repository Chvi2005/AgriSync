<?php
/**
 * AgriSync — Place Order API Endpoint (TASK-048 / Issue #35)
 * Receives commercial order requests and instantly executes autonomous AI Broker matchmaking.
 */

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../agents/broker_agent.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? '';

if ($user_role !== 'business' && $user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Only registered commercial buyers can place orders.']);
    exit;
}

// Ingest JSON or POST input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$crop_type = sanitize($input['crop_type'] ?? '');
$quantity_kg = (float) ($input['quantity_kg'] ?? 0);
$max_price = (float) ($input['max_price'] ?? 0);
$delivery_date = sanitize($input['delivery_date'] ?? '');
$urgency = sanitize($input['urgency'] ?? 'medium');
$notes = sanitize($input['notes'] ?? '');

// Validation
if (empty($crop_type) || !in_array($crop_type, AGRISYNC_CROPS, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please select a valid crop from the catalog.']);
    exit;
}

if ($quantity_kg <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Quantity must be greater than 0 kg.']);
    exit;
}

if ($max_price <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Maximum price cap must be a positive number.']);
    exit;
}

if (empty($delivery_date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Target delivery date is required.']);
    exit;
}

if (!in_array($urgency, ['low', 'medium', 'high'], true)) {
    $urgency = 'medium';
}

try {
    $db = getDbConnection();

    // 1. Insert Order Request
    $stmt = $db->prepare("
        INSERT INTO order_requests 
            (business_id, crop_type, quantity_kg, max_price, delivery_date, urgency, status, notes, created_at, updated_at)
        VALUES 
            (:business_id, :crop_type, :quantity_kg, :max_price, :delivery_date, :urgency, 'pending', :notes, NOW(), NOW())
    ");
    $stmt->execute([
        ':business_id' => $user_id,
        ':crop_type' => $crop_type,
        ':quantity_kg' => $quantity_kg,
        ':max_price' => $max_price,
        ':delivery_date' => $delivery_date,
        ':urgency' => $urgency,
        ':notes' => $notes
    ]);

    $order_id = (int) $db->lastInsertId();

    // 2. Trigger AI Broker Agent Matchmaking Pipeline
    $broker = new BrokerAgent($db);
    $matchResult = $broker->matchOrder($order_id);

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'matched' => (bool) ($matchResult['matched'] ?? false),
        'message' => $matchResult['message'] ?? 'Order placed and processed by AI Broker.',
        'match' => $matchResult['match'] ?? null
    ], JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    error_log("Place Order API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error processing order: ' . $e->getMessage()
    ]);
}
