<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Reservation;
use App\Model\Table;

/**
 * GET /admin/stats — full statistics page (admin only).
 * Uses the existing Reservation/Table/Game stat queries (no new deps).
 */
class StatsController
{
    public function index()
    {
        $this->requireAdmin();

        $reservationModel = new Reservation();
        $gameModel        = new Game();
        $tableModel       = new Table();

        $pending   = $reservationModel->countPending();
        $confirmed = $reservationModel->countConfirmed();
        $cancelled = $reservationModel->countCancelled();

        $this->render('dashboard/stats', [
            'total'          => $pending + $confirmed + $cancelled,
            'pending'        => $pending,
            'confirmed'      => $confirmed,
            'cancelled'      => $cancelled,
            'monthStats'     => $reservationModel->getMonthStats(),
            'last7Days'      => $reservationModel->getLast7DaysStats(),
            'peakHours'      => $reservationModel->getPeakHours(),
            'mostBooked'     => $reservationModel->getMostBookedGames(8),
            'tableOccupancy' => $reservationModel->getTableOccupationRates(),
            'gameStats'      => $gameModel->getStats(),
            'tableStats'     => $tableModel->getStats(),
        ]);
    }

    private function render($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . "/../View/{$view}.php";
        if (!file_exists($viewPath)) {
            http_response_code(404);
            require __DIR__ . '/../View/error/404.php';
            return;
        }
        require $viewPath;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    private function requireAdmin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
        if (!$this->isAdmin()) {
            http_response_code(403);
            require __DIR__ . '/../View/error/403.php';
            exit;
        }
    }
}
