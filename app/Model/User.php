<?php

namespace App\Model;

use Core\Database;
use PDO;

class User
{
    private $id;
    private $username;
    private $email;
    private $password;
    private $role;
    private $phone;

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setRole($role) { $this->role = $role; }
    public function setPhone($phone) { $this->phone = $phone; }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getPhone() { return $this->phone; }

    public function register()
    {
        try {
            $pdo = Database::getInstance()->getConnection();

            if ($this->emailExists($this->email)) {
                return false;
            }

            $sql = "INSERT INTO users (name_user, email, pass_word, phone_number, role_user)
                    VALUES (:name, :email, :password, :phone, :role)";
            $stmt = $pdo->prepare($sql);
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            return $stmt->execute([
                ':name'     => $this->username,
                ':email'    => $this->email,
                ':password' => $hashedPassword,
                ':phone'    => $this->phone,
                ':role'     => $this->role ?? 'player',
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function login()
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $this->email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($this->password, $user['pass_word'])) {
                $this->id = $user['id_user'];
                $this->username = $user['name_user'];
                $this->email = $user['email'];
                $this->role = $user['role_user'];
                $this->phone = $user['phone_number'];
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function getById(int $id): ?array
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function emailExists(string $email): bool
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch() ? true : false;
        } catch (\PDOException $e) {
            return false;
        }
    }
}