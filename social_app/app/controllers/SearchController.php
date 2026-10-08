<?php
/**
 * SearchController - search users (name / username) or posts (keywords).
 */
class SearchController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    public function index(): void
    {
        $q    = Input::line($_GET['q'] ?? '');
        $type = ($_GET['type'] ?? 'users') === 'posts' ? 'posts' : 'users';
        $qLen = Input::length($q);

        $users    = [];
        $posts    = [];
        $comments = [];
        $error    = null;
        $searched = false;

        if ($q !== '') {
            if ($qLen < 2) {
                $error = 'Please type at least 2 characters.';
            } elseif ($qLen > 50) {
                $error = 'Search terms can be at most 50 characters.';
            } else {
                $searched = true;
                if ($type === 'users') {
                    $users = (new UserModel())->search($q);
                } else {
                    $posts    = (new PostModel())->search($q, (int) Auth::id());
                    $comments = (new CommentModel())->forPosts(array_column($posts, 'id'));
                }
            }
        }

        $this->view('search/index', [
            'title'    => 'Search',
            'q'        => $q,
            'type'     => $type,
            'users'    => $users,
            'posts'    => $posts,
            'comments' => $comments,
            'error'    => $error,
            'searched' => $searched,
        ]);
    }
}
