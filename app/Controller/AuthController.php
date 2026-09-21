<?php

namespace App\Controller;

use App\Model\User;
use Core\Csrf;
use Core\Sanitizer;
use Core\Validator;

class AuthController
{
    public function loginForm()
    {
        $error = isset($_GET['error']) ? Sanitizer::str($_GET['error'], 200) : null;
        $success = isset($_GET['success']) ? Sanitizer::str($_GET['success'], 200) : null;
        $this->render('auth/login', ['error' => $error, 'success' => $success]);
    }

    public function login()
    {
        if (($csrfError = Csrf::requireValid()) !== null) {
            $this->redirect('/login?error=' . urlencode($csrfError));
            return;
        }
        $email = Sanitizer::str($_POST['email'] ?? '', 100);
        $password = (string) ($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $this->redirect('/login?error=' . urlencode('Please fill all fields'));
            return;
        }

        if (Validator::email($email) !== null) {
            // Generic message to avoid user enumeration
            $this->redirect('/login?error=' . urlencode('Invalid email or password'));
            return;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);

        if ($user->login()) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getUsername();
            $_SESSION['user_role'] = $user->getRole();

            if ($user->getRole() === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/player/dashboard');
            }
        } else {
            // No artificial delay: POST /login is rate-limited (5/min) and
            // password_verify itself (~250ms) already dominates failure timing.
            $this->redirect('/login?error=' . urlencode('Invalid email or password'));
        }
    }

    public function registerForm()
    {
        $error = isset($_GET['error']) ? Sanitizer::str($_GET['error'], 200) : null;
        $this->render('auth/register', ['error' => $error]);
    }

    public function register()
    {
        if (($csrfError = Csrf::requireValid()) !== null) {
            $this->redirect('/register?error=' . urlencode($csrfError));
            return;
        }
        $name = Sanitizer::str($_POST['name'] ?? '', 40);
        $email = Sanitizer::str($_POST['email'] ?? '', 100);
        $phone = Sanitizer::str($_POST['phone'] ?? '', 20);
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if (empty($name) || empty($email) || empty($password)) {
            $this->redirect('/register?error=' . urlencode('Please fill all fields'));
            return;
        }

        if (($e = Validator::name($name)) !== null) {
            $this->redirect('/register?error=' . urlencode($e));
            return;
        }
        if (($e = Validator::email($email)) !== null) {
            $this->redirect('/register?error=' . urlencode($e));
            return;
        }
        if (($e = Validator::phone($phone)) !== null) {
            $this->redirect('/register?error=' . urlencode($e));
            return;
        }
        if (($e = Validator::password($password)) !== null) {
            $this->redirect('/register?error=' . urlencode($e));
            return;
        }

        if ($password !== $confirm) {
            $this->redirect('/register?error=' . urlencode('Passwords do not match'));
            return;
        }

        $user = new User();
        $user->setUsername($name);
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setPassword($password);
        $user->setRole('player');

        if ($user->register()) {
            $this->redirect('/login?success=' . urlencode('Account created! Please login'));
        } else {
            $this->redirect('/register?error=' . urlencode('Email already exists'));
        }
    }

    public function logout()
    {
        // Prefer POST with CSRF, but keep GET working for existing links.
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && Csrf::check($_POST['_csrf'] ?? null) === false) {
            http_response_code(419);
            $this->render('auth/login', ['error' => 'Invalid logout token.']);
            return;
        }
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