<?php
/**
 * FeedController - the newsfeed / timeline (latest posts first).
 */
class FeedController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    public function index(): void
    {
        $posts   = new PostModel();
        $perPage = (int) config('posts_per_page', 10);
        $total   = $posts->countAll();
        $pages   = max(1, (int) ceil($total / $perPage));
        $page    = min($pages, max(1, (int) ($_GET['page'] ?? 1)));

        $items    = $posts->feed((int) Auth::id(), $perPage, ($page - 1) * $perPage);
        $comments = (new CommentModel())->forPosts(array_column($items, 'id'));

        $this->view('feed/index', [
            'title'    => 'Home',
            'posts'    => $items,
            'comments' => $comments,
            'page'     => $page,
            'pages'    => $pages,
        ]);
    }
}
