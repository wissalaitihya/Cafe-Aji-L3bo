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

        $this->render('Session/create', [
            'games'        => $games,
            'tables'       => $tables,
            'reservations' => $reservations,
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
            // Mark table as occupied
            $tableModel->setStatus($data['id_table'], 'occupied');
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
            // Free the table
            $tableModel = new Table();
            $tableModel->setStatus($session['id_table'], 'free');
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