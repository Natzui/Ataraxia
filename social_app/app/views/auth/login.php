<?php /** @var array $errors @var array $old */ ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-chat-heart-fill brand-icon"></i>
                    <h1 class="h3 mt-2">Welcome back</h1>
                    <p class="text-muted mb-0">Log in to see what's new.</p>
                </div>

                <?php if (!empty($errors['form'])): ?>
                    <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('auth/login')) ?>" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required
                                   maxlength="30" autocomplete="username" autofocus
                                   value="<?= old_value($old, 'username') ?>">
                            <div class="invalid-feedback">Please enter your username.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required
                                   autocomplete="current-password">
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Log in</button>
                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    New here? <a href="<?= e(url('auth/register')) ?>">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</div>
