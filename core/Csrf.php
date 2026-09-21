<?php

namespace Core;

/**
 * CSRF protection (OWASP A01). One token per session, rotated every 24h.
 * Insert with Csrf::field() inside every <form method="POST">,
 * verify with Csrf::check() at the top of every POST handler.
 */
class Csrf
{
    private const KEY = '_csrf_token';
    private const TIME_KEY = '_csrf_time';
    private const TTL = 86400;

    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return '';
        }
        $tok = $_SESSION[self::KEY] ?? null;
        $at = $_SESSION[self::TIME_KEY] ?? 0;
        if (!is_string($tok) || $tok === '' || (time() - (int) $at) > self::TTL) {
            $tok = bin2hex(random_bytes(32));
            $_SESSION[self::KEY] = $tok;
            $_SESSION[self::TIME_KEY] = time();
        }
        return $tok;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . Sanitizer::e(self::token()) . '">';
    }

    public static function check(?string $submitted): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }
        $expected = $_SESSION[self::KEY] ?? '';
        if (!is_string($expected) || $expected === '' || !is_string($submitted) || $submitted === '') {
            return false;
        }
        return hash_equals($expected, $submitted);
    }

    /** Returns error string or null on success. */
    public static function requireValid(): ?string
    {
        if (!self::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            return 'Invalid or expired form token. Please reload the page and try again.';
        }
        return null;
    }
}
