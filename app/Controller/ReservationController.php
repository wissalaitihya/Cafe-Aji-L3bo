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
        $sessionModel     = new \App\Model\Session();

        $reservations   = $reservationModel->getAll();
        $activeSessions = $sessionModel->getActive();

        // Build a map: id_reservation => session row
        $sessionsByReservation = [];
        foreach ($activeSessions as $s) {
            if (!empty($s['id_reservation'])) {
                $sessionsByReservation[(int)$s['id_reservation']] = $s;
            }
        }

        $this->render('reservation/index', [
            'reservations'          => $reservations,
            'sessionsByReservation' => $sessionsByReservation,
        ]);
    }

    // Show booking form
    public function create()
    {
        $this->requireLogin();

        // Non-admin players: block if they already have an active/upcoming reservation
        if (!$this->isAdmin()) {
            $reservationModel = new Reservation();
            $existing = $reservationModel->getActiveReservationForUser((int)$_SESSION['user_id']);
            if ($existing) {
                $tableModel = new Table();
                $gameModel  = new Game();
                $this->render('reservation/create', [
                    'tables'       => [],
                    'games'        => $gameModel->getAvailable(),
                    'prefill'      => [],
                    'blockBooking' => true,
                    'existingRes'  => $existing,
                ]);
                return;
            }
        }

        $date    = $_GET['date'] ?? '';
        $time    = $_GET['time'] ?? '';
        $endTime = $_GET['end_time'] ?? '';
        $tableId = $_GET['id_table'] ?? '';

        $tableModel = new Table();
        $gameModel  = new Game();

        if ($date && $time) {
            $et     = $endTime ?: date('H:i', strtotime($time) + 7200);
            $tables = $tableModel->getAvailableForSlot($date, $time, $et);
            $games  = $gameModel->getAvailableForSlot($date, $time);
        } else {
            $tables = $tableModel->getAll();
            $games  = $gameModel->getAvailable();
        }

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
                'end_time'     => $endTime,
                'people_count' => $peoplePrefill,
            ],
        ]);
    }

    // Save reservation
    public function store()
    {
        $this->requireLogin();

        $data = [
            'id_user'               => $_SESSION['user_id'],
            'id_table'              => (int)($_POST['id_table'] ?? 0),
            'id_game'               => (int)($_POST['id_game'] ?? 0),
            'people_count'          => (int)($_POST['people_count'] ?? 1),
            'reservation_date'      => $_POST['reservation_date'] ?? '',
            'reservation_time'      => $_POST['reservation_time'] ?? '',
            'reservation_end_time'  => $_POST['reservation_end_time'] ?? '',
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

        // Validate people count vs game min/max
        if (!empty($data['id_game'])) {
            $gameModel = new Game();
            $game = $gameModel->getById($data['id_game']);
            if ($game) {
                if ($data['people_count'] < $game['players_min']) {
                    $tableModel = new Table();
                    $this->render('reservation/create', [
                        'error'   => 'This game requires at least ' . $game['players_min'] . ' players. You entered ' . $data['people_count'] . '.',
                        'tables'  => $tableModel->getAll(),
                        'games'   => $gameModel->getAvailable(),
                        'prefill' => $prefill,
                    ]);
                    return;
                }
                if ($data['people_count'] > $game['players_max']) {
                    $tableModel = new Table();
                    $this->render('reservation/create', [
                        'error'   => 'This game supports max ' . $game['players_max'] . ' players. You entered ' . $data['people_count'] . '.',
                        'tables'  => $tableModel->getAll(),
                        'games'   => $gameModel->getAvailable(),
                        'prefill' => $prefill,
                    ]);
                    return;
                }
            }
        }

        // Validate people count vs table capacity
        $tableModel = new Table();
        $table = $tableModel->getById($data['id_table']);
        if ($table && $data['people_count'] > $table['capacity']) {
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => 'Table "' . $table['name_table'] . '" only seats ' . $table['capacity'] . ' people. Your group has ' . $data['people_count'] . '.',
                'tables'  => $tableModel->getAll(),
                'games'   => $gameModel->getAvailable(),
                'prefill' => $prefill,
            ]);
            return;
        }

        $reservationModel = new Reservation();

        // Non-admin: enforce one active reservation at a time
        if (!$this->isAdmin()) {
            if ($reservationModel->hasActiveReservation((int)$_SESSION['user_id'])) {
                $existing = $reservationModel->getActiveReservationForUser((int)$_SESSION['user_id']);
                $this->render('reservation/create', [
                    'tables'       => [],
                    'games'        => (new Game())->getAvailable(),
                    'prefill'      => [],
                    'blockBooking' => true,
                    'existingRes'  => $existing,
                ]);
                return;
            }
        }

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

        // Re-sync table statuses after any status change
        (new Table())->syncStatuses();

        $this->redirect('/reservations');
    }

    // Player: cancel their own pending or confirmed reservation
    public function cancelByPlayer($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservation = $reservationModel->getById((int)$id);

        // Security: only the owner can cancel
        if (!$reservation || (int)$reservation['id_user'] !== (int)$_SESSION['user_id']) {
            $this->redirect('/reservations/my');
            return;
        }

        $status = $reservation['status_reservation'];

        // Only pending or confirmed can be cancelled
        if (!in_array($status, ['pending', 'confirmed'], true)) {
            $this->redirect('/reservations/my');
            return;
        }

        // If confirmed, block cancellation if a session has already started on this reservation
        if ($status === 'confirmed') {
            $sessionModel = new \App\Model\Session();
            $activeSession = $sessionModel->getActiveByReservationId((int)$id);
            if ($activeSession) {
                // Session already started — cannot cancel
                $this->redirect('/reservations/my?error=session_started');
                return;
            }
        }

        $reservationModel->updateStatus((int)$id, 'cancelled');
        (new Table())->syncStatuses();

        $this->redirect('/reservations/my?cancelled=1');
    }

    // ── API: return available tables as JSON ──
    public function apiAvailableTables()
    {
        $this->requireLogin();

        header('Content-Type: application/json');

        $date    = $_GET['date'] ?? '';
        $time    = $_GET['time'] ?? '';
        $endTime = $_GET['end_time'] ?? '';

        if (empty($date) || empty($time)) {
            echo json_encode([]);
            return;
        }

        $tableModel = new Table();
        $gameModel  = new Game();

        // If a game_id is provided filter tables by that game's min/max capacity
        $gameId = (int)($_GET['game_id'] ?? 0);
        $minCap = null;
        $maxCap = null;
        if ($gameId > 0) {
            $game = $gameModel->getById($gameId);
            if ($game) {
                $minCap = (int)$game['players_min'];
                $maxCap = (int)$game['players_max'];
            }
        }

        $tables = $tableModel->getAvailableForSlot($date, $time, $endTime, $minCap, $maxCap);
        echo json_encode($tables);
    }
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