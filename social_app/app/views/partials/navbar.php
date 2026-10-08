<?php
$me    = Auth::user();
$route = current_route();
?>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e(url($me ? 'feed/index' : 'auth/login')) ?>">
            <i class="bi bi-chat-heart-fill me-1"></i><?= e(config('app_name')) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <?php if ($me): ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link<?= strpos($route, 'feed') === 0 || $route === '' ? ' active' : '' ?>"
                           href="<?= e(url('feed/index')) ?>"><i class="bi bi-house-door-fill me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $route === 'profile/show' || $route === 'profile/show/' . (int) $me['id'] ? ' active' : '' ?>"
                           href="<?= e(url('profile/show/' . (int) $me['id'])) ?>"><i class="bi bi-person-circle me-1"></i>My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= strpos($route, 'search') === 0 ? ' active' : '' ?>"
                           href="<?= e(url('search/index')) ?>"><i class="bi bi-search me-1"></i>Search</a>
                    </li>
                </ul>

                <form class="d-flex me-lg-3 mb-2 mb-lg-0" role="search" method="get" action="<?= e(base_url() . '/index.php') ?>">
                    <input type="hidden" name="url" value="search/index">
                    <input type="hidden" name="type" value="users">
                    <input class="form-control form-control-sm me-2" type="search" name="q" maxlength="50"
                           placeholder="Find people…" aria-label="Search people">
                    <button class="btn btn-sm btn-light" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                </form>

                <div class="dropdown">
                    <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= avatar_img($me, 32, 'me-2') ?>
                        <span class="d-none d-sm-inline"><?= e($me['full_name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= e(url('profile/show/' . (int) $me['id'])) ?>"><i class="bi bi-person me-2"></i>View profile</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('profile/edit')) ?>"><i class="bi bi-pencil-square me-2"></i>Edit profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= e(url('auth/logout')) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('auth/login')) ?>">Log in</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('auth/register')) ?>">Sign up</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
