<?php
/**
 * Main layout: header + navigation + flash messages + page content + footer.
 * $content holds the HTML of the current page view.
 */
partial('partials/header', ['title' => $title ?? null]);
partial('partials/navbar');
?>
<main class="container py-4">
    <?php partial('partials/flash'); ?>
    <?= $content ?>
</main>
<?php partial('partials/footer'); ?>
