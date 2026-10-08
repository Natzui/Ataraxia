<?php
/**
 * Base controller - shared helpers for all controllers.
 */
abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $route, array $params = []): void
    {
        header('Location: ' . url($route, $params));
        exit;
    }

    /**
     * Redirect to the page the user came from (same host only), otherwise to $fallbackRoute.
     * $avoid: if the referrer contains one of these strings, use the fallback (e.g. a page that no longer exists).
     */
    protected function redirectBack(string $fallbackRoute, string $anchor = '', array $avoid = []): void
    {
        $target  = url($fallbackRoute);
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        if ($referer !== '') {
            $p        = parse_url($referer);
            $thisHost = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
            $path     = $p['path'] ?? '';
            if (
                $p && isset($p['host']) && strtolower($p['host']) === $thisHost
                && $path !== '' && $path[0] === '/' && strpos($path, '//') !== 0
            ) {
                $candidate = $path . (isset($p['query']) ? '?' . $p['query'] : '');
                $skip = false;
                foreach ($avoid as $needle) {
                    if (strpos($candidate, $needle) !== false) {
                        $skip = true;
                    }
                }
                if (!$skip) {
                    $target = str_replace(["\r", "\n"], '', $candidate);
                }
            }
        }

        header('Location: ' . $target . ($anchor !== '' ? '#' . $anchor : ''));
        exit;
    }

    protected function requireAuth(): void
    {
        if (Auth::check()) {
            return;
        }
        if ($this->isAjax()) {
            $this->json(['error' => 'Please log in.'], 401);
        }
        flash('warning', 'Please log in to continue.');
        $this->redirect('auth/login');
    }

    protected function requireGuest(): void
    {
        if (Auth::check()) {
            $this->redirect('feed/index');
        }
    }

    /** The logged-in user (array). Redirects to login if the session is no longer valid. */
    protected function currentUser(): array
    {
        $user = Auth::user();
        if ($user === null) {
            $this->redirect('auth/login');
        }
        return $user;
    }

    /** State-changing actions must be POST requests carrying a valid CSRF token. */
    protected function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Allow: POST');
            $this->abort(405, 'This action must be submitted from a form.');
        }
        if (!csrf_verify()) {
            if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                $this->abort(413, 'The submitted data was too large. Try a smaller image.');
            }
            $this->abort(403, 'Your session expired or the form was invalid. Please go back, refresh the page and try again.');
        }
    }

    protected function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        View::render('errors/error', [
            'title'   => 'Error ' . $code,
            'code'    => $code,
            'message' => $message !== '' ? $message : 'Something went wrong.',
        ]);
        exit;
    }

    /** Raw POST value as a string ('' if missing or not a string). */
    protected function input(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? $value : '';
    }
}
