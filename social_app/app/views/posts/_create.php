<?php $me = Auth::user(); ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="post" action="<?= e(url('post/create')) ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <div class="d-flex">
                <?= avatar_img($me, 44, 'me-3 flex-shrink-0') ?>
                <div class="flex-grow-1">
                    <textarea class="form-control border-0 bg-light" name="content" rows="3" required maxlength="1000"
                              placeholder="What's on your mind, <?= e(strtok($me['full_name'], ' ')) ?>?"
                              aria-label="Post content" data-counter="#postCounter"></textarea>
                    <div class="invalid-feedback">Write something before posting.</div>

                    <img id="postPreview" class="img-fluid rounded mt-2 d-none post-preview" alt="Selected image preview">

                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-2 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <label class="btn btn-sm btn-outline-secondary mb-0" for="postImage">
                                <i class="bi bi-image me-1"></i>Photo
                            </label>
                            <input type="file" class="d-none" id="postImage" name="image"
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   data-image-input data-preview="#postPreview">
                            <span class="small text-muted" id="postCounter">0 / 1000</span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="bi bi-send me-1"></i>Post</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
