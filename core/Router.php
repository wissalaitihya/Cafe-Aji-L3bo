<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['GET', 'POST'], true)) {
            http_response_code(405);
            header('Allow: GET, POST');
            echo 'Method Not Allowed';
            return;
        }
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // Strip the subdirectory base path so routes match
        // SCRIPT_NAME = /Cafe-Aji-L3bo/public/index.php → base = /Cafe-Aji-L3bo
        $basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = rtrim($uri, '/') ?: '/';

        // ── Rate limit every endpoint (OWASP) ──
        [$max, $window] = RateLimiter::limitFor($method, $uri);
        $retryAfter = RateLimiter::check(RateLimiter::key($method, $uri), $max, $window);
        if ($retryAfter !== null) {
            $this->rateLimited($uri, $retryAfter);
            return;
        }

        // Try exact match first
        if (isset($this->routes[$method][$uri])) {
            $this->callAction($this->routes[$method][$uri]);
            return;
        }

        // Try dynamic routes like /games/{id}
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $action) {
                $pattern = preg_replace('#\{([a-zA-Z]+)\}#', '([0-9]+)', $route);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // remove full match
                    $this->callAction($action, $matches);
                    return;
                }
            }
        }

        // No route found → 404
        http_response_code(404);
        require __DIR__ . '/../app/View/error/404.php';
    }

    private function callAction(string $action, array $params = []): void
    {
        // Format: "GameController@index"
        [$controllerName, $method] = explode('@', $action);
        $controllerClass = "App\\Controller\\{$controllerName}";

        try {
            if (!class_exists($controllerClass)) {
                throw new \Exception("Page not found");
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                throw new \Exception("Page not found");
            }

            // Release session lock early for read-only GET APIs so parallel
            // AJAX (available-tables + available-games + recommend) don't queue.
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'
                && (str_starts_with($action, 'ReservationController@api') || str_starts_with($action, 'GameController@api'))) {
                session_write_close();
            }

            call_user_func_array([$controller, $method], $params);
        } catch (\Exception $e) {
            http_response_code(500);
            error_log("Route error {$action}: " . $e->getMessage());
            echo "Something went wrong. Please try again later.";
        }
    }

    private function rateLimited(string $uri, int $retryAfter): void
    {
        http_response_code(429);
        header('Retry-After: ' . $retryAfter);
        $wantsJson = str_starts_with($uri, '/api/')
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
        if ($wantsJson) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Too many requests. Please slow down.', 'retry_after' => $retryAfter]);
            return;
        }
        http_response_code(429);
        require __DIR__ . '/../app/View/error/429.php';
    }
}