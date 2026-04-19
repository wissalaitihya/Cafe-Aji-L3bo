<?php

namespace App\Model;

use Core\Database;
class Reservation
{
    //  PDO instance (singleton from Database class)
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    //  1. getAll()

    public function getAll(): array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                r.status_reservation,
                u.id_user,
                u.name_user,
                u.phone_number,
                t.id_table,
                t.name_table,
                t.capacity,
                g.name_game
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            LEFT JOIN games  g ON r.id_game  = g.id_game
            ORDER BY r.reservation_date ASC, r.reservation_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //  2. getById($id)
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                r.status_reservation,
                u.id_user,
                u.name_user,
                u.phone_number,
                t.id_table,
                t.name_table,
                t.capacity,
                g.name_game
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            LEFT JOIN games  g ON r.id_game  = g.id_game
            WHERE r.id_reservation = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // fetch() returns false when nothing found — normalize to null
        return $result ?: null;
    }

    //  3. create($data)

    public function create(array $data): bool
    {
        $idGame  = !empty($data['id_game'])  ? (int)$data['id_game']  : null;
        $endTime = !empty($data['reservation_end_time']) ? $data['reservation_end_time'] : null;

        $sql = "
            INSERT INTO reservations
                (id_user, id_table, id_game, people_count, reservation_date, reservation_time, reservation_end_time, status_reservation)
            VALUES
                (:id_user, :id_table, :id_game, :people_count, :reservation_date, :reservation_time, :reservation_end_time, 'pending')
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id_user',               $data['id_user'],          \PDO::PARAM_INT);
        $stmt->bindParam(':id_table',              $data['id_table'],         \PDO::PARAM_INT);
        $stmt->bindParam(':id_game',               $idGame,                   $idGame   ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $stmt->bindParam(':people_count',          $data['people_count'],     \PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date',      $data['reservation_date'], \PDO::PARAM_STR);
        $stmt->bindParam(':reservation_time',      $data['reservation_time'], \PDO::PARAM_STR);
        $stmt->bindParam(':reservation_end_time',  $endTime,                  $endTime  ? \PDO::PARAM_STR : \PDO::PARAM_NULL);

        return $stmt->execute();
    }

    //  4. updateStatus($id, $status)

    public function updateStatus(int $id, string $status): bool
    {
        // Whitelist — never trust user input for ENUM values
        $allowed = ['pending', 'confirmed', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $sql = "
            UPDATE reservations
            SET    status_reservation = :status
            WHERE  id_reservation     = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->bindParam(':id',     $id,     \PDO::PARAM_INT);

        return $stmt->execute();
    }

    //  5. getByDate($date)
    public function getByDate(string $date): array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                r.status_reservation,
                u.id_user,
                u.name_user,
                u.phone_number,
                t.id_table,
                t.name_table,
                t.capacity,
                g.name_game
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            LEFT JOIN games  g ON r.id_game  = g.id_game
            WHERE r.reservation_date = :date
              AND r.status_reservation != 'cancelled'
            ORDER BY r.reservation_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':date', $date, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //  6. getByUserId($userId)
    public function getByUserId(int $userId): array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                r.status_reservation,
                t.id_table,
                t.name_table,
                t.capacity,
                g.name_game
            FROM reservations r
            LEFT JOIN tables t ON r.id_table = t.id_table
            LEFT JOIN games  g ON r.id_game  = g.id_game
            WHERE r.id_user = :user_id
            ORDER BY r.reservation_date DESC, r.reservation_time DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //  7. checkAvailability($tableId, $date, $time)
    public function checkAvailability(int $tableId, string $date, string $time): bool
    {
        $sql = "
            SELECT COUNT(*) AS conflict_count
            FROM   reservations r
            LEFT JOIN games g ON r.id_game = g.id_game
            WHERE  r.id_table            = :table_id
              AND  r.reservation_date    = :date
              AND  r.status_reservation != 'cancelled'
              AND  :time < ADDTIME(r.reservation_time, SEC_TO_TIME(COALESCE(g.duration, 120) * 60))
              AND  ADDTIME(:time2, SEC_TO_TIME(COALESCE(g.duration, 120) * 60)) > r.reservation_time
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':table_id', $tableId, \PDO::PARAM_INT);
        $stmt->bindParam(':date',     $date,    \PDO::PARAM_STR);
        $stmt->bindParam(':time',     $time,    \PDO::PARAM_STR);
        $stmt->bindParam(':time2',    $time,    \PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int) $row['conflict_count'] === 0;
    }

    public function isDuplicate(int $userId, int $tableId, string $date, string $time): bool
    {
        $sql = "
            SELECT COUNT(*) AS cnt
            FROM   reservations
            WHERE  id_user             = :user_id
              AND  id_table            = :table_id
              AND  reservation_date    = :date
              AND  reservation_time    = :time
              AND  status_reservation != 'cancelled'
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id',  $userId,  \PDO::PARAM_INT);
        $stmt->bindParam(':table_id', $tableId, \PDO::PARAM_INT);
        $stmt->bindParam(':date',     $date,    \PDO::PARAM_STR);
        $stmt->bindParam(':time',     $time,    \PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['cnt'] > 0;
    }

    /**
     * Returns true if the player already has a non-cancelled reservation
     * that is still in the future or currently in progress.
     * Used to enforce the one-reservation-at-a-time rule.
     */
    public function hasActiveReservation(int $userId): bool
    {
        try {
            $sql = "
                SELECT COUNT(*) AS cnt
                FROM reservations
                WHERE id_user = :user_id
                  AND status_reservation NOT IN ('cancelled')
                  AND (
                      reservation_date > CURDATE()
                      OR (reservation_date = CURDATE() AND reservation_end_time > CURTIME())
                  )
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Returns the active (non-cancelled, not-yet-ended) reservation
     * for a user — used to show them what they already have booked.
     */
    public function getActiveReservationForUser(int $userId): ?array
    {
        try {
            $sql = "
                SELECT r.*, t.name_table, g.name_game
                FROM reservations r
                LEFT JOIN tables t ON r.id_table = t.id_table
                LEFT JOIN games  g ON r.id_game  = g.id_game
                WHERE r.id_user = :user_id
                  AND r.status_reservation NOT IN ('cancelled')
                  AND (
                      r.reservation_date > CURDATE()
                      OR (r.reservation_date = CURDATE() AND r.reservation_end_time > CURTIME())
                  )
                ORDER BY r.reservation_date ASC, r.reservation_time ASC
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    //  BONUS HELPERS
    public function getTodayReservations(): array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                r.reservation_end_time,
                r.status_reservation,
                u.id_user,
                u.name_user,
                u.phone_number,
                t.id_table,
                t.name_table,
                t.capacity,
                g.name_game
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            LEFT JOIN games  g ON r.id_game  = g.id_game
            WHERE r.reservation_date = :date
              AND r.status_reservation = 'confirmed'
            ORDER BY r.reservation_time ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':date', date('Y-m-d'), \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPending(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM   reservations
            WHERE  status_reservation = 'pending'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int) $row['total'];
    }

    public function getConfirmed(): array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.id_game,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                r.reservation_end_time,
                u.name_user,
                u.phone_number,
                t.id_table,
                t.name_table,
                g.name_game
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            LEFT JOIN games  g ON r.id_game  = g.id_game
            WHERE r.status_reservation = 'confirmed'
              AND r.reservation_date   = CURDATE()
              AND CURTIME() BETWEEN r.reservation_time AND r.reservation_end_time
              AND NOT EXISTS (
                SELECT 1 FROM sessions s
                WHERE s.id_reservation = r.id_reservation
                  AND s.status_session = 'active'
              )
            ORDER BY r.reservation_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function countConfirmed(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM reservations WHERE status_reservation = 'confirmed'");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    public function countCancelled(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM reservations WHERE status_reservation = 'cancelled'");
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    public function getMonthStats(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(status_reservation = 'confirmed') AS confirmed,
                SUM(status_reservation = 'pending')   AS pending,
                SUM(status_reservation = 'cancelled') AS cancelled
            FROM reservations
            WHERE YEAR(reservation_date)  = YEAR(CURDATE())
              AND MONTH(reservation_date) = MONTH(CURDATE())
        ";
        $row = $this->pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
        return [
            'total'     => (int)($row['total']     ?? 0),
            'confirmed' => (int)($row['confirmed'] ?? 0),
            'pending'   => (int)($row['pending']   ?? 0),
            'cancelled' => (int)($row['cancelled'] ?? 0),
        ];
    }

    /** Peak hours: count confirmed reservations grouped by hour of day */
    public function getPeakHours(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT HOUR(reservation_time) AS hour, COUNT(*) AS cnt
                 FROM reservations
                 WHERE status_reservation = 'confirmed'
                 GROUP BY HOUR(reservation_time)
                 ORDER BY hour ASC"
            );
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // Fill all 24 hours
            $hours = array_fill(0, 24, 0);
            foreach ($rows as $r) {
                $hours[(int)$r['hour']] = (int)$r['cnt'];
            }
            return $hours;
        } catch (\PDOException $e) {
            return array_fill(0, 24, 0);
        }
    }

    /** Most booked games (confirmed reservations) */
    public function getMostBookedGames(int $limit = 8): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT g.name_game, g.category_game, COUNT(r.id_reservation) AS bookings
                 FROM reservations r
                 JOIN games g ON r.id_game = g.id_game
                 WHERE r.status_reservation = 'confirmed'
                 GROUP BY r.id_game, g.name_game, g.category_game
                 ORDER BY bookings DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /** Table occupation: ratio of confirmed reservation hours vs operating hours */
    public function getTableOccupationRates(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT t.name_table, t.capacity,
                        COUNT(r.id_reservation) AS total_bookings,
                        SUM(TIMESTAMPDIFF(MINUTE,
                            CONCAT(r.reservation_date,' ',r.reservation_time),
                            CONCAT(r.reservation_date,' ',r.reservation_end_time)
                        )) AS total_minutes
                 FROM tables t
                 LEFT JOIN reservations r ON r.id_table = t.id_table
                    AND r.status_reservation = 'confirmed'
                 GROUP BY t.id_table, t.name_table, t.capacity
                 ORDER BY total_bookings DESC"
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /** Last 7 days bookings per day */
    public function getLast7DaysStats(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT DATE(reservation_date) AS day,
                        COUNT(*) AS total,
                        SUM(status_reservation='confirmed') AS confirmed,
                        SUM(status_reservation='cancelled') AS cancelled
                 FROM reservations
                 WHERE reservation_date >= CURDATE() - INTERVAL 6 DAY
                 GROUP BY DATE(reservation_date)
                 ORDER BY day ASC"
            );
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // Ensure all 7 days present
            $result = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $result[$d] = ['day' => $d, 'total' => 0, 'confirmed' => 0, 'cancelled' => 0];
            }
            foreach ($rows as $r) {
                if (isset($result[$r['day']])) $result[$r['day']] = $r;
            }
            return array_values($result);
        } catch (\PDOException $e) {
            return [];
        }
    }
}