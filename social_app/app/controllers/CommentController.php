<?php
/**
 * CommentController - CRUD for comments (CRUD Module #2).
 * (Comments are READ together with their posts by the feed/profile/post pages.)
 */
class CommentController extends Controller
{
    private const MAX_LENGTH = 500;

    public function __construct()
    {
        $this->requireAuth();
    }

    /** CREATE - POST comment/store */
    public function store(): void
    {
        $this->requirePost();
        $postId  = (int) $this->input('post_id');
        $content = Input::text($this->input('content'));

        $post = (new PostModel())->findRaw($postId);
        if ($post === null) {
            $this->abort(404, 'That post does not exist.');
        }

        $error = $this->validate($content);
        if ($error !== null) {
            flash('danger', $error);
            $this->redirectBack('feed/index', 'post-' . $postId);
        }

        (new CommentModel())->create($postId, (int) Auth::id(), $content);
        $this->redirectBack('feed/index', 'post-' . $postId);
    }

    /** UPDATE - POST comment/update/{id} */
    public function update($id = 0): void
    {
        $this->requirePost();
        $comment = $this->ownedComment((int) $id);
        $content = Input::text($this->input('content'));

        $error = $this->validate($content);
        if ($error !== null) {
            flash('danger', $error);
        } else {
            (new CommentModel())->update((int) $comment['id'], (int) Auth::id(), $content);
            flash('success', 'Comment updated.');
        }
        $this->redirectBack('feed/index', 'post-' . (int) $comment['post_id']);
    }

    /** DELETE - POST comment/delete/{id} */
    public function delete($id = 0): void
    {
        $this->requirePost();
        $comment = $this->ownedComment((int) $id);

        if ((new CommentModel())->delete((int) $comment['id'], (int) Auth::id())) {
            flash('success', 'Comment deleted.');
        }
        $this->redirectBack('feed/index', 'post-' . (int) $comment['post_id']);
    }

    private function validate(string $content): ?string
    {
        if ($content === '') {
            return 'A comment cannot be empty.';
        }
        if (Input::length($content) > self::MAX_LENGTH) {
            return 'Comments can be at most ' . self::MAX_LENGTH . ' characters.';
        }
        return null;
    }

    /** Load a comment and make sure the logged-in user wrote it (404 / 403 otherwise). */
    private function ownedComment(int $id): array
    {
        $comment = (new CommentModel())->find($id);
        if ($comment === null) {
            $this->abort(404, 'That comment does not exist.');
        }
        if ((int) $comment['user_id'] !== (int) Auth::id()) {
            $this->abort(403, 'You can only change your own comments.');
        }
        return $comment;
    }
}
