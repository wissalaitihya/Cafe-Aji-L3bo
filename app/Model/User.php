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


    // ========================
    // SETTERS
    // ========================
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }



    // ========================
    // GETTERS
    // ========================
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }
   

    // ========================
    // REGISTER
    // ========================
    public function register()
    {
        $pdo = Database::getInstance()->getConnection();

        // Check if email already exists
        if ($this->emailExists($this->email)) {
            return false;
        }
        

        $sql = "INSERT INTO users (name_user, email, pass_word, role_user)
                VALUES (:name, :email, :password, :role)";

        $stmt = $pdo->prepare($sql);

        // 🔐 Hash password here
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        return $stmt->execute([
            ':name' => $this->username,
            ':email' => $this->email,
            ':password' => $hashedPassword,
            ':role' => $this->role ?? 'player',
        ]);
    }

    // ========================
    // LOGIN
    // ========================
    public function login()
    {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $this->email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔐 Verify password here
        if ($user && password_verify($this->password, $user['pass_word'])) {
            $this->id = $user['id_user'];
            $this->username = $user['name_user'];
            $this->email = $user['email'];
            $this->role = $user['role_user'];

            return true;
        }

        return false;
    }

    // ========================
    // GET USER BY ID
    // ========================
    public function getById($id)
    {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM users WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ========================
    // CHECK EMAIL EXISTS
    // ========================
    public function emailExists($email)
    {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT id_user FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch() ? true : false;
    }
}


    // ========================
    // SETTERS
    // ========================
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setPhoneNumber($phone)
{
    $this->phone = $phone;
}

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }



    // ========================
    // GETTERS
    // ========================
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function getPhoneNumber()
{
    return $this->phone;
}

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }
   

    // ========================
    // REGISTER
    // ========================
    public function register()
    {
        $pdo = Database::getInstance()->getConnection();

        // Check if email already exists
        if ($this->emailExists($this->email)) {
            return false;
        }
        

        $sql = "INSERT INTO users (name_user,phone_number, email, pass_word, role_user)
                VALUES (:name, :phone, :email, :password, :role)";

        $stmt = $pdo->prepare($sql);

        // 🔐 Hash password here
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        return $stmt->execute([
            ':name' => $this->username,
            ':phone' => $this->phone,
            ':email' => $this->email,
            ':password' => $hashedPassword,
            ':role' => $this->role ?? 'player',
        ]);
    }

    // ========================
    // LOGIN
    // ========================
    public function login()
    {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $this->email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔐 Verify password here
        if ($user && password_verify($this->password, $user['pass_word'])) {
            $this->id = $user['id_user'];
            $this->username = $user['name_user'];
            $this->phone = $user['phone_number'];
            $this->email = $user['email'];
            $this->role = $user['role_user'];
           

            return true;
        }

        return false;
    }

    // ========================
    // GET USER BY ID
    // ========================
    public function getById($id)
    {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM users WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ========================
    // CHECK EMAIL EXISTS
    // ========================
    public function emailExists($email)
    {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT id_user FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch() ? true : false;
    }
}



?>
