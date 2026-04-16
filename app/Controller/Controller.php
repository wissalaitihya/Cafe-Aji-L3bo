<?php

namespace App\Controller;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . "/../View/{$view}.php";

        if (!file_exists($viewPath)) {
            http_response_code(404);
            require __DIR__ . '/../View/error/404.php';
            return;
        }

        require $viewPath;
    }

    protected function redirect(string $url): void
    {
        header("Location: " . BASE . $url);
        exit;
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    protected function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            http_response_code(403);
            require __DIR__ . '/../View/error/403.php';
            exit;
        }
    }
}