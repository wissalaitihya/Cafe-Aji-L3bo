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

    public function getAvailableForSlot(string $date, string $time): array
    {
        try {
            $sql = "
                SELECT * FROM tables t
                WHERE t.id_table NOT IN (
                    SELECT r.id_table FROM reservations r
                    LEFT JOIN games g ON r.id_game = g.id_game
                    WHERE r.reservation_date    = :date
                      AND r.status_reservation != 'cancelled'
                      AND :time < ADDTIME(r.reservation_time, SEC_TO_TIME(COALESCE(g.duration, 120) * 60))
                      AND ADDTIME(:time2, SEC_TO_TIME(COALESCE(g.duration, 120) * 60)) > r.reservation_time
                )
                ORDER BY t.id_table ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':date' => $date, ':time' => $time, ':time2' => $time]);
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
}