<?php

namespace Core;

/**
 * Central output-escaping + input-sanitizing helper (OWASP XSS prevention).
 * Use e() in every view for anything that came from users/DB/$_GET/$_SESSION.
 */
class Sanitizer
{
    public static function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function str(mixed $value, int $maxLen = 2000): string
    {
        $s = trim((string) ($value ?? ''));
        // Strip control chars except tab/newline
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? '';
        if (mb_strlen($s) > $maxLen) {
            $s = mb_substr($s, 0, $maxLen);
        }
        return $s;
    }

    public static function int(mixed $value, int $default = 0): int
    {
        $v = filter_var($value, FILTER_VALIDATE_INT);
        return $v === false ? $default : (int) $v;
    }

    public static function urlPath(string $path): string
    {
        // Allow only safe path chars for redirects/IDs in URLs
        return preg_replace('#[^A-Za-z0-9/_\-.]#', '', $path) ?? '';
    }
}
