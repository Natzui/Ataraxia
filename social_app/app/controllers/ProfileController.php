<?php
/**
 * ProfileController - view a profile, edit your own profile.
 */
class ProfileController extends Controller
{
    private const BIO_MAX = 255;

    public function __construct()
    {
        $this->requireAuth();
    }

    /** profile/show/{id}  (no id = my own profile) */
    public function show($id = 0): void
    {
        $id = (int) $id;
        if ($id === 0) {
            $id = (int) Auth::id();
        }

        $profileUser = (new UserModel())->findById($id);
        if ($profileUser === null) {
            $this->abort(404, 'That user does not exist.');
        }

        $posts   = new PostModel();
        $perPage = (int) config('posts_per_page', 10);
        $total   = $posts->countByUser($id);
        $pages   = max(1, (int) ceil($total / $perPage));
        $page    = min($pages, max(1, (int) ($_GET['page'] ?? 1)));

        $items    = $posts->byUser($id, (int) Auth::id(), $perPage, ($page - 1) * $perPage);
        $comments = (new CommentModel())->forPosts(array_column($items, 'id'));

        $this->view('profile/show', [
            'title'       => $profileUser['full_name'],
            'profileUser' => $profileUser,
            'posts'       => $items,
            'comments'    => $comments,
            'postCount'   => $total,
            'page'        => $page,
            'pages'       => $pages,
        ]);
    }

    public function edit(): void
    {
        $user = $this->currentUser();
        $this->view('profile/edit', [
            'title'  => 'Edit profile',
            'user'   => $user,
            'errors' => [],
        ]);
    }

    public function update(): void
    {
        $this->requirePost();
        $user = $this->currentUser();

        $fullName = Input::line($this->input('full_name'));
        $bio      = Input::text($this->input('bio'));
        $errors   = [];

        $nameLen = Input::length($fullName);
        if ($nameLen < 2 || $nameLen > 100) {
            $errors['full_name'] = 'Full name must be 2 to 100 characters.';
        }
        if (Input::length($bio) > self::BIO_MAX) {
            $errors['bio'] = 'Bio can be at most ' . self::BIO_MAX . ' characters.';
        }

        $upload = ['status' => 'none'];
        if (!$errors) {
            $upload = Upload::image($_FILES['profile_image'] ?? null, 'avatars');
            if ($upload['status'] === 'error') {
                $errors['profile_image'] = $upload['error'];
            }
        }

        if ($errors) {
            $user['full_name'] = $fullName;
            $user['bio']       = $bio;
            $this->view('profile/edit', ['title' => 'Edit profile', 'user' => $user, 'errors' => $errors]);
            return;
        }

        $image    = $user['profile_image'];
        $deleteOld = false;
        if ($upload['status'] === 'ok') {
            $image     = $upload['filename'];
            $deleteOld = true;
        } elseif ($this->input('remove_photo') === '1') {
            $image     = null;
            $deleteOld = true;
        }

        (new UserModel())->updateProfile((int) $user['id'], $fullName, $bio !== '' ? $bio : null, $image);
        if ($deleteOld) {
            Upload::delete($user['profile_image'], 'avatars');
        }

        flash('success', 'Your profile was updated.');
        $this->redirect('profile/show/' . (int) $user['id']);
    }
}
