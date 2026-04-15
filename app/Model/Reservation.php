<?php

namespace app\Model;

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
                t.capacity
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
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
                t.capacity
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
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
        $sql = "
            INSERT INTO reservations
                (id_user, id_table, people_count, reservation_date, reservation_time, status_reservation)
            VALUES
                (:id_user, :id_table, :people_count, :reservation_date, :reservation_time, 'pending')
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id_user',          $data['id_user'],          \PDO::PARAM_INT);
        $stmt->bindParam(':id_table',         $data['id_table'],         \PDO::PARAM_INT);
        $stmt->bindParam(':people_count',     $data['people_count'],     \PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $data['reservation_date'], \PDO::PARAM_STR);
        $stmt->bindParam(':reservation_time', $data['reservation_time'], \PDO::PARAM_STR);

        return $stmt->execute();
    }


    // ══════════════════════════════════════════════════════════════
    //  4. updateStatus($id, $status)
    //  Changes pending → confirmed or cancelled
    //  Used by: ReservationController::updateStatus() (admin action)
    // ══════════════════════════════════════════════════════════════
    /**
     * Update the status of a reservation.
     *
     * @param  int    $id      id_reservation
     * @param  string $status  One of: 'pending' | 'confirmed' | 'cancelled'
     * @return bool            True on success
     */
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


    // ══════════════════════════════════════════════════════════════
    //  5. getByDate($date)
    //  Returns all reservations for a specific calendar day
    //  Used by: AdminController::dashboard() — today's list
    // ══════════════════════════════════════════════════════════════
    /**
     * Get all reservations for a given date.
     *
     * @param  string $date  Format: YYYY-MM-DD
     * @return array         Array of reservation rows
     */
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
                t.capacity
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            WHERE r.reservation_date = :date
            ORDER BY r.reservation_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':date', $date, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    // ══════════════════════════════════════════════════════════════
    //  6. getByUserId($userId)
    //  Returns all reservations belonging to ONE player
    //  Used by: ReservationController::myReservations()
    // ══════════════════════════════════════════════════════════════
    /**
     * Get all reservations for a specific user (player history).
     *
     * @param  int   $userId  id_user from session
     * @return array          Array of reservation rows, newest first
     */
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
                t.capacity
            FROM reservations r
            LEFT JOIN tables t ON r.id_table = t.id_table
            WHERE r.id_user = :user_id
            ORDER BY r.reservation_date DESC, r.reservation_time DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    // ══════════════════════════════════════════════════════════════
    //  7. checkAvailability($tableId, $date, $time)
    //  Checks if a table is free for a given date + time slot
    //  Used by: ReservationController::store() before INSERT
    //
    //  Logic:
    //    A table is "taken" if there is already a reservation on the
    //    same date, within 2 hours of the requested time, that is
    //    NOT cancelled.
    //    2-hour buffer = average session duration.
    // ══════════════════════════════════════════════════════════════
    /**
     * Check if a table is available for a given date and time slot.
     *
     * Returns TRUE  → table is free, reservation can proceed
     * Returns FALSE → table is already booked at that slot
     *
     * @param  int    $tableId  id_table
     * @param  string $date     Format: YYYY-MM-DD
     * @param  string $time     Format: HH:MM
     * @return bool
     */
    public function checkAvailability(int $tableId, string $date, string $time): bool
    {
        /*
         * A conflict exists when:
         *   - same table
         *   - same date
         *   - NOT cancelled
         *   - requested time falls within 2 hours of existing reservation
         *
         * TIMESTAMPDIFF returns the difference in minutes between
         * the existing reservation time and the requested time.
         * ABS() covers both directions (booking before or after).
         */
        $sql = "
            SELECT COUNT(*) AS conflict_count
            FROM   reservations
            WHERE  id_table            = :table_id
              AND  reservation_date    = :date
              AND  status_reservation != 'cancelled'
              AND  ABS(
                       TIMESTAMPDIFF(
                           MINUTE,
                           reservation_time,
                           :time
                       )
                   ) < 120
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':table_id', $tableId, \PDO::PARAM_INT);
        $stmt->bindParam(':date',     $date,    \PDO::PARAM_STR);
        $stmt->bindParam(':time',     $time,    \PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        // 0 conflicts → available (true), 1+ conflicts → taken (false)
        return (int) $row['conflict_count'] === 0;
    }


    // ══════════════════════════════════════════════════════════════
    //  BONUS HELPERS
    //  Small utilities used by multiple controllers
    // ══════════════════════════════════════════════════════════════

    /**
     * Get today's reservations (shortcut for dashboard).
     * Delegates to getByDate() with today's date.
     *
     * @return array
     */
    public function getTodayReservations(): array
    {
        return $this->getByDate(date('Y-m-d'));
    }

    /**
     * Count pending reservations (used in sidebar badge).
     *
     * @return int
     */
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

    /**
     * Get all confirmed reservations (used by SessionController
     * when starting a session — only confirmed ones can start).
     *
     * @return array
     */
    public function getConfirmed(): array
    {
        $sql = "
            SELECT
                r.id_reservation,
                r.people_count,
                r.reservation_date,
                r.reservation_time,
                u.name_user,
                u.phone_number,
                t.id_table,
                t.name_table
            FROM reservations r
            LEFT JOIN users  u ON r.id_user  = u.id_user
            LEFT JOIN tables t ON r.id_table = t.id_table
            WHERE r.status_reservation = 'confirmed'
            ORDER BY r.reservation_date ASC, r.reservation_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the last inserted reservation ID.
     * Useful after create() to redirect to confirmation page.
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}