<?php
/**
 * LikeController - like / unlike a post (one like per user per post).
 */
class LikeController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /** POST like/toggle  (post_id in the form body) */
    public function toggle(): void
    {
        $this->requirePost();
        $postId = (int) $this->input('post_id');

        if ((new PostModel())->findRaw($postId) === null) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Post not found.'], 404);
            }
            $this->abort(404, 'That post does not exist.');
        }

        $result = (new LikeModel())->toggle($postId, (int) Auth::id());

        if ($this->isAjax()) {
            $this->json($result);
        }
        $this->redirectBack('feed/index', 'post-' . $postId);
    }
}
