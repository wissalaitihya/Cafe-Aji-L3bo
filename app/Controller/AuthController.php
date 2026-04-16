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
            $pass_word = $_POST['pass_word'] ?? '';

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($pass_word);
            if ($user->login()) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_name'] = $user->getUsername();
                $_SESSION['user_role'] = $user->getRole();
                
                if ($_SESSION['user_role'] === 'admin') {
                    header("Location: /Cafe-Aji-L3bo/public/index.php?action=adminDashboard");
                } else {
                    header("Location: /Cafe-Aji-L3bo/public/index.php?action=playerDashboard");
                }
                exit();
                } else {
                    header("Location: /Cafe-Aji-L3bo/public/index.php?action=login&error=invalid_credentials");
                exit();
                 } 
            }
        }

        public function handleRegister(){
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username         = $_POST['name_user'] ?? '';
            $phone            = $_POST['phone_number'] ?? '';
            $email            = $_POST['email'] ?? '';
            $password         = $_POST['pass_word'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if ($password !== $password_confirm) {
                header("Location: /Cafe-Aji-L3bo/public/index.php?action=register&error=Les+mots+de+passe+ne+correspondent+pas");
                exit();
            }
            $user = new User();
            $user->setUsername($username);
            $user->setPhoneNumber($phone);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole('player');

         if ($user->register()) {
    header("Location: /Cafe-Aji-L3bo/public/index.php?action=login&success=1");
    exit();
} else {
    header("Location: /Cafe-Aji-L3bo/public/index.php?action=register&error=1");
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