<?php

namespace App\Model;

use Core\Database;
use PDO;

class Game
{
    private PDO $pdo;

    /** Card-list columns only (no TEXT blobs) — keeps catalogue fast. */
    private const CARD_COLUMNS = 'id_game, name_game, players_min, players_max, duration, difficulty, image_game, status_game, category_game';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(int $limit = 100, int $offset = 0): array
    {
        try {
            $limit = max(1, min($limit, 100));
            $offset = max(0, $offset);
            $stmt = $this->pdo->prepare("SELECT " . self::CARD_COLUMNS . " FROM games ORDER BY name_game ASC LIMIT :lim OFFSET :off");
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function countAll(array $filters = []): int
    {
        try {
            [$where, $params] = $this->buildSearchWhere($filters);
            $sql = "SELECT COUNT(*) FROM games" . ($where ? " WHERE {$where}" : "");
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function getAvailable(int $limit = 100, int $offset = 0): array
    {
        try {
            $limit = max(1, min($limit, 100));
            $offset = max(0, $offset);
            $stmt = $this->pdo->prepare("SELECT " . self::CARD_COLUMNS . " FROM games WHERE status_game = 'available' ORDER BY name_game ASC LIMIT :lim OFFSET :off");
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAvailableForSlot(string $date, string $time, int $limit = 100): array
    {
        try {
            $limit = max(1, min($limit, 100));
            $sql = "
                SELECT " . self::CARD_COLUMNS . " FROM games g
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
                LIMIT :lim
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':date', $date);
            $stmt->bindValue(':time', $time);
            $stmt->bindValue(':time2', $time);
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
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
            // Avoid ORDER BY RAND() full-sort: fetch recent candidates, shuffle in PHP.
            $stmt = $this->pdo->prepare(
                "SELECT " . self::CARD_COLUMNS . " FROM games
                 WHERE category_game = :category AND id_game != :id AND status_game = 'available'
                 ORDER BY id_game DESC
                 LIMIT 20"
            );
            $stmt->bindValue(':category', $category);
            $stmt->bindValue(':id', $currentId, \PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            if (count($rows) > $limit) {
                shuffle($rows);
                $rows = array_slice($rows, 0, $limit);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /** Shared WHERE builder so search() + countAll() stay in sync (all values bound). */
    private function buildSearchWhere(array $filters): array
    {
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
        return [$wheres ? implode(' AND ', $wheres) : '', $params];
    }

    public function search(array $filters, int $limit = 48, int $offset = 0): array
    {
        try {
            $limit = max(1, min($limit, 100));
            $offset = max(0, $offset);
            [$where, $params] = $this->buildSearchWhere($filters);

            $sql = "SELECT " . self::CARD_COLUMNS . " FROM games";
            if ($where) {
                $sql .= " WHERE " . $where;
            }
            $sql .= " ORDER BY name_game ASC LIMIT :lim OFFSET :off";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    /** Lightweight autocomplete: id + name only, capped at 6. */
    public function searchNames(string $q, int $limit = 6): array
    {
        try {
            $q = trim(mb_substr($q, 0, 100));
            if (mb_strlen($q) < 2) {
                return [];
            }
            $stmt = $this->pdo->prepare(
                "SELECT id_game, name_game FROM games WHERE name_game LIKE :q ORDER BY name_game ASC LIMIT :lim"
            );
            $stmt->bindValue(':q', '%' . $q . '%');
            $stmt->bindValue(':lim', max(1, min($limit, 10)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getStats(): array
    {
        try {
            $available = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM games WHERE status_game = 'available'"
            )->fetchColumn();

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