<?php

namespace App\Controller;

use App\Model\Reservation;
use App\Model\Table;
use App\Model\Game;

class ReservationController
{
    // Admin: all reservations
    public function index()
    {
        $this->requireAdmin();

        $reservationModel = new Reservation();
        $reservations = $reservationModel->getAll();

        $this->render('reservation/index', ['reservations' => $reservations]);
    }

    // Show booking form
    public function create()
    {
        $this->requireLogin();

        $date    = $_GET['date'] ?? '';
        $time    = $_GET['time'] ?? '';
        $tableId = $_GET['id_table'] ?? '';

        $tableModel = new Table();
        $gameModel = new Game();

        // Show only available tables when date+time are known
        if ($date && $time) {
            $tables = $tableModel->getAvailableForSlot($date, $time);
            $games  = $gameModel->getAvailableForSlot($date, $time);
        } else {
            $tables = $tableModel->getAll();
            $games  = $gameModel->getAvailable();
        }

        // Find capacity of selected table for people_count default
        $peoplePrefill = '';
        if ($tableId) {
            foreach ($tables as $t) {
                if ((int)$t['id_table'] === (int)$tableId) {
                    $peoplePrefill = $t['capacity'];
                    break;
                }
            }
        }

        $this->render('reservation/create', [
            'tables'   => $tables,
            'games'    => $games,
            'prefill'  => [
                'id_table'     => $tableId,
                'date'         => $date,
                'time'         => $time,
                'people_count' => $peoplePrefill,
            ],
        ]);
    }

    // Save reservation
    public function store()
    {
        $this->requireLogin();

        $data = [
            'id_user'          => $_SESSION['user_id'],
            'id_table'         => (int)($_POST['id_table'] ?? 0),
            'id_game'          => (int)($_POST['id_game'] ?? 0),
            'people_count'     => (int)($_POST['people_count'] ?? 1),
            'reservation_date' => $_POST['reservation_date'] ?? '',
            'reservation_time' => $_POST['reservation_time'] ?? '',
        ];

        $prefill = [
            'id_table'     => $data['id_table'],
            'date'         => $data['reservation_date'],
            'time'         => $data['reservation_time'],
            'people_count' => $data['people_count'],
        ];

        if (empty($data['id_table']) || empty($data['reservation_date']) || empty($data['reservation_time'])) {
            $tableModel = new Table();
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => 'Please fill all fields',
                'tables'  => $tableModel->getAll(),
                'games'   => $gameModel->getAvailable(),
                'prefill' => $prefill,
            ]);
            return;
        }

        $reservationModel = new Reservation();

        // Prevent duplicate submission
        if ($reservationModel->isDuplicate($data['id_user'], $data['id_table'], $data['reservation_date'], $data['reservation_time'])) {
            $this->redirect('/reservations/my');
            return;
        }

        // Check availability
        if (!$reservationModel->checkAvailability($data['id_table'], $data['reservation_date'], $data['reservation_time'])) {
            $tableModel = new Table();
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => 'Table not available at that time',
                'tables'  => $tableModel->getAll(),
                'games'   => $gameModel->getAvailableForSlot($data['reservation_date'], $data['reservation_time']),
                'prefill' => $prefill,
            ]);
            return;
        }

        if ($reservationModel->create($data)) {
            if ($this->isAdmin()) {
                $this->redirect('/reservations');
            } else {
                $this->redirect('/reservations/my');
            }
        } else {
            $tableModel = new Table();
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => 'Failed to create reservation',
                'tables'  => $tableModel->getAll(),
                'games'   => $gameModel->getAvailableForSlot($data['reservation_date'], $data['reservation_time']),
                'prefill' => $prefill,
            ]);
        }
    }

    // Player: my reservations
    public function myReservations()
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservations = $reservationModel->getByUserId($_SESSION['user_id']);

        $this->render('reservation/myreservations', ['reservations' => $reservations]);
    }

    // Check availability page
    public function availability()
    {
        $this->requireLogin();

        $date = $_GET['date'] ?? date('Y-m-d');
        $time = $_GET['time'] ?? '';

        $available = [];
        if (!empty($time)) {
            $tableModel = new Table();
            $available = $tableModel->getAvailableForSlot($date, $time);
        }

        $this->render('reservation/availability', [
            'tables'    => $available,
            'date'      => $date,
            'time'      => $time,
            'searched'  => !empty($time),
        ]);
    }

    // Admin: confirm/cancel
    public function updateStatus($id)
    {
        $this->requireAdmin();

        $status = $_POST['status'] ?? '';
        $reservationModel = new Reservation();
        $reservationModel->updateStatus($id, $status);

        $this->redirect('/reservations');
    }

    // ── API: return available tables as JSON ──
    public function apiAvailableTables()
    {
        $this->requireLogin();

        header('Content-Type: application/json');

        $date = $_GET['date'] ?? '';
        $time = $_GET['time'] ?? '';

        if (empty($date) || empty($time)) {
            echo json_encode([]);
            return;
        }

        $tableModel = new Table();
        $tables = $tableModel->getAvailableForSlot($date, $time);
        echo json_encode($tables);
    }

    // ── API: return available games as JSON ──
    public function apiAvailableGames()
    {
        $this->requireLogin();

        header('Content-Type: application/json');

        $date = $_GET['date'] ?? '';
        $time = $_GET['time'] ?? '';

        if (empty($date) || empty($time)) {
            echo json_encode([]);
            return;
        }

        $gameModel = new Game();
        $games = $gameModel->getAvailableForSlot($date, $time);
        echo json_encode($games);
    }

    // HELPER METHODS
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