<?php

namespace App\Model;

use PDO;
use Core\Database;

class Game {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public static function all(): array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM games ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $game = $stmt->fetch();
        return $game ?: null;
    }
}