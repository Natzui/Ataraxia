<?php
/**
 * Expects: $page, $pages, $route, optional $params (extra query parameters, without "page").
 */
$params = $params ?? [];
if ($pages > 1):
    $start = max(1, $page - 2);
    $end   = min($pages, $page + 2);
?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= e(url($route, $params + ['page' => max(1, $page - 1)])) ?>" aria-label="Previous">&laquo;</a>
        </li>
        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                <a class="page-link" href="<?= e(url($route, $params + ['page' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= e(url($route, $params + ['page' => min($pages, $page + 1)])) ?>" aria-label="Next">&raquo;</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
