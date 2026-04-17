<?php

namespace App\Controller;

use App\Model\User;

class AuthController
{
    public function loginForm()
    {
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;
        $this->render('auth/login', ['error' => $error, 'success' => $success]);
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->redirect('/login?error=Please fill all fields');
            return;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);

        if ($user->login()) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getUsername();
            $_SESSION['user_role'] = $user->getRole();

            if ($user->getRole() === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/player/dashboard');
            }
        } else {
            $this->redirect('/login?error=Invalid email or password');
        }
    }

    public function registerForm()
    {
        $error = $_GET['error'] ?? null;
        $this->render('auth/register', ['error' => $error]);
    }

    public function register()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $this->redirect('/register?error=Please fill all fields');
            return;
        }

        if ($password !== $confirm) {
            $this->redirect('/register?error=Passwords do not match');
            return;
        }

        $user = new User();
        $user->setUsername($name);
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setPassword($password);
        $user->setRole('player');

        if ($user->register()) {
            $this->redirect('/login?success=Account created! Please login');
        } else {
            $this->redirect('/register?error=Email already exists');
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        $this->redirect('/login');
        exit;
    }

    // ========================
    // HELPER METHODS
    // ========================
    private function render($view, $data = [])
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

    private function redirect($url)
    {
        header("Location: " . BASE_PATH . $url);
        exit;
    }
}