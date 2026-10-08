<?php
/**
 * Comment list + "add comment" form. Expects $post and $comments (list).
 */
$postId = (int) $post['id'];
$meId   = (int) Auth::id();
$me     = Auth::user();
?>
<div class="comments-section border-top px-3 py-3">
    <?php foreach ($comments as $comment): ?>
        <?php
            $cid     = (int) $comment['id'];
            $isMine  = (int) $comment['user_id'] === $meId;
        ?>
        <div class="d-flex mb-3" id="comment-<?= $cid ?>">
            <a href="<?= e(url('profile/show/' . (int) $comment['user_id'])) ?>" class="flex-shrink-0">
                <?= avatar_img($comment, 32, 'me-2') ?>
            </a>
            <div class="flex-grow-1">
                <div class="comment-bubble">
                    <a class="fw-semibold small text-decoration-none text-dark"
                       href="<?= e(url('profile/show/' . (int) $comment['user_id'])) ?>"><?= e($comment['full_name']) ?></a>
                    <span class="text-muted small ms-1" title="<?= e(full_date($comment['created_at'])) ?>"><?= e(time_ago($comment['created_at'])) ?></span>
                    <div class="comment-text"><?= nl2br(e($comment['content'])) ?></div>

                    <?php if ($isMine): ?>
                        <form method="post" action="<?= e(url('comment/update/' . $cid)) ?>"
                              class="comment-edit-form d-none mt-1 needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <textarea class="form-control form-control-sm mb-2" name="content" rows="2" required
                                      maxlength="500" aria-label="Edit comment"><?= e($comment['content']) ?></textarea>
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm js-cancel-edit">Cancel</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if ($isMine): ?>
                    <div class="comment-actions small">
                        <button type="button" class="btn btn-link btn-sm p-0 me-2 text-decoration-none js-edit-comment">Edit</button>
                        <form method="post" action="<?= e(url('comment/delete/' . $cid)) ?>" class="d-inline"
                              data-confirm="Delete this comment?">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger text-decoration-none">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!$comments): ?>
        <p class="text-muted small mb-3">No comments yet. Be the first to reply!</p>
    <?php endif; ?>

    <form method="post" action="<?= e(url('comment/store')) ?>" class="d-flex align-items-start needs-validation" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="post_id" value="<?= $postId ?>">
        <?= avatar_img($me, 32, 'me-2 flex-shrink-0') ?>
        <div class="input-group input-group-sm has-validation">
            <input type="text" class="form-control" name="content" required maxlength="500"
                   placeholder="Write a comment…" aria-label="Write a comment">
            <button class="btn btn-primary" type="submit" aria-label="Send comment"><i class="bi bi-send"></i></button>
            <div class="invalid-feedback">Please write a comment.</div>
        </div>
    </form>
</div>
