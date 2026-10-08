<?php /** @var array $errors @var array $old */ ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill brand-icon"></i>
                    <h1 class="h3 mt-2">Create your account</h1>
                    <p class="text-muted mb-0">Join the community in a few seconds.</p>
                </div>

                <?php if (!empty($errors['form'])): ?>
                    <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('auth/register')) ?>" enctype="multipart/form-data"
                      class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full name</label>
                        <input type="text" class="form-control<?= invalid_class($errors, 'full_name') ?>" id="full_name"
                               name="full_name" required minlength="2" maxlength="100" autocomplete="name"
                               value="<?= old_value($old, 'full_name') ?>">
                        <div class="invalid-feedback"><?= e($errors['full_name'] ?? 'Please enter your full name (2–100 characters).') ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">@</span>
                            <input type="text" class="form-control<?= invalid_class($errors, 'username') ?>" id="username"
                                   name="username" required pattern="[A-Za-z0-9_]{3,30}" maxlength="30" autocomplete="username"
                                   value="<?= old_value($old, 'username') ?>">
                            <div class="invalid-feedback"><?= e($errors['username'] ?? '3–30 characters: letters, numbers and underscores only.') ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control<?= invalid_class($errors, 'password') ?>" id="password"
                                   name="password" required minlength="8" maxlength="72" autocomplete="new-password"
                                   pattern="(?=.*[A-Za-z])(?=.*[0-9]).{8,72}">
                            <div class="invalid-feedback"><?= e($errors['password'] ?? 'At least 8 characters with a letter and a number.') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Confirm password</label>
                            <input type="password" class="form-control<?= invalid_class($errors, 'password_confirm') ?>"
                                   id="password_confirm" name="password_confirm" required autocomplete="new-password"
                                   data-match="#password">
                            <div class="invalid-feedback"><?= e($errors['password_confirm'] ?? 'Passwords do not match.') ?></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="profile_image" class="form-label">Profile picture <span class="text-muted">(optional)</span></label>
                        <input type="file" class="form-control<?= invalid_class($errors, 'profile_image') ?>" id="profile_image"
                               name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp"
                               data-image-input data-preview="#avatarPreview">
                        <div class="invalid-feedback"><?= e($errors['profile_image'] ?? 'Choose a JPG, PNG, GIF or WEBP image up to 2 MB.') ?></div>
                        <div class="form-text">JPG, PNG, GIF or WEBP, up to 2 MB.</div>
                        <img id="avatarPreview" class="avatar rounded-circle mt-2 d-none" width="80" height="80" alt="Profile picture preview">
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-check me-1"></i>Sign up</button>
                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    Already have an account? <a href="<?= e(url('auth/login')) ?>">Log in</a>
                </p>
            </div>
        </div>
    </div>
</div>
