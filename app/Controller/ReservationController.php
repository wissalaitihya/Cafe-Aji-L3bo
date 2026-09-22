<?php

namespace App\Controller;

use App\Model\Reservation;
use App\Model\Table;
use App\Model\Game;
use Core\Csrf;
use Core\Sanitizer;
use Core\Validator;

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

        $date    = Sanitizer::str($_GET['date'] ?? '', 10);
        $time    = Sanitizer::str($_GET['time'] ?? '', 8);
        $endTime = Sanitizer::str($_GET['end_time'] ?? '', 8);
        $tableId = Sanitizer::int($_GET['id_table'] ?? 0, 0);

        $tableModel = new Table();
        $gameModel  = new Game();

        if ($date && $time) {
            // Validate before hitting DB; fall back to full lists on bad input.
            $slotError = Validator::slot($date, $time, $endTime ?: date('H:i', strtotime($time) + 7200));
            if ($slotError === null) {
                $et     = $endTime ?: date('H:i', strtotime($time) + 7200);
                $tables = $tableModel->getAvailableForSlot($date, $time, $et);
                $games  = $gameModel->getAvailableForSlot($date, $time);
            } else {
                $tables = $tableModel->getAll();
                $games  = $gameModel->getAvailable();
            }
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
        if (($csrfError = Csrf::requireValid()) !== null) {
            $tableModel = new Table();
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => $csrfError,
                'tables'  => $tableModel->getAll(),
                'games'   => $gameModel->getAvailable(),
                'prefill' => [],
            ]);
            return;
        }

        $data = [
            'id_user'               => (int)$_SESSION['user_id'],
            'id_table'              => Sanitizer::int($_POST['id_table'] ?? 0, 0),
            'id_game'               => Sanitizer::int($_POST['id_game'] ?? 0, 0),
            'people_count'          => Sanitizer::int($_POST['people_count'] ?? 1, 1),
            'reservation_date'      => Sanitizer::str($_POST['reservation_date'] ?? '', 10),
            'reservation_time'      => Sanitizer::str($_POST['reservation_time'] ?? '', 8),
            'reservation_end_time'  => Sanitizer::str($_POST['reservation_end_time'] ?? '', 8),
        ];

        $prefill = [
            'id_table'     => $data['id_table'],
            'date'         => $data['reservation_date'],
            'time'         => $data['reservation_time'],
            'end_time'     => $data['reservation_end_time'],
            'people_count' => $data['people_count'],
        ];

        if (empty($data['id_table']) || empty($data['reservation_date']) || empty($data['reservation_time']) || empty($data['reservation_end_time'])) {
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

        // Backend parity with frontend: table/date/time formats + past-date guard.
        if (($e = Validator::intRange($data['id_table'], 1, 1000000, 'table')) !== null
            || ($e = Validator::intRange($data['people_count'], 1, 30, 'people')) !== null
            || ($e = Validator::slot($data['reservation_date'], $data['reservation_time'], $data['reservation_end_time'])) !== null) {
            $tableModel = new Table();
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => $e,
                'tables'  => $tableModel->getAll(),
                'games'   => $gameModel->getAvailable(),
                'prefill' => $prefill,
            ]);
            return;
        }
        if ($data['id_game'] !== 0 && Validator::intRange($data['id_game'], 1, 1000000, 'game') !== null) {
            $data['id_game'] = 0;
        }

        // Validate end time: must be after start, min 30 minutes
        $startMins = strtotime($data['reservation_time']);
        $endMins   = strtotime($data['reservation_end_time']);
        if ($startMins === false || $endMins === false || ($endMins - $startMins) < 1800) {
            $tableModel = new Table();
            $gameModel = new Game();
            $this->render('reservation/create', [
                'error'   => 'End time must be at least 30 minutes after the start time.',
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
        $reservations = $reservationModel->getByUserId((int)$_SESSION['user_id']);

        // Map active sessions so the view can hide Edit on live bookings
        // (same pattern as the admin list).
        $sessionsByReservation = [];
        foreach ((new \App\Model\Session())->getActive() as $s) {
            if (!empty($s['id_reservation'])) {
                $sessionsByReservation[(int)$s['id_reservation']] = $s;
            }
        }

        $this->render('reservation/myreservations', [
            'reservations'          => $reservations,
            'sessionsByReservation' => $sessionsByReservation,
        ]);
    }

    // Show edit form (owner or admin; pending bookings only)
    public function edit($id)
    {
        $this->requireLogin();
        $id = (int) $id;

        $reservationModel = new Reservation();
        $reservation = $reservationModel->getById($id);
        if (!$reservation) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }
        // Admins confirm/cancel only — modification is the player's own action.
        if ($this->isAdmin()) {
            $this->redirect('/reservations?error=edit_admin');
            return;
        }
        if ((int)($reservation['id_user'] ?? 0) !== (int)$_SESSION['user_id']) {
            $this->redirect('/reservations/my');
            return;
        }
        if (($blockCode = $this->editBlockCode($reservation)) !== null) {
            $this->redirect('/reservations/my?error=' . $blockCode);
            return;
        }

        $tableModel = new Table();
        $gameModel  = new Game();
        $tables = $tableModel->getAll();
        $games  = $gameModel->getAvailable();
        // Keep the currently booked game selectable even if flagged in_use.
        if (!empty($reservation['id_game'])) {
            $found = false;
            foreach ($games as $g) {
                if ((int)$g['id_game'] === (int)$reservation['id_game']) { $found = true; break; }
            }
            if (!$found) {
                $current = $gameModel->getById((int)$reservation['id_game']);
                if ($current) { $games[] = $current; }
            }
        }

        $this->render('reservation/edit', [
            'reservation' => $reservation,
            'form'        => [
                'id_table'     => (int)($reservation['id_table'] ?? 0),
                'id_game'      => (int)($reservation['id_game'] ?? 0),
                'people_count' => (int)($reservation['people_count'] ?? 1),
                'date'         => $reservation['reservation_date'] ?? '',
                'time'         => substr($reservation['reservation_time'] ?? '', 0, 5),
                'end_time'     => substr($reservation['reservation_end_time'] ?? '', 0, 5),
            ],
            'tables' => $tables,
            'games'  => $games,
        ]);
    }

    // Save modification (same validation as booking, availability re-checked
    // excluding the reservation itself; status is never changed here)
    public function update($id)
    {
        $this->requireLogin();
        $id = (int) $id;

        $reservationModel = new Reservation();
        $reservation = $reservationModel->getById($id);
        if (!$reservation) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }
        // Admins confirm/cancel only — modification is the player's own action.
        if ($this->isAdmin()) {
            $this->redirect('/reservations?error=edit_admin');
            return;
        }
        if ((int)($reservation['id_user'] ?? 0) !== (int)$_SESSION['user_id']) {
            $this->redirect('/reservations/my');
            return;
        }
        if (($blockCode = $this->editBlockCode($reservation)) !== null) {
            $this->redirect('/reservations/my?error=' . $blockCode);
            return;
        }

        $tableModel = new Table();
        $gameModel  = new Game();
        $fail = function (string $error, array $form) use ($tableModel, $gameModel, $reservation, $id) {
            $tables = $tableModel->getAll();
            $games  = $gameModel->getAvailable();
            if (!empty($reservation['id_game'])) {
                $found = false;
                foreach ($games as $g) {
                    if ((int)$g['id_game'] === (int)$reservation['id_game']) { $found = true; break; }
                }
                if (!$found) {
                    $current = $gameModel->getById((int)$reservation['id_game']);
                    if ($current) { $games[] = $current; }
                }
            }
            $this->render('reservation/edit', [
                'error'       => $error,
                'reservation' => $reservation,
                'form'        => $form,
                'tables'      => $tables,
                'games'       => $games,
            ]);
        };

        if (($csrfError = Csrf::requireValid()) !== null) {
            $this->render('reservation/edit', [
                'error'       => $csrfError,
                'reservation' => $reservation,
                'form'        => $this->formFromReservation($reservation),
                'tables'      => $tableModel->getAll(),
                'games'       => $gameModel->getAvailable(),
            ]);
            return;
        }

        $data = [
            'id_table'              => Sanitizer::int($_POST['id_table'] ?? 0, 0),
            'id_game'               => Sanitizer::int($_POST['id_game'] ?? 0, 0),
            'people_count'          => Sanitizer::int($_POST['people_count'] ?? 1, 1),
            'reservation_date'      => Sanitizer::str($_POST['reservation_date'] ?? '', 10),
            'reservation_time'      => Sanitizer::str($_POST['reservation_time'] ?? '', 8),
            'reservation_end_time'  => Sanitizer::str($_POST['reservation_end_time'] ?? '', 8),
        ];
        $form = [
            'id_table'     => $data['id_table'],
            'id_game'      => $data['id_game'],
            'people_count' => $data['people_count'],
            'date'         => $data['reservation_date'],
            'time'         => $data['reservation_time'],
            'end_time'     => $data['reservation_end_time'],
        ];

        if (empty($data['id_table']) || empty($data['reservation_date']) || empty($data['reservation_time']) || empty($data['reservation_end_time'])) {
            $fail('Please fill all fields', $form);
            return;
        }

        // Backend parity with booking: table/date/time formats + past-date guard.
        if (($e = Validator::intRange($data['id_table'], 1, 1000000, 'table')) !== null
            || ($e = Validator::intRange($data['people_count'], 1, 30, 'people')) !== null
            || ($e = Validator::slot($data['reservation_date'], $data['reservation_time'], $data['reservation_end_time'])) !== null) {
            $fail($e, $form);
            return;
        }
        if ($data['id_game'] !== 0 && Validator::intRange($data['id_game'], 1, 1000000, 'game') !== null) {
            $data['id_game'] = 0;
            $form['id_game'] = 0;
        }

        // End time: must be after start, min 30 minutes.
        $startMins = strtotime($data['reservation_time']);
        $endMins   = strtotime($data['reservation_end_time']);
        if ($startMins === false || $endMins === false || ($endMins - $startMins) < 1800) {
            $fail('End time must be at least 30 minutes after the start time.', $form);
            return;
        }

        // People count vs game min/max.
        if (!empty($data['id_game'])) {
            $game = $gameModel->getById($data['id_game']);
            if ($game) {
                if ($data['people_count'] < $game['players_min']) {
                    $fail('This game requires at least ' . $game['players_min'] . ' players. You entered ' . $data['people_count'] . '.', $form);
                    return;
                }
                if ($data['people_count'] > $game['players_max']) {
                    $fail('This game supports max ' . $game['players_max'] . ' players. You entered ' . $data['people_count'] . '.', $form);
                    return;
                }
            }
        }

        // People count vs table capacity.
        $table = $tableModel->getById($data['id_table']);
        if ($table && $data['people_count'] > $table['capacity']) {
            $fail('Table "' . $table['name_table'] . '" only seats ' . $table['capacity'] . ' people. Your group has ' . $data['people_count'] . '.', $form);
            return;
        }

        // Prevent duplicate submission (excluding itself).
        if ($reservationModel->isDuplicate((int)$reservation['id_user'], $data['id_table'], $data['reservation_date'], $data['reservation_time'], $id)) {
            $this->redirect($this->isAdmin() ? '/reservations' : '/reservations/my');
            return;
        }

        // Availability re-check (excluding itself).
        if (!$reservationModel->checkAvailability($data['id_table'], $data['reservation_date'], $data['reservation_time'], $id)) {
            $fail('Table not available at that time', $form);
            return;
        }

        if ($reservationModel->update($id, $data)) {
            (new Table())->syncStatuses();
            $this->redirect('/reservations/my?updated=1');
        } else {
            $fail('Failed to update reservation', $form);
        }
    }

    // A reservation can be modified only while still pending.
    // Once the admin confirms it, editing is blocked: the player must
    // cancel it and make a new reservation instead.
    // Returns 'edit_confirmed' / 'edit_closed' when blocked, null when editable.
    private function editBlockCode(array $reservation): ?string
    {
        $status = $reservation['status_reservation'] ?? '';
        if ($status === 'confirmed') {
            return 'edit_confirmed';
        }
        if ($status !== 'pending') {
            return 'edit_closed';
        }
        return null;
    }

    private function formFromReservation(array $reservation): array
    {
        return [
            'id_table'     => (int)($reservation['id_table'] ?? 0),
            'id_game'      => (int)($reservation['id_game'] ?? 0),
            'people_count' => (int)($reservation['people_count'] ?? 1),
            'date'         => $reservation['reservation_date'] ?? '',
            'time'         => substr($reservation['reservation_time'] ?? '', 0, 5),
            'end_time'     => substr($reservation['reservation_end_time'] ?? '', 0, 5),
        ];
    }

    // Check availability page
    public function availability()
    {
        $this->requireLogin();

        $date = Sanitizer::str($_GET['date'] ?? date('Y-m-d'), 10);
        $time = Sanitizer::str($_GET['time'] ?? '', 8);
        if (Validator::date($date, false) !== null) {
            $date = date('Y-m-d');
        }
        if ($time !== '' && Validator::time($time) !== null) {
            $time = '';
        }

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
        if (($csrfError = Csrf::requireValid()) !== null) {
            http_response_code(419);
            echo $csrfError;
            return;
        }

        $status = Sanitizer::str($_POST['status'] ?? '', 12);
        $reservationModel = new Reservation();
        $reservationModel->updateStatus((int)$id, $status);

        // Re-sync table statuses after any status change
        (new Table())->syncStatuses();

        $this->redirect('/reservations');
    }

    // Player: cancel their own pending or confirmed reservation
    public function cancelByPlayer($id)
    {
        $this->requireLogin();
        if (($csrfError = Csrf::requireValid()) !== null) {
            http_response_code(419);
            echo $csrfError;
            return;
        }

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

        // Ended bookings (past day, or today with end time passed) cannot be cancelled.
        $today = date('Y-m-d');
        $ended = ($reservation['reservation_date'] ?? '') < $today
            || (($reservation['reservation_date'] ?? '') === $today
                && substr($reservation['reservation_end_time'] ?? '', 0, 5) <= date('H:i'));
        if ($ended) {
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

        $date    = Sanitizer::str($_GET['date'] ?? '', 10);
        $time    = Sanitizer::str($_GET['time'] ?? '', 8);
        $endTime = Sanitizer::str($_GET['end_time'] ?? '', 8);

        if (Validator::date($date, false) !== null || Validator::time($time) !== null
            || ($endTime !== '' && Validator::time($endTime) !== null)) {
            echo json_encode([]);
            return;
        }

        if (empty($date) || empty($time)) {
            echo json_encode([]);
            return;
        }

        $tableModel = new Table();
        $gameModel  = new Game();

        // If a game_id is provided filter tables by that game's min/max capacity
        $gameId = Sanitizer::int($_GET['game_id'] ?? 0, 0);
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

        $date = Sanitizer::str($_GET['date'] ?? '', 10);
        $time = Sanitizer::str($_GET['time'] ?? '', 8);

        if (Validator::date($date, false) !== null || Validator::time($time) !== null) {
            echo json_encode([]);
            return;
        }

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