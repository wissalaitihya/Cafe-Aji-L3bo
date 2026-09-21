<?php

namespace App\Model;

use Core\Database;
use PDO;

class Rating
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * game_id => ['avg' => float, 'total' => int]
     */
    public function getAllRatingsMap(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT id_game,
                        ROUND(AVG(stars), 2) AS avg,
                        COUNT(*)             AS total
                 FROM game_ratings
                 GROUP BY id_game"
            );
            $map = [];
            foreach ($stmt->fetchAll() as $row) {
                $map[(int)$row['id_game']] = [
                    'avg'   => (float)$row['avg'],
                    'total' => (int)$row['total'],
                ];
            }
            return $map;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Rating summary for a single game.
     */
    public function getSummary(int $gameId): array
    {
        $map = $this->getSummariesForGames([$gameId]);
        return $map[$gameId] ?? ['avg' => 0.0, 'total' => 0];
    }

    /**
     * Batched summaries for many games in ONE query (fixes N+1 in recommend).
     * @param int[] $gameIds
     * @return array<int, array{avg: float, total: int}>
     */
    public function getSummariesForGames(array $gameIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $gameIds)));
        $ids = array_values(array_filter($ids, fn($id) => $id > 0));
        if (!$ids) {
            return [];
        }
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->pdo->prepare(
                "SELECT id_game, ROUND(AVG(stars), 2) AS avg, COUNT(*) AS total
                 FROM game_ratings WHERE id_game IN ({$placeholders}) GROUP BY id_game"
            );
            $stmt->execute($ids);
            $map = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $map[(int) $row['id_game']] = [
                    'avg' => (float) ($row['avg'] ?? 0),
                    'total' => (int) ($row['total'] ?? 0),
                ];
            }
            return $map;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Most recent ratings for a game, including the rating author.
     */
    public function getForGame(int $gameId, int $limit = 5): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT r.stars, r.comment_rating, r.rated_at, u.name_user
                 FROM game_ratings r
                 JOIN users u ON r.id_user = u.id_user
                 WHERE r.id_game = :g
                 ORDER BY r.rated_at DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':g', $gameId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * A user's own rating for a game (or null if none yet).
     */
    public function getByUserAndGame(int $userId, int $gameId): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_rating, id_session, stars, comment_rating, rated_at
                 FROM game_ratings WHERE id_user = :u AND id_game = :g
                 LIMIT 1"
            );
            $stmt->execute([':u' => $userId, ':g' => $gameId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Whether the user has played the game (finished OR active session).
     * Active sessions are included so players can rate while playing.
     */
    public function hasPlayedGame(int $userId, int $gameId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT 1
                 FROM sessions s
                 JOIN reservations r ON s.id_reservation = r.id_reservation
                 WHERE r.id_user = :u AND s.id_game = :g
                   AND s.status_session IN ('active', 'finished')
                 LIMIT 1"
            );
            $stmt->execute([':u' => $userId, ':g' => $gameId]);
            return $stmt->fetch() ? true : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Whether the user is currently playing the game (active session).
     */
    public function hasActiveSession(int $userId, int $gameId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT 1
                 FROM sessions s
                 JOIN reservations r ON s.id_reservation = r.id_reservation
                 WHERE r.id_user = :u AND s.id_game = :g
                   AND s.status_session = 'active'
                 LIMIT 1"
            );
            $stmt->execute([':u' => $userId, ':g' => $gameId]);
            return $stmt->fetch() ? true : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Insert a rating, or update it if the user already rated this game
     * (unique key uq_user_game on id_user + id_game).
     */
    public function upsert(int $userId, int $gameId, float $stars, ?string $comment = null, ?int $sessionId = null): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO game_ratings (id_user, id_game, id_session, stars, comment_rating)
                 VALUES (:u, :g, :s, :stars, :comment)
                 ON DUPLICATE KEY UPDATE
                     stars = VALUES(stars),
                     comment_rating = VALUES(comment_rating),
                     id_session = VALUES(id_session),
                     rated_at = CURRENT_TIMESTAMP"
            );
            return $stmt->execute([
                ':u'       => $userId,
                ':g'       => $gameId,
                ':s'       => $sessionId,
                ':stars'   => $stars,
                ':comment' => $comment,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}