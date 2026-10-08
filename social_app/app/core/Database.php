<?php
/**
 * Database - creates one shared PDO connection (singleton).
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $cfg = require ROOT_PATH . '/config/database.php';
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'],
            (int) $cfg['port'],
            $cfg['name'],
            $cfg['charset']
        );

        try {
            $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
            ]);
        } catch (PDOException $ex) {
            // Do not leak credentials in the message.
            throw new RuntimeException(
                'Could not connect to the database. Check config/database.php and make sure MySQL is running and social_app.sql was imported.'
            );
        }

        $tz = (string) config('db_timezone', '+00:00');
        if (preg_match('/^[+-]\d{2}:\d{2}$/', $tz)) {
            $pdo->exec("SET time_zone = '" . $tz . "'");
        }

        self::$pdo = $pdo;
        return self::$pdo;
    }
}
