<?php /** @var array $posts @var array $comments @var int $page @var int $pages */ ?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <?php partial('posts/_create'); ?>

        <?php if (!$posts): ?>
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-chat-square-text display-4 text-muted"></i>
                    <h2 class="h5 mt-3">Nothing here yet</h2>
                    <p class="text-muted mb-0">Be the first to share something!</p>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
            <?php partial('posts/_card', ['post' => $post, 'comments' => $comments[(int) $post['id']] ?? []]); ?>
        <?php endforeach; ?>

        <?php partial('partials/pagination', ['page' => $page, 'pages' => $pages, 'route' => 'feed/index']); ?>
    </div>
</div>
