<?php
/**
 * PostController - CRUD for posts (CRUD Module #1).
 */
class PostController extends Controller
{
    private const MAX_LENGTH = 1000;

    public function __construct()
    {
        $this->requireAuth();
    }

    /** CREATE - POST post/create */
    public function create(): void
    {
        $this->requirePost();
        $content = Input::text($this->input('content'));

        if ($content === '') {
            flash('danger', 'Write something before posting.');
            $this->redirect('feed/index');
        }
        if (Input::length($content) > self::MAX_LENGTH) {
            flash('danger', 'Posts can be at most ' . self::MAX_LENGTH . ' characters.');
            $this->redirect('feed/index');
        }

        $upload = Upload::image($_FILES['image'] ?? null, 'posts');
        if ($upload['status'] === 'error') {
            flash('danger', $upload['error']);
            $this->redirect('feed/index');
        }

        $image = $upload['status'] === 'ok' ? $upload['filename'] : null;
        (new PostModel())->create((int) Auth::id(), $content, $image);

        flash('success', 'Your post was published.');
        $this->redirect('feed/index');
    }

    /** READ - GET post/show/{id} (a single post with its comments) */
    public function show($id = 0): void
    {
        $post = (new PostModel())->find((int) $id, (int) Auth::id());
        if ($post === null) {
            $this->abort(404, 'That post does not exist.');
        }
        $comments = (new CommentModel())->forPosts([(int) $post['id']]);

        $this->view('posts/show', [
            'title'    => 'Post by ' . $post['full_name'],
            'post'     => $post,
            'comments' => $comments[(int) $post['id']] ?? [],
        ]);
    }

    /** UPDATE (form) - GET post/edit/{id} */
    public function edit($id = 0): void
    {
        $post = $this->ownedPost((int) $id);
        $this->view('posts/edit', ['title' => 'Edit post', 'post' => $post, 'errors' => []]);
    }

    /** UPDATE (save) - POST post/update/{id} */
    public function update($id = 0): void
    {
        $this->requirePost();
        $post    = $this->ownedPost((int) $id);
        $content = Input::text($this->input('content'));
        $errors  = [];

        if ($content === '') {
            $errors['content'] = 'Post content cannot be empty.';
        } elseif (Input::length($content) > self::MAX_LENGTH) {
            $errors['content'] = 'Posts can be at most ' . self::MAX_LENGTH . ' characters.';
        }

        $upload = ['status' => 'none'];
        if (!$errors) {
            $upload = Upload::image($_FILES['image'] ?? null, 'posts');
            if ($upload['status'] === 'error') {
                $errors['image'] = $upload['error'];
            }
        }

        if ($errors) {
            $post['content'] = $content;
            $this->view('posts/edit', ['title' => 'Edit post', 'post' => $post, 'errors' => $errors]);
            return;
        }

        $image     = $post['image'];
        $deleteOld = false;
        if ($upload['status'] === 'ok') {
            $image     = $upload['filename'];
            $deleteOld = true;
        } elseif ($this->input('remove_image') === '1') {
            $image     = null;
            $deleteOld = true;
        }

        (new PostModel())->update((int) $post['id'], (int) Auth::id(), $content, $image);
        if ($deleteOld) {
            Upload::delete($post['image'], 'posts');
        }

        flash('success', 'Your post was updated.');
        $this->redirect('post/show/' . (int) $post['id']);
    }

    /** DELETE - POST post/delete/{id} */
    public function delete($id = 0): void
    {
        $this->requirePost();
        $post = $this->ownedPost((int) $id);

        if ((new PostModel())->delete((int) $post['id'], (int) Auth::id())) {
            Upload::delete($post['image'], 'posts');
            flash('success', 'Your post was deleted.');
        }
        $this->redirectBack('feed/index', '', ['post/show', 'post/edit']);
    }

    /** Load a post and make sure the logged-in user owns it (404 / 403 otherwise). */
    private function ownedPost(int $id): array
    {
        $post = (new PostModel())->findRaw($id);
        if ($post === null) {
            $this->abort(404, 'That post does not exist.');
        }
        if ((int) $post['user_id'] !== (int) Auth::id()) {
            $this->abort(403, 'You can only change your own posts.');
        }
        return $post;
    }
}
