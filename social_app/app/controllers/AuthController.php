<?php
/**
 * AuthController - register, login and logout.
 */
class AuthController extends Controller
{
    /** Used only to keep login timing similar when the username does not exist. */
    private const DUMMY_HASH = '$2y$10$abcdefghijklmnopqrstuuWmT2k8o3qXz2vPz0m3lJm9q4gYw5pQK';

    public function login(): void
    {
        $this->requireGuest();
        $errors = [];
        $old    = [];

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (!csrf_verify()) {
                $errors['form'] = 'Your session expired. Please try again.';
            } else {
                $username = Input::line($this->input('username'));
                $password = $this->input('password');
                $old['username'] = $username;

                if ($username === '' || $password === '') {
                    $errors['form'] = 'Please enter your username and password.';
                } else {
                    $users = new UserModel();
                    $user  = $users->findByUsername($username);

                    if ($user === null) {
                        password_verify($password, self::DUMMY_HASH);
                        $errors['form'] = 'Invalid username or password.';
                    } elseif (!password_verify($password, $user['password'])) {
                        $errors['form'] = 'Invalid username or password.';
                    } else {
                        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                            $users->updatePassword((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
                        }
                        Auth::login($user);
                        flash('success', 'Welcome back, ' . $user['full_name'] . '!');
                        $this->redirect('feed/index');
                    }
                }
            }
        }

        $this->view('auth/login', ['title' => 'Log in', 'errors' => $errors, 'old' => $old]);
    }

    public function register(): void
    {
        $this->requireGuest();
        $errors = [];
        $old    = [];

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (!csrf_verify()) {
                $errors['form'] = 'Your session expired or the upload was too large. Please try again.';
            } else {
                $fullName = Input::line($this->input('full_name'));
                $username = Input::line($this->input('username'));
                $password = $this->input('password');
                $confirm  = $this->input('password_confirm');
                $old      = ['full_name' => $fullName, 'username' => $username];
                $users    = new UserModel();

                // ---- Server-side validation ----
                $nameLen = Input::length($fullName);
                if ($nameLen < 2 || $nameLen > 100) {
                    $errors['full_name'] = 'Full name must be 2 to 100 characters.';
                }

                if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
                    $errors['username'] = 'Username must be 3 to 30 characters: letters, numbers and underscores only.';
                } elseif ($users->usernameExists($username)) {
                    $errors['username'] = 'That username is already taken.';
                }

                if (strlen($password) < 8) {
                    $errors['password'] = 'Password must be at least 8 characters.';
                } elseif (strlen($password) > 72) {
                    $errors['password'] = 'Password must be 72 characters or fewer.';
                } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
                    $errors['password'] = 'Password must contain at least one letter and one number.';
                }

                if ($confirm !== $password) {
                    $errors['password_confirm'] = 'Passwords do not match.';
                }

                // ---- Optional profile picture ----
                $avatar = null;
                if (!$errors) {
                    $upload = Upload::image($_FILES['profile_image'] ?? null, 'avatars');
                    if ($upload['status'] === 'error') {
                        $errors['profile_image'] = $upload['error'];
                    } elseif ($upload['status'] === 'ok') {
                        $avatar = $upload['filename'];
                    }
                }

                if (!$errors) {
                    try {
                        $id = $users->create($username, password_hash($password, PASSWORD_DEFAULT), $fullName, $avatar);
                        Auth::login(['id' => $id]);
                        flash('success', 'Welcome to ' . config('app_name') . ', ' . $fullName . '!');
                        $this->redirect('feed/index');
                    } catch (PDOException $ex) {
                        Upload::delete($avatar, 'avatars');
                        if ($ex->getCode() === '23000') {      // duplicate username (race condition)
                            $errors['username'] = 'That username is already taken.';
                        } else {
                            throw $ex;
                        }
                    }
                }
            }
        }

        $this->view('auth/register', ['title' => 'Create account', 'errors' => $errors, 'old' => $old]);
    }

    public function logout(): void
    {
        $this->requirePost();
        Auth::logout();
        flash('success', 'You have been logged out.');
        $this->redirect('auth/login');
    }
}
