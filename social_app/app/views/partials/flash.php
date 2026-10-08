<?php
$allowed = ['success', 'danger', 'warning', 'info'];
foreach (pull_flashes() as $flash):
    $type = in_array($flash['type'], $allowed, true) ? $flash['type'] : 'info';
?>
    <div class="alert alert-<?= e($type) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endforeach; ?>
