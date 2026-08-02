<?php
// AgriSync Reusable Empty State Helper Component

/**
 * Render reusable Empty State HTML string
 * 
 * @param string $title Main heading title
 * @param string $description Subtitle description
 * @param string $icon Bootstrap Icon class (e.g. 'bi-box-seam')
 * @param string|null $btnText CTA button label
 * @param string|null $btnLink CTA link URL
 * @param string|null $btnModalId Target modal ID
 * @return string HTML string
 */
function renderEmptyState(
    string $title,
    string $description = '',
    string $icon = 'bi-inbox',
    ?string $btnText = null,
    ?string $btnLink = null,
    ?string $btnModalId = null
): string {
    $btnHtml = '';
    if (!empty($btnText)) {
        if (!empty($btnModalId)) {
            $btnHtml = '<div class="empty-state-action"><button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#' . htmlspecialchars($btnModalId, ENT_QUOTES, 'UTF-8') . '"><i class="bi bi-plus-lg me-1"></i>' . htmlspecialchars($btnText, ENT_QUOTES, 'UTF-8') . '</button></div>';
        } elseif (!empty($btnLink)) {
            $btnHtml = '<div class="empty-state-action"><a href="' . htmlspecialchars($btnLink, ENT_QUOTES, 'UTF-8') . '" class="btn btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i>' . htmlspecialchars($btnText, ENT_QUOTES, 'UTF-8') . '</a></div>';
        }
    }

    $descHtml = !empty($description) ? '<p class="empty-state-description mb-3">' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</p>' : '';

    return '
        <div class="empty-state py-5 text-center">
            <div class="empty-state-icon-wrapper mx-auto">
                <i class="bi ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i>
            </div>
            <h5 class="empty-state-title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h5>
            ' . $descHtml . '
            ' . $btnHtml . '
        </div>
    ';
}
