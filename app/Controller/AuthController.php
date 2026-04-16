<?php
namespace App\Controller;
use App\Model\User;


class AuthController
 {
    public function login() 
    {
        include __DIR__."/../View/auth/login.php";
    }
    public function register()
    {
        include __DIR__ ."/../View/auth/register.php";
    }
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = new User();
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
                    header("Location: /View/dashboard/admin.php");
                } else {
                    header("Location: /View/dashboard/player.php");
                }
                exit();
                } else {
                    header("Location: /View/auth/login.php?error=invalid_credentials");
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
                header("Location: /View/auth/register.php?error=Les+mots+de+passe+ne+correspondent+pas");
                exit();
            }
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole('player');

            if ($user->register()) {
                header("Location: /View/auth/login.php?success=Inscription+reussie.+Veuillez+vous+connecter.");
                exit();
            } else {
                header("Location: /View/auth/register.php?error=Email+deja+utilise+ou+erreur");
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
        header("Location: /View/auth/login.php");
        exit();
    }

}

?>