<?php
namespace App\Controllers;

use App\Model\Reservation;
use App\Models\Session;

class DashboardController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ── Dashboard PLAYER ──────────────────────
    public function playerDashboard(): void {

        $reservationModel = new Reservation($this->db);
        $reservations = $reservationModel->getById('...');

        require_once __DIR__ . '/../../views/player/dashboard.php';
    }
}