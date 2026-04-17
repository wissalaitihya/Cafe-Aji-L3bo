<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Reservation;
use App\Model\Session;

class DashboardController
{
    public function admin()
    {
        $this->requireAdmin();

        $gameModel = new Game();
        $reservationModel = new Reservation();
        $sessionModel = new Session();

        $totalGames = count($gameModel->getAll());
        $todayReservations = $reservationModel->getTodayReservations();
        $allReservations = $reservationModel->getAll();
        $activeSessions = $sessionModel->getActive();

        $this->render('dashboard/admin', [
            'totalGames'        => $totalGames,
            'todayReservations' => $todayReservations,
            'allReservations'   => $allReservations,
            'activeSessions'    => $activeSessions,
        ]);
    }

    public function player()
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $myReservations = $reservationModel->getByUserId($_SESSION['user_id']);

        $this->render('dashboard/player', [
            'myReservations' => $myReservations,
            'userName'       => $_SESSION['user_name'] ?? 'Player',
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
        header("Location: /Cafe-Aji-L3bo" . $url);
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