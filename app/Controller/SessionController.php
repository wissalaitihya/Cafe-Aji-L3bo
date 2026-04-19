<?php

namespace App\Controller;

use App\Model\Session;
use App\Model\Game;
use App\Model\Table;
use App\Model\Reservation;

class SessionController
{
    // Active sessions dashboard
    public function dashboard()
    {
        $this->requireAdmin();

        $sessionModel = new Session();
        $tableModel   = new Table();
        $sessionModel->autoEndOverdue();
        $tableModel->syncStatuses();
        $sessions = $sessionModel->getActive();

        $this->render('Session/dashboard', ['sessions' => $sessions]);
    }

    // Show create session form
    public function create()
    {
        $this->requireAdmin();

        $gameModel = new Game();
        $tableModel = new Table();
        $reservationModel = new Reservation();

        $games = $gameModel->getAll();
        $tables = $tableModel->getAll();
        $reservations = $reservationModel->getConfirmed();

        // Build map: id_reservation => [game_id, ...] so the view can filter game select
        $gamesByReservation = [];
        foreach ($reservations as $r) {
            if (!empty($r['id_game'])) {
                $gamesByReservation[(int)$r['id_reservation']] = [(int)$r['id_game']];
            }
        }

        $this->render('Session/create', [
            'games'              => $games,
            'tables'             => $tables,
            'reservations'       => $reservations,
            'gamesByReservation' => $gamesByReservation,
        ]);
    }
    public function store()
    {
        $this->requireAdmin();

        $data = [
            'id_reservation' => !empty($_POST['id_reservation']) ? (int)$_POST['id_reservation'] : null,
            'id_game'        => (int)($_POST['id_game'] ?? 0),
            'id_table'       => (int)($_POST['id_table'] ?? 0),
        ];

        if (empty($data['id_game']) || empty($data['id_table'])) {
            $this->redirect('/sessions/create');
            return;
        }

        $sessionModel = new Session();
        $tableModel = new Table();

        if ($sessionModel->create($data)) {
            // Mark table as occupied + game as in_use
            $tableModel->setStatus($data['id_table'], 'occupied');
            if (!empty($data['id_game'])) {
                (new Game())->setStatus($data['id_game'], 'in_use');
            }
            $this->redirect('/sessions');
        } else {
            $this->redirect('/sessions/create');
        }
    }

    public function end($id)
    {
        $this->requireAdmin();

        $sessionModel = new Session();
        $session = $sessionModel->getById($id);

        if ($session) {
            $sessionModel->endSession($id);
            // Free the table + mark game available
            $tableModel = new Table();
            $tableModel->setStatus($session['id_table'], 'free');
            if (!empty($session['id_game'])) {
                (new Game())->setStatus($session['id_game'], 'available');
            }
        }

        $this->redirect('/sessions');
    }

     public function history()
    {
        $this->requireAdmin();

        $sessionModel = new Session();
        $sessions = $sessionModel->getHistory();

        $this->render('Session/history', ['sessions' => $sessions]);
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

?>