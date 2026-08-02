<?php
/**
 * AgriSync — Admin Platform Settings & System Health (TASK-090)
 * Central administrative panel for configuration status, API connectivity, and platform diagnostics.
 */

$page_title = 'System Settings & Health';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['admin']);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../agents/gemini_client.php';

$db = getDbConnection();
$gemini = new GeminiClient();

// Fetch platform diagnostic counts
try {
    $counts = [
        'users'         => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'listings'      => $db->query("SELECT COUNT(*) FROM harvest_listings")->fetchColumn(),
        'orders'        => $db->query("SELECT COUNT(*) FROM order_requests")->fetchColumn(),
        'matches'       => $db->query("SELECT COUNT(*) FROM order_matches")->fetchColumn(),
        'agent_logs'    => $db->query("SELECT COUNT(*) FROM agent_logs")->fetchColumn(),
        'notifications' => $db->query("SELECT COUNT(*) FROM notifications")->fetchColumn(),
    ];
} catch (PDOException $e) {
    $counts = ['users' => 0, 'listings' => 0, 'orders' => 0, 'matches' => 0, 'agent_logs' => 0, 'notifications' => 0];
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid dashboard-wrapper">
    <div class="row">
        <!-- Sidebar Navigation -->
        <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <!-- Header Banner -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Platform Settings & Diagnostics</h2>
                    <p class="text-muted small mb-0">System configuration parameters, AI subsystem connectivity, and runtime metrics.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-activity me-1"></i> System Operational
                    </span>
                </div>
            </div>

            <!-- Diagnostics Metrics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                        <span class="text-muted small">Registered Users</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= number_format($counts['users']) ?></h4>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                        <span class="text-muted small">Harvest Listings</span>
                        <h4 class="fw-bold text-primary mb-0 mt-1"><?= number_format($counts['listings']) ?></h4>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                        <span class="text-muted small">Order Requests</span>
                        <h4 class="fw-bold text-info mb-0 mt-1"><?= number_format($counts['orders']) ?></h4>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                        <span class="text-muted small">Active Matches</span>
                        <h4 class="fw-bold text-warning mb-0 mt-1"><?= number_format($counts['matches']) ?></h4>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                        <span class="text-muted small">Agent Audit Logs</span>
                        <h4 class="fw-bold text-success mb-0 mt-1"><?= number_format($counts['agent_logs']) ?></h4>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                        <span class="text-muted small">Notifications</span>
                        <h4 class="fw-bold text-secondary mb-0 mt-1"><?= number_format($counts['notifications']) ?></h4>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- AI Engine & API Subsystem Configuration -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-cpu text-primary me-2"></i>AI Subsystem Configuration</h5>
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">AI Engine Provider</td>
                                    <td class="fw-semibold text-end">Google Gemini API</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">Target Model</td>
                                    <td class="fw-semibold text-end">
                                        <code><?= htmlspecialchars(defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-1.5-flash', ENT_QUOTES, 'UTF-8') ?></code>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">API Key Status</td>
                                    <td class="text-end">
                                        <?php if ($gemini->isConfigured()): ?>
                                            <span class="badge bg-success-subtle text-success">Configured & Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning">Fallback Mode Enabled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">Fair Trade Floor Multiplier</td>
                                    <td class="fw-semibold text-end"><?= defined('FAIR_TRADE_MIN_MULTIPLIER') ? FAIR_TRADE_MIN_MULTIPLIER : '1.20' ?>x (Min 20% margin)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Autonomous Matching Mode</td>
                                    <td class="text-end"><span class="badge bg-primary">Real-Time Event Driven</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Environment & Server Diagnostics -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-server text-primary me-2"></i>Runtime Environment</h5>
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">Application Environment</td>
                                    <td class="text-end"><span class="badge bg-secondary"><?= defined('APP_ENV') ? strtoupper(APP_ENV) : 'PRODUCTION' ?></span></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">PHP Version</td>
                                    <td class="fw-semibold text-end"><?= PHP_VERSION ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">Database Engine</td>
                                    <td class="fw-semibold text-end">MySQL 8.x (PDO Prepared)</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted py-2">Database Charset</td>
                                    <td class="fw-semibold text-end"><?= defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Base Application URL</td>
                                    <td class="text-end text-truncate" style="max-width: 200px;">
                                        <small><code><?= htmlspecialchars(defined('APP_URL') ? APP_URL : 'http://localhost:8000', ENT_QUOTES, 'UTF-8') ?></code></small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
