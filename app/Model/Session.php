<?php
namespace App\Model;

use Core\Database;
use PDO;

class Session {

private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getActive() {
         try {
            $sql = "SELECT s.*, g.name_game, t.name_table, u.name_user,
                    TIMESTAMPDIFF(MINUTE, s.start_time, NOW()) AS elapsed_minutes
                    FROM sessions s
                    LEFT JOIN games g ON s.id_game = g.id_game
                    LEFT JOIN tables t ON s.id_table = t.id_table
                    LEFT JOIN reservations r ON s.id_reservation = r.id_reservation
                    LEFT JOIN users u ON r.id_user = u.id_user
                    WHERE s.status_session = 'active'
                    ORDER BY s.start_time ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById(int $id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE id_session = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function create(array $data){
        try {
            $sql = "INSERT INTO sessions (id_reservation, id_game, id_table, start_time, status_session)
                    VALUES (:reservation, :game, :table_id, NOW(), 'active')";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':reservation' => $data['id_reservation'] ?: null,
                ':game'        => $data['id_game'],
                ':table_id'    => $data['id_table'],
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    public function endSession(int $id){
        try {
            $sql = "UPDATE sessions SET end_time = NOW(), status_session = 'finished' WHERE id_session = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getHistory(){
        try {
            $sql = "SELECT s.*, g.name_game, t.name_table, u.name_user,
                    TIMESTAMPDIFF(MINUTE, s.start_time, s.end_time) AS duration_minutes
                    FROM sessions s
                    LEFT JOIN games g ON s.id_game = g.id_game
                    LEFT JOIN tables t ON s.id_table = t.id_table
                    LEFT JOIN reservations r ON s.id_reservation = r.id_reservation
                    LEFT JOIN users u ON r.id_user = u.id_user
                    WHERE s.status_session = 'finished'
                    ORDER BY s.end_time DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
    public function countActive(){
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM sessions WHERE status_session = 'active'");
            return (int) $stmt->fetch()['total'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
}
?>