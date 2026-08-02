<?php
/**
 * AgriSync — Admin AI Agent Monitor & Audit Logs (TASK-086, TASK-093)
 * Real-time monitoring and Explainable AI transparency hub for all platform AI decisions.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

// Strict Admin Access Control
requireRole('admin');

$page_title = 'AI Agent Monitor & Audit Logs';
$selected_agent = sanitize($_GET['agent'] ?? 'all');
$search_query = sanitize($_GET['q'] ?? '');

$db = null;
$logs = [];
$stats = [
    'total_calls' => 0,
    'broker_calls' => 0,
    'demand_calls' => 0,
    'recent_24h_calls' => 0,
];

try {
    $db = getDbConnection();

    // Fetch Performance Metrics
    $statStmt = $db->query("
        SELECT 
            COUNT(*) as total_calls,
            SUM(CASE WHEN agent_type = 'broker' THEN 1 ELSE 0 END) as broker_calls,
            SUM(CASE WHEN agent_type = 'demand_predictor' THEN 1 ELSE 0 END) as demand_calls,
            SUM(CASE WHEN created_at >= NOW() - INTERVAL 24 HOUR THEN 1 ELSE 0 END) as recent_24h_calls
        FROM agent_logs
    ");
    $statsRow = $statStmt->fetch(PDO::FETCH_ASSOC);
    if ($statsRow) {
        $stats = [
            'total_calls' => (int) ($statsRow['total_calls'] ?? 0),
            'broker_calls' => (int) ($statsRow['broker_calls'] ?? 0),
            'demand_calls' => (int) ($statsRow['demand_calls'] ?? 0),
            'recent_24h_calls' => (int) ($statsRow['recent_24h_calls'] ?? 0),
        ];
    }

    // Build filter query for logs
    $sql = "SELECT id, agent_type, order_id, action_step, log_data, created_at FROM agent_logs WHERE 1=1";
    $params = [];

    if ($selected_agent !== 'all' && in_array($selected_agent, ['broker', 'demand_predictor'])) {
        $sql .= " AND agent_type = :agent_type";
        $params[':agent_type'] = $selected_agent;
    }

    if (!empty($search_query)) {
        $sql .= " AND (action_step LIKE :search OR log_data LIKE :search OR CAST(order_id AS CHAR) LIKE :search)";
        $params[':search'] = '%' . $search_query . '%';
    }

    $sql .= " ORDER BY id DESC LIMIT 100";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log("Admin Agent Logs Error: " . $e->getMessage());
    $error_message = "Unable to fetch live agent logs. Please verify database connectivity.";
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Role-based Sidebar Navigation -->
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow-1 bg-light p-4 overflow-auto">
        <div class="container-fluid max-w-7xl">
            
            <!-- Page Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-cpu-fill text-success me-2"></i> AI Agent Monitor & Audit Logs
                    </h1>
                    <p class="text-muted small mb-0">
                        Explainable AI transparency hub — inspect reasoning steps, confidence scores, and neural telemetry.
                    </p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <button type="button" class="btn btn-outline-success d-flex align-items-center rounded-3" onclick="window.location.reload();">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Logs
                    </button>
                </div>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger rounded-3 d-flex align-items-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endif; ?>

            <!-- Performance Metrics Stat Cards (TASK-093) -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Total Invocations</span>
                            <div class="p-2 rounded-3 bg-success-subtle text-success">
                                <i class="bi bi-activity fs-5"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-0 text-dark"><?= number_format($stats['total_calls']) ?></h2>
                        <small class="text-muted">Recorded across all agents</small>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Broker Agent Matches</span>
                            <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                                <i class="bi bi-robot fs-5"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-0 text-dark"><?= number_format($stats['broker_calls']) ?></h2>
                        <small class="text-muted">Supply-demand matchmaking steps</small>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Demand Predictions</span>
                            <div class="p-2 rounded-3 bg-info-subtle text-info">
                                <i class="bi bi-graph-up-arrow fs-5"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-0 text-dark"><?= number_format($stats['demand_calls']) ?></h2>
                        <small class="text-muted">Market & seasonal crop forecasts</small>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Last 24 Hours</span>
                            <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                                <i class="bi bi-clock-history fs-5"></i>
                            </div>
                        </div>
                        <h2 class="h3 fw-bold mb-0 text-dark"><?= number_format($stats['recent_24h_calls']) ?></h2>
                        <small class="text-muted">Active agent telemetry</small>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Controls -->
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                <form method="GET" action="agent_logs.php" class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-muted mb-1 fw-semibold">Agent Engine Filter</label>
                        <select name="agent" class="form-select rounded-3" onchange="this.form.submit()">
                            <option value="all" <?= $selected_agent === 'all' ? 'selected' : '' ?>>All Agents (Combined)</option>
                            <option value="broker" <?= $selected_agent === 'broker' ? 'selected' : '' ?>>AI Broker Matchmaking Agent</option>
                            <option value="demand_predictor" <?= $selected_agent === 'demand_predictor' ? 'selected' : '' ?>>Demand Prediction Advisory Agent</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-1 fw-semibold">Search Logs & Reasoning</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="q" class="form-control border-start-0 rounded-end-3" placeholder="Search by step name, order ID, crop, or reasoning keyword..." value="<?= htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 rounded-3" style="min-height: 40px;">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Logs Audit Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-list-columns-reverse text-success me-2"></i> Audit Trail Activity
                    </h5>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                        Showing <?= count($logs) ?> entries
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 80px;">Log ID</th>
                                <th scope="col" style="width: 170px;">Agent Engine</th>
                                <th scope="col" style="width: 100px;">Order Ref</th>
                                <th scope="col">Decision Step / Action</th>
                                <th scope="col" style="width: 180px;">Timestamp</th>
                                <th scope="col" class="text-end" style="width: 130px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                        No agent activity logs recorded matching your filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <?php 
                                        $is_broker = ($log['agent_type'] === 'broker');
                                        $badge_class = $is_broker ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-info-subtle text-info border border-info-subtle';
                                        $agent_label = $is_broker ? 'Broker Agent' : 'Demand Predictor';
                                        $log_json = !empty($log['log_data']) ? json_encode(json_decode($log['log_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '{}';
                                    ?>
                                    <tr>
                                        <td class="text-muted fw-semibold">#<?= (int)$log['id'] ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?= $badge_class ?> px-2 py-1">
                                                <i class="bi <?= $is_broker ? 'bi-link-45deg' : 'bi-stars' ?> me-1"></i> <?= $agent_label ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($log['order_id'])): ?>
                                                <span class="badge bg-light text-dark border">Order #<?= (int)$log['order_id'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold text-dark">
                                            <?= htmlspecialchars($log['action_step'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="text-muted small">
                                            <i class="bi bi-clock me-1"></i> <?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1 d-inline-flex align-items-center"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#inspectModal"
                                                    data-log-id="<?= (int)$log['id'] ?>"
                                                    data-agent-type="<?= htmlspecialchars($agent_label, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-action-step="<?= htmlspecialchars($log['action_step'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-timestamp="<?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?>"
                                                    data-payload="<?= htmlspecialchars($log_json, ENT_QUOTES, 'UTF-8') ?>">
                                                <i class="bi bi-eye-fill me-1"></i> Inspect
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Inspect Decision Payload Modal -->
<div class="modal fade" id="inspectModal" tabindex="-1" aria-labelledby="inspectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <div class="d-flex align-items-center">
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary me-3">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="inspectModalLabel">AI Agent Decision Payload</h5>
                        <small class="text-muted" id="inspectModalSubtitle">Detailed neural telemetry and explainability output</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-secondary-subtle text-secondary" id="modalBadgeAgent"></span>
                    <span class="badge bg-light text-muted border" id="modalBadgeTime"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted fw-semibold">Action Step</label>
                    <div class="p-2 bg-light rounded-3 fw-semibold text-dark" id="modalActionStep"></div>
                </div>
                <div>
                    <label class="form-label small text-muted fw-semibold">Recorded JSON Payload & Reasoning</label>
                    <pre class="bg-dark text-light p-3 rounded-3 overflow-auto small mb-0" style="max-height: 380px; font-family: monospace;" id="modalJsonPayload"></pre>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-2">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inspectModal = document.getElementById('inspectModal');
    if (inspectModal) {
        inspectModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const logId = button.getAttribute('data-log-id');
            const agentType = button.getAttribute('data-agent-type');
            const actionStep = button.getAttribute('data-action-step');
            const timestamp = button.getAttribute('data-timestamp');
            const payload = button.getAttribute('data-payload');

            document.getElementById('inspectModalLabel').textContent = `AI Decision Telemetry — Log #${logId}`;
            document.getElementById('modalBadgeAgent').textContent = agentType;
            document.getElementById('modalBadgeTime').textContent = timestamp;
            document.getElementById('modalActionStep').textContent = actionStep;
            document.getElementById('modalJsonPayload').textContent = payload;
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
