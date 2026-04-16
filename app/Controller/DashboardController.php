<?php

namespace App\Controller;

use App\Model\Reservation; 
use App\Model\Session;

class DashboardController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Dashboard PLAYER
     * Objectif : US7 (Voir mes réservations)
     */
    public function playerDashboard(): void {
        // 1.  le Model
        $reservationModel = new Reservation($this->db);
        
        
        $userId = $_SESSION['user_id'] ?? 1; 
        $reservations = $reservationModel->getById($userId);

    
        require_once __DIR__ . '/../Views/dashboard/player.php';
    }

  
    public function adminDashboard(): void {
        // $sessionModel = new Session($this->db);
        $reservationModel = new Reservation($this->db);

        // $activeSessions = $sessionModel->getActiveSessions(); // US10
        $allReservations = $reservationModel->getAll(); // US8

        require_once __DIR__ . '/../Views/dashboard/admin.php';
    }
}