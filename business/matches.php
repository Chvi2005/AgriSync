<?php
// AgriSync Business Matched Produce Page (TASK-011)
$page_title = 'Matched Produce';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../auth/auth_check.php';
checkRole(['business']);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$business_id = (int)$_SESSION['user_id'];
$db = getDbConnection();

// Fetch matched produce for logged-in business buyer
try {
    $stmt = $db->prepare("
        SELECT 
            m.id as match_id,
            m.matched_price,
            m.agent_reasoning,
            m.confidence_score,
            m.status as match_status,
            m.created_at as matched_at,
            o.id as order_id,
            o.crop_type,
            o.quantity_kg,
            u.name as farmer_name,
            u.district as farmer_district,
            u.phone as farmer_phone
        FROM order_matches m
        JOIN order_requests o ON m.order_id = o.id
        JOIN users u ON m.farmer_id = u.id
        WHERE m.business_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$business_id]);
    $matches = $stmt->fetchAll();
} catch (PDOException $e) {
    $matches = [];
}
?>

<div class="container-fluid px-4 py-4">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">Matched Farmer Harvest Produce</h2>
            <p class="text-muted small mb-0">View AI-matched local farmers and confirm order fulfillment</p>
        </div>
    </div>

    <!-- Matches Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shop-window text-primary me-2"></i>Confirmed & Proposed Harvest Matches</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Match ID</th>
                            <th>Matched Farmer</th>
                            <th>Crop Yield</th>
                            <th>Agreed Price / kg</th>
                            <th>Confidence</th>
                            <th>Status</th>
                            <th>Reasoning</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($matches)): ?>
                            <tr>
                                <td colspan="8" class="p-0">
                                    <?php 
                                    require_once __DIR__ . '/../includes/empty_state.php';
                                    echo renderEmptyState('No matched produce yet', 'Submit a pre-order request to trigger automated farmer yield matching.', 'bi-shop-window', 'Submit Pre-Order', APP_URL . '/business/requests.php'); 
                                    ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($matches as $m): ?>
                                <tr>
                                    <td class="ps-4 text-muted">#MATCH-<?= $m['match_id'] ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($m['farmer_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="text-muted extra-small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($m['farmer_district'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($m['farmer_phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>)</div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($m['crop_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <div class="text-muted extra-small"><?= number_format($m['quantity_kg'], 2) ?> kg</div>
                                    </td>
                                    <td class="fw-bold text-success">Rs. <?= number_format($m['matched_price'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                            <i class="bi bi-cpu me-1"></i><?= (int)$m['confidence_score'] ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= getStatusBadgeClass($m['match_status']) ?>"><?= htmlspecialchars($m['match_status'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0 extra-small" onclick="viewReasoning(`<?= htmlspecialchars(addslashes($m['agent_reasoning']), ENT_QUOTES, 'UTF-8') ?>`)">
                                            <i class="bi bi-info-circle me-1"></i>View Logic
                                        </button>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <?php if ($m['match_status'] === 'accepted'): ?>
                                            <button type="button" class="btn btn-sm btn-success" onclick="completeOrder(<?= $m['match_id'] ?>)">
                                                <i class="bi bi-check-circle me-1"></i> Confirm & Fulfill
                                            </button>
                                        <?php elseif ($m['match_status'] === 'completed'): ?>
                                            <span class="badge bg-success-subtle text-success">Order Completed</span>
                                        <?php else: ?>
                                            <span class="text-muted extra-small">Awaiting Farmer Acceptance</span>
                                        <?php endif; ?>
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

<!-- View Agent Reasoning Modal -->
<div class="modal fade" id="reasoningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-cpu text-primary me-2"></i>AI Match Reasoning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p id="reasoningContent" class="text-secondary mb-0 extra-small lead" style="white-space: pre-line;"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewReasoning(text) {
    document.getElementById('reasoningContent').textContent = text;
    new bootstrap.Modal(document.getElementById('reasoningModal')).show();
}

async function completeOrder(matchId) {
    if (!confirm('Confirm delivery & mark order as fulfilled?')) return;

    try {
        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('csrf_token', '<?= generateCSRFToken() ?>');

        const res = await fetch('<?= APP_URL ?>/api/business.php?action=complete_order', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>' }
        });

        const data = await res.json();
        if (data.success) {
            if (window.AgriSync && window.AgriSync.showToast) {
                window.AgriSync.showToast('Order completed & marked fulfilled!', 'success');
            }
            setTimeout(() => location.reload(), 600);
        } else {
            alert(data.error || 'Failed to complete order.');
        }
    } catch (err) {
        alert('Network error completing order.');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
