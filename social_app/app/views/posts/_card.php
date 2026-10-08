<?php
/**
 * One post card. Expects $post (row from PostModel) and $comments (list of comments for this post).
 */
$postId  = (int) $post['id'];
$isOwner = (int) $post['user_id'] === (int) Auth::id();
$liked   = !empty($post['liked_by_me']);
$profile = url('profile/show/' . (int) $post['user_id']);
?>
<article class="card post-card shadow-sm border-0 mb-3" id="post-<?= $postId ?>">
    <div class="card-body pb-2">
        <div class="d-flex align-items-start mb-2">
            <a href="<?= e($profile) ?>" class="flex-shrink-0"><?= avatar_img($post, 44, 'me-3') ?></a>
            <div class="flex-grow-1">
                <a class="fw-semibold text-decoration-none text-dark" href="<?= e($profile) ?>"><?= e($post['full_name']) ?></a>
                <div class="text-muted small">
                    @<?= e($post['username']) ?> &middot;
                    <a class="text-muted text-decoration-none" href="<?= e(url('post/show/' . $postId)) ?>"
                       title="<?= e(full_date($post['created_at'])) ?>"><?= e(time_ago($post['created_at'])) ?></a>
                </div>
            </div>

            <?php if ($isOwner): ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Post options">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= e(url('post/edit/' . $postId)) ?>"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                        <li>
                            <form method="post" action="<?= e(url('post/delete/' . $postId)) ?>"
                                  data-confirm="Delete this post? Its comments and likes will be removed too.">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <p class="post-content mb-2"><?= nl2br(e($post['content'])) ?></p>

        <?php if (!empty($post['image'])): ?>
            <img class="post-image img-fluid rounded mb-2" src="<?= e(upload_url('posts', $post['image'])) ?>"
                 alt="Image attached to <?= e($post['full_name']) ?>'s post" loading="lazy">
        <?php endif; ?>

        <div class="d-flex align-items-center gap-3 border-top pt-2 mt-2">
            <form method="post" action="<?= e(url('like/toggle')) ?>" class="like-form">
                <?= csrf_field() ?>
                <input type="hidden" name="post_id" value="<?= $postId ?>">
                <button type="submit" class="btn btn-sm btn-link text-decoration-none like-btn <?= $liked ? 'text-danger' : 'text-muted' ?>"
                        aria-pressed="<?= $liked ? 'true' : 'false' ?>">
                    <i class="bi <?= $liked ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                    <span class="like-count"><?= (int) $post['like_count'] ?></span>
                    <span class="like-label"><?= (int) $post['like_count'] === 1 ? 'Like' : 'Likes' ?></span>
                </button>
            </form>
            <a class="btn btn-sm btn-link text-decoration-none text-muted" data-bs-toggle="collapse"
               href="#comments-<?= $postId ?>" role="button" aria-expanded="<?= $comments ? 'true' : 'false' ?>"
               aria-controls="comments-<?= $postId ?>">
                <i class="bi bi-chat"></i> <?= (int) $post['comment_count'] ?> <?= (int) $post['comment_count'] === 1 ? 'Comment' : 'Comments' ?>
            </a>
        </div>
    </div>

    <div class="collapse<?= $comments ? ' show' : '' ?>" id="comments-<?= $postId ?>">
        <?php partial('comments/_section', ['post' => $post, 'comments' => $comments]); ?>
    </div>
</article>
