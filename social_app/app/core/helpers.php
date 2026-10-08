<?php
/**
 * Global helper functions (available in controllers AND views).
 */

/** Read a value from config/config.php */
function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require ROOT_PATH . '/config/config.php';
    }
    return $config[$key] ?? $default;
}

/** Escape output for HTML (XSS protection). Use it for EVERY dynamic value in a view. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL path of the folder that contains index.php, e.g. "/social_app/public" (or "" on a virtual host). */
function base_url(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir    = rtrim(dirname($script), '/');
    return $dir === '.' ? '' : $dir;
}

/** Build a link to a route, e.g. url('profile/show/3', ['page' => 2]) */
function url(string $route = '', array $params = []): string
{
    $route = trim($route, '/');
    $qs    = $route !== '' ? 'url=' . $route : '';
    if ($params) {
        $qs .= ($qs !== '' ? '&' : '') . http_build_query($params);
    }
    return base_url() . '/index.php' . ($qs !== '' ? '?' . $qs : '');
}

function current_route(): string
{
    $route = isset($_GET['url']) && is_string($_GET['url']) ? trim($_GET['url'], '/') : '';
    return $route;
}

function asset(string $path): string
{
    return base_url() . '/assets/' . ltrim($path, '/');
}

function upload_url(string $folder, string $filename): string
{
    return base_url() . '/uploads/' . $folder . '/' . rawurlencode(basename($filename));
}

function avatar_url(array $user): string
{
    if (!empty($user['profile_image'])) {
        return upload_url('avatars', $user['profile_image']);
    }
    return asset('img/default-avatar.svg');
}

/** <img> tag for a user's avatar (works with users rows and post/comment rows that join users). */
function avatar_img(array $user, int $size = 40, string $class = ''): string
{
    $alt = ($user['username'] ?? 'user') . ' avatar';
    return '<img src="' . e(avatar_url($user)) . '" width="' . $size . '" height="' . $size . '"'
        . ' class="avatar rounded-circle ' . e($class) . '" alt="' . e($alt) . '">';
}

/** "5 mins ago", "2 days ago" ... */
function time_ago(string $datetime): string
{
    $ts = strtotime($datetime);
    if ($ts === false) {
        return '';
    }
    $diff = max(0, time() - $ts);
    if ($diff < 60) {
        return 'just now';
    }
    if ($diff < 3600) {
        $n = intdiv($diff, 60);
        return $n . ' min' . ($n > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 86400) {
        $n = intdiv($diff, 3600);
        return $n . ' hour' . ($n > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 604800) {
        $n = intdiv($diff, 86400);
        return $n . ' day' . ($n > 1 ? 's' : '') . ' ago';
    }
    return date('M j, Y', $ts);
}

function full_date(string $datetime): string
{
    $ts = strtotime($datetime);
    return $ts === false ? '' : date('M j, Y \a\t g:i A', $ts);
}

function partial(string $view, array $data = []): void
{
    View::partial($view, $data);
}

/* ---------- CSRF protection ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return is_string($token) && $token !== '' && hash_equals($_SESSION['_csrf'] ?? '', $token);
}

/* ---------- Flash messages ---------- */

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $flashes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flashes;
}

/* ---------- Form helpers ---------- */

/** Escaped old value of a form field. */
function old_value(array $old, string $key): string
{
    return e($old[$key] ?? '');
}

/** ' is-invalid' when the field has a validation error. */
function invalid_class(array $errors, string $key): string
{
    return isset($errors[$key]) ? ' is-invalid' : '';
}
