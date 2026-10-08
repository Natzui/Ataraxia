<?php
/**
 * Auth - session-based authentication helpers.
 */
class Auth
{
    private static ?array $cache = null;

    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user_id'] : null;
    }

    /** The logged-in user's row (without password), loaded once per request. */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        if (self::$cache === null) {
            self::$cache = (new UserModel())->findById((int) $_SESSION['user_id']);
            if (self::$cache === null) {      // account no longer exists
                self::logout();
                return null;
            }
        }
        return self::$cache;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);          // prevent session fixation
        $_SESSION['user_id'] = (int) $user['id'];
        self::$cache = null;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);          // fresh, empty session (flash messages still work)
        self::$cache = null;
    }
}
