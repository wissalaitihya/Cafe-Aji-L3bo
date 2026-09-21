<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Reservation;
use App\Model\Session;
use App\Model\Table;


class DashboardController
{
    public function admin()
    {
        $this->requireAdmin();

        $gameModel        = new Game();
        $reservationModel = new Reservation();
        $sessionModel     = new Session();
        $tableModel       = new Table();

        // Single cheap queries — no full-table fetches + PHP counting.
        $gameStats         = $gameModel->getStats();
        $totalGames        = $gameModel->countAll();
        $todayReservations = $reservationModel->getTodayReservations();
        $activeSessions    = $sessionModel->getActive();
        $tables            = $tableModel->getAll();
        $tableStats        = $tableModel->getStats();
        $monthStats        = $reservationModel->getMonthStats();
        $pendingCount      = $reservationModel->countPending();
        $confirmedCount    = $reservationModel->countConfirmed();
        $cancelledCount    = $reservationModel->countCancelled();
        // Recent reservations for the table (bounded) instead of unbounded getAll().
        $allReservations   = $reservationModel->getAll(50);

        // Games currently in use: bounded query instead of filtering full getAll() twice.
        $inUseGames = $gameModel->search(['status' => 'in_use'], 12);

        $this->render('dashboard/admin', [
            'totalGames'        => $totalGames,
            'todayReservations' => $todayReservations,
            'allReservations'   => $allReservations,
            'activeSessions'    => $activeSessions,
            'tables'            => $tables,
            'gameStats'         => $gameStats,
            'tableStats'        => $tableStats,
            'monthStats'        => $monthStats,
            'pendingCount'      => $pendingCount,
            'confirmedCount'    => $confirmedCount,
            'cancelledCount'    => $cancelledCount,
            'inUseGames'        => $inUseGames,
        ]);
    }

    public function player()
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $gameModel        = new Game();

        $myReservations = $reservationModel->getByUserId((int)$_SESSION['user_id'], 50);
        $featuredGames  = $gameModel->getAvailable(4);

        $this->render('dashboard/player', [
            'myReservations' => $myReservations,
            'userName'       => \Core\Sanitizer::str($_SESSION['user_name'] ?? 'Player', 40),
            'featuredGames'  => $featuredGames,
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

    private function redirect($url)
    {
        header("Location: " . BASE_PATH . $url);
        exit;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    private function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    private function requireAdmin()
    {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            http_response_code(403);
            require __DIR__ . '/../View/error/403.php';
            exit;
        }
    }
}