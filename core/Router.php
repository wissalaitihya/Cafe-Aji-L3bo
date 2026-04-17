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
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip the subdirectory base path so routes match
        // SCRIPT_NAME = /Cafe-Aji-L3bo/public/index.php → base = /Cafe-Aji-L3bo
        $basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = rtrim($uri, '/') ?: '/';

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
                throw new \Exception("Controller {$controllerClass} not found");
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                throw new \Exception("Method {$method} not found in {$controllerClass}");
            }

            call_user_func_array([$controller, $method], $params);
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    }
}