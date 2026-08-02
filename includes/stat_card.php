<?php
// AgriSync Reusable Dashboard Summary Stat Card Component

/**
 * Render reusable Dashboard Summary Stat Card HTML string (.stat-card)
 * 
 * @param string $label Stat metric label (e.g. "Total Orders")
 * @param string $value Stat metric value (e.g. "24" or "Rs. 15,000")
 * @param string $icon Bootstrap Icon class (e.g. "bi-box-seam")
 * @param string $color Accent variant: 'primary', 'success', 'info', 'warning', 'danger'
 * @param string|null $trend Trend percentage or direction (e.g. "+12%" or "up", "-5%" or "down")
 * @param string|null $elementId HTML ID for dynamic JS value updating
 * @return string HTML string
 */
function renderStatCard(
    string $label,
    string $value,
    string $icon,
    string $color = 'primary',
    ?string $trend = null,
    ?string $elementId = null
): string {
    $idAttr = $elementId ? 'id="' . htmlspecialchars($elementId, ENT_QUOTES, 'UTF-8') . '"' : '';
    $validColor = in_array($color, ['primary', 'success', 'info', 'warning', 'danger'], true) ? $color : 'primary';

    $trendHtml = '';
    if (!empty($trend)) {
        $isDown = str_contains(strtolower($trend), 'down') || str_contains($trend, '-');
        $trendClass = $isDown ? 'trend-down' : 'trend-up';
        $trendIcon = $isDown ? 'bi-arrow-down-right' : 'bi-arrow-up-right';
        $trendHtml = '<div class="stat-card-trend ' . $trendClass . '"><i class="bi ' . $trendIcon . '"></i> ' . htmlspecialchars($trend, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    return '
        <div class="stat-card stat-card-' . $validColor . '">
            <div class="stat-card-inner">
                <div>
                    <span class="stat-card-label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>
                    <h3 class="stat-card-value" ' . $idAttr . '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</h3>
                    ' . $trendHtml . '
                </div>
                <div class="stat-card-icon-wrapper">
                    <i class="bi ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i>
                </div>
            </div>
        </div>
    ';
}
