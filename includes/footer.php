<?php
// AgriSync Global Footer Template (TASK-006)
// Safe to include at the bottom of pages

$app_url = defined('APP_URL') ? APP_URL : '';
?>
    <!-- Global Toast Container for Notifications -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;" aria-live="polite" aria-atomic="true"></div>

    <!-- Global Confirmation Modal -->
    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="globalConfirmModalLabel"><i class="bi bi-question-circle text-primary me-2"></i>Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3" id="globalConfirmModalBody">
                    Are you sure you want to proceed?
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="globalConfirmModalBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>

    <!-- Chart.js 4.4.7 CDN (Deferred / Async Compatible) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <!-- Custom AgriSync JavaScript Helper Library -->
    <script src="<?= $app_url ?>/assets/js/app.js"></script>
</body>
</html>
