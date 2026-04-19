<?php

namespace App\Model;

use Core\Database;
use PDO;

class Game
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM games ORDER BY name_game ASC");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAvailable(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM games WHERE status_game = 'available' ORDER BY name_game ASC");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAvailableForSlot(string $date, string $time): array
    {
        try {
            $sql = "
                SELECT * FROM games g
                WHERE g.status_game = 'available'
                  AND g.id_game NOT IN (
                      SELECT r.id_game FROM reservations r
                      LEFT JOIN games rg ON r.id_game = rg.id_game
                      WHERE r.id_game IS NOT NULL
                        AND r.reservation_date    = :date
                        AND r.status_reservation != 'cancelled'
                        AND :time < ADDTIME(r.reservation_time, SEC_TO_TIME(COALESCE(rg.duration, 120) * 60))
                        AND ADDTIME(:time2, SEC_TO_TIME(COALESCE(rg.duration, 120) * 60)) > r.reservation_time
                  )
                ORDER BY g.name_game ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':date' => $date, ':time' => $time, ':time2' => $time]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id_game = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO games (name_game, players_min, players_max, duration, difficulty, description_game, category_game)
                    VALUES (:name, :min, :max, :duration, :difficulty, :description, :category)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name'        => $data['name_game'],
                ':min'         => $data['players_min'],
                ':max'         => $data['players_max'],
                ':duration'    => $data['duration'],
                ':difficulty'  => $data['difficulty'],
                ':description' => $data['description_game'],
                ':category'    => $data['category_game'],
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE games SET name_game = :name, players_min = :min, players_max = :max,
                    duration = :duration, difficulty = :difficulty, description_game = :description,
                    category_game = :category WHERE id_game = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name'        => $data['name_game'],
                ':min'         => $data['players_min'],
                ':max'         => $data['players_max'],
                ':duration'    => $data['duration'],
                ':difficulty'  => $data['difficulty'],
                ':description' => $data['description_game'],
                ':category'    => $data['category_game'],
                ':id'          => $id,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM games WHERE id_game = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getByCategory(string $category): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM games WHERE category_game = :category ORDER BY name_game ASC");
            $stmt->execute([':category' => $category]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
}