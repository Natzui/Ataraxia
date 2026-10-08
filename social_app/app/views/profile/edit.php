<?php /** @var array $user @var array $errors */ ?>
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-4"><i class="bi bi-pencil-square me-2"></i>Edit profile</h1>

                <form method="post" action="<?= e(url('profile/update')) ?>" enctype="multipart/form-data"
                      class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="d-flex align-items-center mb-4">
                        <img id="avatarPreview" src="<?= e(avatar_url($user)) ?>" width="96" height="96"
                             class="avatar rounded-circle me-3" alt="Current profile picture">
                        <div class="flex-grow-1">
                            <label for="profile_image" class="form-label mb-1">Profile photo</label>
                            <input type="file" class="form-control form-control-sm<?= invalid_class($errors, 'profile_image') ?>"
                                   id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp"
                                   data-image-input data-preview="#avatarPreview" data-keep-visible>
                            <div class="invalid-feedback"><?= e($errors['profile_image'] ?? 'Choose a JPG, PNG, GIF or WEBP image up to 2 MB.') ?></div>
                            <?php if (!empty($user['profile_image'])): ?>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_photo" name="remove_photo">
                                    <label class="form-check-label small" for="remove_photo">Remove my photo</label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="@<?= e($user['username']) ?>" disabled>
                        <div class="form-text">Usernames cannot be changed.</div>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full name</label>
                        <input type="text" class="form-control<?= invalid_class($errors, 'full_name') ?>" id="full_name"
                               name="full_name" required minlength="2" maxlength="100" value="<?= e($user['full_name']) ?>">
                        <div class="invalid-feedback"><?= e($errors['full_name'] ?? 'Please enter your full name (2–100 characters).') ?></div>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="form-label">Bio / About</label>
                        <textarea class="form-control<?= invalid_class($errors, 'bio') ?>" id="bio" name="bio" rows="3"
                                  maxlength="255" data-counter="#bioCounter"><?= e($user['bio'] ?? '') ?></textarea>
                        <div class="invalid-feedback"><?= e($errors['bio'] ?? 'Bio can be at most 255 characters.') ?></div>
                        <div class="form-text text-end" id="bioCounter">0 / 255</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save changes</button>
                        <a href="<?= e(url('profile/show/' . (int) $user['id'])) ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
