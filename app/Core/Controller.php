<?php

namespace App\Core;

class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        
        $viewPath = __DIR__ . "/../../View/{$view}.php";
        
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . "/../../View/error/404.php";
        }
        
        require_once $viewPath;
    }

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}