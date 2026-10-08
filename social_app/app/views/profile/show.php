<?php
/** @var array $profileUser @var array $posts @var array $comments @var int $postCount @var int $page @var int $pages */
$isMe = (int) $profileUser['id'] === (int) Auth::id();
?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="card shadow-sm border-0 mb-4 profile-card">
            <div class="profile-cover"></div>
            <div class="card-body pt-0">
                <div class="d-flex flex-wrap align-items-end justify-content-between profile-header">
                    <?= avatar_img($profileUser, 112, 'profile-avatar border border-4 border-white') ?>
                    <?php if ($isMe): ?>
                        <a href="<?= e(url('profile/edit')) ?>" class="btn btn-outline-primary btn-sm mt-3">
                            <i class="bi bi-pencil-square me-1"></i>Edit profile
                        </a>
                    <?php endif; ?>
                </div>

                <h1 class="h4 mt-3 mb-0"><?= e($profileUser['full_name']) ?></h1>
                <div class="text-muted">@<?= e($profileUser['username']) ?></div>

                <p class="mt-3 mb-2">
                    <?php if (!empty($profileUser['bio'])): ?>
                        <?= nl2br(e($profileUser['bio'])) ?>
                    <?php else: ?>
                        <span class="text-muted fst-italic"><?= $isMe ? 'You have not written a bio yet.' : 'No bio yet.' ?></span>
                    <?php endif; ?>
                </p>

                <div class="text-muted small">
                    <i class="bi bi-calendar3 me-1"></i>Joined <?= e(date('F Y', strtotime($profileUser['created_at']))) ?>
                    &middot; <i class="bi bi-file-text me-1"></i><?= (int) $postCount ?> <?= (int) $postCount === 1 ? 'post' : 'posts' ?>
                </div>
            </div>
        </div>

        <h2 class="h5 mb-3">Posts</h2>

        <?php if (!$posts): ?>
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="card-body text-muted">
                    <?= $isMe ? 'You have not posted anything yet.' : e($profileUser['full_name']) . ' has not posted anything yet.' ?>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
            <?php partial('posts/_card', ['post' => $post, 'comments' => $comments[(int) $post['id']] ?? []]); ?>
        <?php endforeach; ?>

        <?php partial('partials/pagination', ['page' => $page, 'pages' => $pages, 'route' => 'profile/show/' . (int) $profileUser['id']]); ?>
    </div>
</div>
