<?php

use Core\SecurityHeaders;

date_default_timezone_set('Africa/Casablanca');

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env (never commit real secrets). Safe no-op if file missing (e.g. prod injects env).
try {
    if (class_exists(\Dotenv\Dotenv::class) && is_file(__DIR__ . '/../.env')) {
        \Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
    }
} catch (\Throwable) {
    // Env loading must never break the app
}

// Secure session cookies (OWASP session management)
$isHttps = ($_SERVER['HTTPS'] ?? 'off') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_start();

// Idle timeout: 2h
$maxIdle = 7200;
if (isset($_SESSION['last_activity']) && (time() - (int)$_SESSION['last_activity']) > $maxIdle) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Dynamic base path: works regardless of where the project is deployed
define('BASE_PATH', rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'));

SecurityHeaders::send();

$router = new Core\Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch();