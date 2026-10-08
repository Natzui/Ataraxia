<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <a href="<?= e(url('feed/index')) ?>" class="btn btn-sm btn-link text-decoration-none mb-2">
            <i class="bi bi-arrow-left me-1"></i>Back to feed
        </a>
        <?php partial('posts/_card', ['post' => $post, 'comments' => $comments]); ?>
    </div>
</div>
