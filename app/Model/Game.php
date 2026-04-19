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
            $sql = "INSERT INTO games (name_game, players_min, players_max, duration, difficulty, description_game, how_to_play, category_game, image_game)
                    VALUES (:name, :min, :max, :duration, :difficulty, :description, :howtoplay, :category, :image)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name'        => $data['name_game'],
                ':min'         => $data['players_min'],
                ':max'         => $data['players_max'],
                ':duration'    => $data['duration'],
                ':difficulty'  => $data['difficulty'],
                ':description' => $data['description_game'],
                ':howtoplay'   => $data['how_to_play'] ?? null,
                ':category'    => $data['category_game'],
                ':image'       => $data['image_game'] ?? null,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $imageSet = array_key_exists('image_game', $data) ? ', image_game = :image' : '';
            $sql = "UPDATE games SET name_game = :name, players_min = :min, players_max = :max,
                    duration = :duration, difficulty = :difficulty, description_game = :description,
                    how_to_play = :howtoplay, category_game = :category{$imageSet} WHERE id_game = :id";
            $params = [
                ':name'        => $data['name_game'],
                ':min'         => $data['players_min'],
                ':max'         => $data['players_max'],
                ':duration'    => $data['duration'],
                ':difficulty'  => $data['difficulty'],
                ':description' => $data['description_game'],
                ':howtoplay'   => $data['how_to_play'] ?? null,
                ':category'    => $data['category_game'],
                ':id'          => $id,
            ];
            if (array_key_exists('image_game', $data)) {
                $params[':image'] = $data['image_game'];
            }
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
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

    public function getRelated(int $currentId, string $category, int $limit = 3): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM games
                 WHERE category_game = :category AND id_game != :id AND status_game = 'available'
                 ORDER BY RAND()
                 LIMIT :lim"
            );
            $stmt->bindValue(':category', $category);
            $stmt->bindValue(':id', $currentId, \PDO::PARAM_INT);
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function search(array $filters): array
    {
        try {
            $wheres = [];
            $params = [];

            if (!empty($filters['q'])) {
                $wheres[] = "(name_game LIKE :q OR description_game LIKE :q2)";
                $params[':q']  = '%' . $filters['q'] . '%';
                $params[':q2'] = '%' . $filters['q'] . '%';
            }
            if (!empty($filters['category'])) {
                $wheres[] = "category_game = :category";
                $params[':category'] = $filters['category'];
            }
            if (!empty($filters['difficulty'])) {
                $wheres[] = "difficulty = :difficulty";
                $params[':difficulty'] = $filters['difficulty'];
            }
            if (!empty($filters['players'])) {
                $p = (int)$filters['players'];
                $wheres[] = "players_min <= :pmin AND players_max >= :pmax";
                $params[':pmin'] = $p;
                $params[':pmax'] = $p;
            }
            if (!empty($filters['status'])) {
                $wheres[] = "status_game = :status";
                $params[':status'] = $filters['status'];
            }

            $sql = "SELECT * FROM games";
            if ($wheres) {
                $sql .= " WHERE " . implode(' AND ', $wheres);
            }
            $sql .= " ORDER BY name_game ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getStats(): array
    {
        try {
            $available = count($this->getAvailable());

            // Most reserved game
            $popular = null;
            try {
                $row = $this->pdo->query(
                    "SELECT g.name_game, COUNT(*) AS cnt
                     FROM reservations r
                     JOIN games g ON r.id_game = g.id_game
                     WHERE r.status_reservation = 'confirmed'
                     GROUP BY r.id_game ORDER BY cnt DESC LIMIT 1"
                )->fetch(\PDO::FETCH_ASSOC);
                if ($row) $popular = $row;
            } catch (\PDOException $e) {}

            return ['available' => $available, 'popular' => $popular];
        } catch (\PDOException $e) {
            return ['available' => 0, 'popular' => null];
        }
    }

    /** Mark a game as available or in_use */
    public function setStatus(int $id, string $status): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE games SET status_game = :s WHERE id_game = :id");
            return $stmt->execute([':s' => $status, ':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /** Returns the game_id the given user is actively playing right now (or null) */
    public function getActiveGameIdForUser(int $userId): ?int
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT s.id_game FROM sessions s
                 JOIN reservations r ON s.id_reservation = r.id_reservation
                 WHERE r.id_user = :u AND s.status_session = 'active'
                 LIMIT 1"
            );
            $stmt->execute([':u' => $userId]);
            $row = $stmt->fetch();
            return $row ? (int)$row['id_game'] : null;
        } catch (\PDOException $e) {
            return null;
        }
    }
}