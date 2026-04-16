<?php
namespace App\Controllers;

use App\Models\Reservation;
use App\Models\Session;

class DashboardController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ── Dashboard PLAYER ──────────────────────
    public function playerDashboard(): void {

        $reservationModel = new Reservation($this->db);
        $reservations = $reservationModel->getByPhone('...');

        require_once __DIR__ . '/../../views/player/dashboard.php';
    }
}