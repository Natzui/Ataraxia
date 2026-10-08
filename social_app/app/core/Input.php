<?php
/**
 * Input - server-side sanitising helpers.
 * Remember: sanitising on input is only half the job. Every value is ALSO
 * escaped with e() when it is printed (XSS protection) and every SQL query
 * uses prepared statements (SQL injection protection).
 */
class Input
{
    /** Multi-line text: trims, normalises line breaks and removes control characters. */
    public static function text($value): string
    {
        if (!is_string($value)) {
            return '';
        }
        if (!mb_check_encoding($value, 'UTF-8')) {
            return '';
        }
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $clean = preg_replace('/[^\P{C}\n\t]/u', '', $value);   // drop control chars except \n and \t
        $value = $clean === null ? '' : $clean;
        $value = preg_replace("/\n{3,}/", "\n\n", $value);
        return trim((string) $value);
    }

    /** Single-line text: like text() but also strips HTML tags and collapses whitespace. */
    public static function line($value): string
    {
        $value = self::text($value);
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value);
        return trim((string) $value);
    }

    public static function length(string $value): int
    {
        return mb_strlen($value, 'UTF-8');
    }
}
