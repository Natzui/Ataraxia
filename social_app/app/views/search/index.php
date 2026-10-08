<?php /** @var string $q @var string $type @var array $users @var array $posts @var array $comments @var ?string $error @var bool $searched */ ?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h1 class="h4 mb-3"><i class="bi bi-search me-2"></i>Search</h1>

                <form method="get" action="<?= e(base_url() . '/index.php') ?>" role="search">
                    <input type="hidden" name="url" value="search/index">
                    <div class="input-group mb-2">
                        <input type="search" class="form-control" name="q" value="<?= e($q) ?>" maxlength="50" required minlength="2"
                               placeholder="<?= $type === 'posts' ? 'Search posts by keyword…' : 'Search people by name or username…' ?>"
                               aria-label="Search term" autofocus>
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Search type">
                        <input type="radio" class="btn-check" name="type" id="typeUsers" value="users" <?= $type === 'users' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="typeUsers"><i class="bi bi-people me-1"></i>People</label>
                        <input type="radio" class="btn-check" name="type" id="typePosts" value="posts" <?= $type === 'posts' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="typePosts"><i class="bi bi-file-text me-1"></i>Posts</label>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-warning"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($searched && $type === 'users'): ?>
            <p class="text-muted small"><?= count($users) ?> <?= count($users) === 1 ? 'person' : 'people' ?> found for “<?= e($q) ?>”</p>
            <?php if (!$users): ?>
                <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-4">No users matched your search.</div></div>
            <?php endif; ?>
            <div class="list-group shadow-sm">
                <?php foreach ($users as $u): ?>
                    <a href="<?= e(url('profile/show/' . (int) $u['id'])) ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <?= avatar_img($u, 48, 'me-3 flex-shrink-0') ?>
                        <div class="min-w-0">
                            <div class="fw-semibold"><?= e($u['full_name']) ?></div>
                            <div class="text-muted small">@<?= e($u['username']) ?></div>
                            <?php if (!empty($u['bio'])): ?>
                                <div class="small text-truncate"><?= e($u['bio']) ?></div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($searched && $type === 'posts'): ?>
            <p class="text-muted small"><?= count($posts) ?> <?= count($posts) === 1 ? 'post' : 'posts' ?> found for “<?= e($q) ?>”</p>
            <?php if (!$posts): ?>
                <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-4">No posts matched your search.</div></div>
            <?php endif; ?>
            <?php foreach ($posts as $post): ?>
                <?php partial('posts/_card', ['post' => $post, 'comments' => $comments[(int) $post['id']] ?? []]); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
