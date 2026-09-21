<?php

namespace Core;

use Predis\Client as RedisClient;

/**
 * Rate limiting on every API/POST endpoint (OWASP automated-threats).
 * Backend: Redis (shared across containers). Fallback: file bucket in sys temp
 * so XAMPP without Redis still gets protection.
 *
 * Sliding window: key => list of microtime hits, trimmed to $window seconds.
 */
class RateLimiter
{
    private static ?RedisClient $redis = null;
    private static bool $triedRedis = false;

    /** Route-class limits: [max requests, window seconds]. */
    public static function limitFor(string $method, string $uri): array
    {
        $path = strtolower($uri);

        if ($method === 'POST' && (str_contains($path, '/login') || str_contains($path, '/register'))) {
            return [5, 60]; // brute-force guard
        }
        if (str_starts_with($path, '/api/')) {
            return [60, 60];
        }
        if ($method === 'POST') {
            return [30, 60];
        }
        return [200, 60];
    }

    public static function key(string $method, string $uri): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'cli';
        $ip = is_string($ip) ? explode(',', $ip)[0] : 'cli';
        $ip = trim($ip);
        $user = $_SESSION['user_id'] ?? 'guest';
        $route = $method . ':' . strtolower(preg_replace('#/\d+#', '/{id}', strtok($uri, '?') ?: '/'));
        return "rl:" . md5($ip . '|' . $user . '|' . $route);
    }

    /**
     * Returns null when allowed, or seconds until retry when blocked.
     */
    public static function check(string $key, int $max, int $window): ?int
    {
        $now = microtime(true);
        $redis = self::redis();
        if ($redis !== null) {
            try {
                $member = (string) $now . ':' . bin2hex(random_bytes(4));
                $redis->zadd($key, [$member => $now]);
                $redis->zremrangebyscore($key, 0, $now - $window);
                $count = $redis->zcard($key);
                $redis->expire($key, $window + 5);
                if ($count > $max) {
                    $oldest = $redis->zrange($key, 0, 0, ['withscores' => true]);
                    $oldestTs = $oldest ? (float) reset($oldest) : $now;
                    return max(1, (int) ceil($window - ($now - $oldestTs)));
                }
                return null;
            } catch (\Throwable) {
                // fall through to file backend
            }
        }
        return self::checkFile($key, $max, $window, $now);
    }

    private static function checkFile(string $key, int $max, int $window, float $now): ?int
    {
        $file = sys_get_temp_dir() . '/aji_ratelimit_' . md5($key) . '.json';
        $hits = [];
        if (is_file($file)) {
            $raw = @file_get_contents($file);
            $decoded = $raw ? json_decode($raw, true) : null;
            if (is_array($decoded)) {
                $hits = array_values(array_filter($decoded, fn($t) => is_numeric($t) && ($now - (float) $t) < $window));
            }
        }
        $hits[] = $now;
        // Keep file small
        $hits = array_slice($hits, -$max - 5);
        @file_put_contents($file, json_encode($hits), LOCK_EX);
        if (count($hits) > $max) {
            $oldest = $hits[count($hits) - $max - 1] ?? $hits[0];
            return max(1, (int) ceil($window - ($now - (float) $oldest)));
        }
        return null;
    }

    private static function redis(): ?RedisClient
    {
        if (self::$triedRedis) {
            return self::$redis;
        }
        self::$triedRedis = true;
        $host = getenv('REDIS_HOST') ?: '127.0.0.1';
        $port = (int) (getenv('REDIS_PORT') ?: 6379);
        try {
            $client = new RedisClient([
                'scheme' => 'tcp',
                'host' => $host,
                'port' => $port,
                'timeout' => 0.3,
            ]);
            $client->ping();
            self::$redis = $client;
        } catch (\Throwable) {
            self::$redis = null;
        }
        return self::$redis;
    }

    public static function blockedResponse(int $retryAfter): void
    {
        http_response_code(429);
        header('Retry-After: ' . $retryAfter);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Too many requests. Please slow down.', 'retry_after' => $retryAfter]);
        exit;
    }
}
