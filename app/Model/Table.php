<?php

namespace App\Model;

use Core\Database;
use PDO;

class Table
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM tables ORDER BY id_table ASC");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAvailable(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM tables WHERE status_table = 'free' ORDER BY id_table ASC");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAvailableForSlot(string $date, string $startTime, string $endTime = '', ?int $minCapacity = null, ?int $maxCapacity = null): array
    {
        try {
            if (empty($endTime)) {
                $endTime = date('H:i:s', strtotime($startTime) + 7200);
            }
            $capacityFilter = '';
            if ($minCapacity !== null) {
                $capacityFilter .= ' AND t.capacity >= :min_cap';
            }
            if ($maxCapacity !== null) {
                $capacityFilter .= ' AND t.capacity <= :max_cap';
            }
            $sql = "
                SELECT t.*
                FROM tables t
                WHERE t.id_table NOT IN (
                    SELECT r.id_table FROM reservations r
                    WHERE r.reservation_date    = :date
                      AND r.status_reservation != 'cancelled'
                      AND :start_time < r.reservation_end_time
                      AND :end_time   > r.reservation_time
                ){$capacityFilter}
                ORDER BY t.capacity ASC, t.id_table ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $params = [
                ':date'       => $date,
                ':start_time' => $startTime,
                ':end_time'   => $endTime,
            ];
            if ($minCapacity !== null) $params[':min_cap'] = $minCapacity;
            if ($maxCapacity !== null) $params[':max_cap'] = $maxCapacity;
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function setStatus(int $id, string $status): bool
    {
        try {
            $allowed = ['free', 'occupied'];
            if (!in_array($status, $allowed, true)) {
                return false;
            }
            $stmt = $this->pdo->prepare("UPDATE tables SET status_table = :status WHERE id_table = :id");
            return $stmt->execute([':status' => $status, ':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tables WHERE id_table = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Auto-sync status_table for all tables based on:
     *  - 'occupied' if there is an active session on the table, OR
     *    a confirmed reservation that is in progress right now today.
     *  - 'free' for everything else.
     * Call this whenever the state may have changed.
     */
    public function syncStatuses(): void
    {
        try {
            // 1. Occupied: active session exists for table
            $this->pdo->exec("
                UPDATE tables SET status_table = 'occupied'
                WHERE id_table IN (
                    SELECT id_table FROM sessions WHERE status_session = 'active'
                )
            ");
            // 2. Occupied: confirmed reservation happening right now
            $this->pdo->exec("
                UPDATE tables SET status_table = 'occupied'
                WHERE id_table IN (
                    SELECT id_table FROM reservations
                    WHERE status_reservation = 'confirmed'
                      AND reservation_date = CURDATE()
                      AND CURTIME() BETWEEN reservation_time AND reservation_end_time
                )
            ");
            // 3. Free: no active session AND no current reservation
            $this->pdo->exec("
                UPDATE tables SET status_table = 'free'
                WHERE id_table NOT IN (
                    SELECT id_table FROM sessions WHERE status_session = 'active'
                )
                AND id_table NOT IN (
                    SELECT id_table FROM reservations
                    WHERE status_reservation = 'confirmed'
                      AND reservation_date = CURDATE()
                      AND CURTIME() BETWEEN reservation_time AND reservation_end_time
                )
            ");
        } catch (\PDOException $e) {
            // fail silently — status_table is visual only
        }
    }

    public function create(array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tables (name_table, capacity, status_table) VALUES (:name, :capacity, :status)"
            );
            return $stmt->execute([
                ':name'     => $data['name_table'],
                ':capacity' => $data['capacity'],
                ':status'   => $data['status_table'] ?? 'free',
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE tables SET name_table = :name, capacity = :capacity, status_table = :status WHERE id_table = :id"
            );
            return $stmt->execute([
                ':name'     => $data['name_table'],
                ':capacity' => $data['capacity'],
                ':status'   => $data['status_table'] ?? 'free',
                ':id'       => $id,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tables WHERE id_table = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getStats(): array
    {
        try {
            $all      = $this->getAll();
            $total    = count($all);
            $occupied = count(array_filter($all, fn($t) => $t['status_table'] === 'occupied'));
            $free     = $total - $occupied;

            // Most used table by confirmed reservations
            $mostUsed = null;
            try {
                $row = $this->pdo->query(
                    "SELECT t.name_table, COUNT(*) AS cnt
                     FROM reservations r
                     JOIN tables t ON r.id_table = t.id_table
                     WHERE r.status_reservation = 'confirmed'
                     GROUP BY r.id_table ORDER BY cnt DESC LIMIT 1"
                )->fetch(\PDO::FETCH_ASSOC);
                if ($row) $mostUsed = $row;
            } catch (\PDOException $e) {}

            return ['total' => $total, 'occupied' => $occupied, 'free' => $free, 'mostUsed' => $mostUsed];
        } catch (\PDOException $e) {
            return ['total' => 0, 'occupied' => 0, 'free' => 0, 'mostUsed' => null];
        }
    }
}