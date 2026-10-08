<?php /** @var array $post @var array $errors */ ?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3"><i class="bi bi-pencil-square me-2"></i>Edit post</h1>

                <form method="post" action="<?= e(url('post/update/' . (int) $post['id'])) ?>" enctype="multipart/form-data"
                      class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control<?= invalid_class($errors, 'content') ?>" id="content" name="content"
                                  rows="5" required maxlength="1000" data-counter="#editCounter"><?= e($post['content']) ?></textarea>
                        <div class="invalid-feedback"><?= e($errors['content'] ?? 'Post content cannot be empty.') ?></div>
                        <div class="form-text text-end" id="editCounter">0 / 1000</div>
                    </div>

                    <?php if (!empty($post['image'])): ?>
                        <div class="mb-3">
                            <label class="form-label d-block">Current image</label>
                            <img src="<?= e(upload_url('posts', $post['image'])) ?>" class="img-fluid rounded post-preview mb-2" alt="Current post image">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remove_image" name="remove_image">
                                <label class="form-check-label" for="remove_image">Remove this image</label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="image" class="form-label"><?= !empty($post['image']) ? 'Replace image' : 'Add an image' ?> <span class="text-muted">(optional)</span></label>
                        <input type="file" class="form-control<?= invalid_class($errors, 'image') ?>" id="image" name="image"
                               accept="image/jpeg,image/png,image/gif,image/webp" data-image-input data-preview="#editPreview">
                        <div class="invalid-feedback"><?= e($errors['image'] ?? 'Choose a JPG, PNG, GIF or WEBP image up to 2 MB.') ?></div>
                        <img id="editPreview" class="img-fluid rounded mt-2 d-none post-preview" alt="New image preview">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save changes</button>
                        <a href="<?= e(url('post/show/' . (int) $post['id'])) ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
