<?php

class AuthController
 {
    public function login() 
    {
        include __DIR__."/../View/Auth/login.php";
    }
    public function register()
    {
        include __DIR__."/../View/Auth/register.php";
    }
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = new ???????????();
            $user->setEmail($email);
            $user->setPassword($password);
            if ($user->login()) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_name'] = $user->getUsername();
                $_SESSION['user_role'] = $user->getRole();
                
                if ($_SESSION['user_role'] === 'admin') {
                    header("Location: /Views/Session/dashboard.php");
                } else {
                    header("Location: /Views/player/dashboard.php");
                }
                exit();
                } else {
                    header("Location: /Views/Auth/login.php?error=invalid_credentials");
                exit();
                 } 
            }
        }

        public function handleRegister(){
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username         = $_POST['name'] ?? '';
            $email            = $_POST['email'] ?? '';
            $password         = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if ($password !== $password_confirm) {
                header("Location: /Views/Auth/register.php?error=Les+mots+de+passe+ne+correspondent+pas");
                exit();
            }
            $user = new ???????();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole('user');

            if ($user->register()) {
                header("Location: /Views/Auth/login.php?success=Inscription+reussie.+Veuillez+vous+connecter.");
                exit();
            } else {
                header("Location: /Views/Auth/register.php?error=Email+deja+utilise+ou+erreur");
                exit();
            }
        }
    }
     public function handleLogout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: /Views/Auth/login.php");
        exit();
    }

}









?>