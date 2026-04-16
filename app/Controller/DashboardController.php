<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Reservation;
use App\Model\Session;
use App\Model\Table;

class DashboardController extends Controller
{
    public function admin(): void
    {
        $this->requireAdmin();

        $gameModel = new Game();
        $reservationModel = new Reservation();
        $sessionModel = new Session();
        $tableModel = new Table();

        $totalGames = count($gameModel->getAll());
        $todayReservations = $reservationModel->getTodayReservations();
        $allReservations = $reservationModel->getAll();
        $activeSessions = $sessionModel->getActive();
        $tables = $tableModel->getAll();

        $this->render('dashboard/admin', [
            'totalGames'        => $totalGames,
            'todayReservations' => $todayReservations,
            'allReservations'   => $allReservations,
            'activeSessions'    => $activeSessions,
            'tables'            => $tables,
        ]);
    }

    public function player(): void
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $myReservations = $reservationModel->getByUserId($_SESSION['user_id']);

        $this->render('dashboard/player', [
            'myReservations' => $myReservations,
            'userName'       => $_SESSION['user_name'] ?? 'Player',
        ]);
    }
}