<?php
/**
 * Front controller - every request enters the application here.
 */
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

require_once APP_PATH . '/core/helpers.php';

date_default_timezone_set((string) config('timezone', 'UTC'));
error_reporting(E_ALL);
ini_set('display_errors', config('debug') ? '1' : '0');

// Simple class autoloader (core, models, controllers)
spl_autoload_register(function (string $class): void {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// Friendly error page for uncaught exceptions
set_exception_handler(function (Throwable $ex): void {
    error_log('[MiniSocial] ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine());
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (!headers_sent()) {
        http_response_code(500);
    }
    $message = config('debug') ? $ex->getMessage() : 'Something went wrong. Please try again later.';
    try {
        View::render('errors/error', ['title' => 'Server error', 'code' => 500, 'message' => $message]);
    } catch (Throwable $inner) {
        echo 'Server error.';
    }
});

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

// Secure session setup
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('MINISOCIALSESS');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

(new Router())->dispatch();
