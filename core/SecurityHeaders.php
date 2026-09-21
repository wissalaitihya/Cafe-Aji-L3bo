<?php

namespace Core;

/**
 * Security headers (OWASP secure-headers). Called once in public/index.php.
 * CSP is deliberately permissive enough for current inline scripts/styles
 * + loremflickr/unsplash/google-fonts, tightened where cheap.
 */
class SecurityHeaders
{
    public static function send(): void
    {
        if (headers_sent()) {
            return;
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        // Remove PHP version exposure
        header_remove('X-Powered-By');
        // CSP: allow self + inline (current views use inline JS) + known CDNs.
        // No object/embed, no base-uri hijack, upgrade insecure requests in prod.
        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline'; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
            . "font-src 'self' https://fonts.gstatic.com; "
            . "img-src 'self' data: https://loremflickr.com https://images.unsplash.com; "
            . "connect-src 'self'; "
            . "form-action 'self'; "
            . "frame-ancestors 'self'; "
            . "base-uri 'self'; "
            . "object-src 'none'";
        header('Content-Security-Policy: ' . $csp);
        if (($_SERVER['HTTPS'] ?? 'off') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
