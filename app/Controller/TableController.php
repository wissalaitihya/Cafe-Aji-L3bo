<?php

namespace App\Controller;

use App\Model\Table;
use App\Model\Session;
use Core\Csrf;
use Core\Sanitizer;
use Core\Validator;

class TableController
{
    public function index()
    {
        $this->requireAdmin();

        $tableModel = new Table();
        $tableModel->syncStatuses();
        $tables = $tableModel->getAll();

        $this->render('tables/index', ['tables' => $tables]);
    }

    public function create()
    {
        $this->requireAdmin();
        $this->render('tables/create');
    }

    public function store()
    {
        $this->requireAdmin();
        if (($csrfError = Csrf::requireValid()) !== null) {
            $this->render('tables/create', ['error' => $csrfError, 'data' => []]);
            return;
        }

        $data = [
            'name_table'   => Sanitizer::str($_POST['name_table'] ?? '', 20),
            'capacity'     => Sanitizer::int($_POST['capacity'] ?? 0, 0),
            'status_table' => Sanitizer::str($_POST['status_table'] ?? 'free', 10),
        ];

        $allowedStatus = ['free', 'occupied'];
        if (!in_array($data['status_table'], $allowedStatus, true)) {
            $data['status_table'] = 'free';
        }

        if (($e = Validator::name($data['name_table'], 20)) !== null
            || ($e = Validator::intRange($data['capacity'], 1, 30, 'capacity')) !== null) {
            $this->render('tables/create', [
                'error' => $e,
                'data'  => $data,
            ]);
            return;
        }

        if (empty($data['name_table']) || $data['capacity'] < 1) {
            $this->render('tables/create', [
                'error' => 'Name and a valid capacity are required.',
                'data'  => $data,
            ]);
            return;
        }

        $tableModel = new Table();
        if ($tableModel->create($data)) {
            $this->redirect('/tables');
        } else {
            $this->render('tables/create', [
                'error' => 'Failed to create table.',
                'data'  => $data,
            ]);
        }
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $tableModel = new Table();
        $table = $tableModel->getById((int)$id);

        if (!$table) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }

        $this->render('tables/edit', ['table' => $table]);
    }

    public function update($id)
    {
        $this->requireAdmin();
        if (($csrfError = Csrf::requireValid()) !== null) {
            http_response_code(419);
            echo $csrfError;
            return;
        }

        $data = [
            'name_table'   => Sanitizer::str($_POST['name_table'] ?? '', 20),
            'capacity'     => Sanitizer::int($_POST['capacity'] ?? 0, 0),
            'status_table' => Sanitizer::str($_POST['status_table'] ?? 'free', 10),
        ];

        $allowedStatus = ['free', 'occupied'];
        if (!in_array($data['status_table'], $allowedStatus, true)) {
            $data['status_table'] = 'free';
        }

        if (($e = Validator::name($data['name_table'], 20)) !== null
            || ($e = Validator::intRange($data['capacity'], 1, 30, 'capacity')) !== null) {
            $tableModel = new Table();
            $table = $tableModel->getById((int)$id);
            $this->render('tables/edit', [
                'error' => $e,
                'table' => array_merge($table ?? [], $data),
            ]);
            return;
        }

        if (empty($data['name_table']) || $data['capacity'] < 1) {
            $tableModel = new Table();
            $table = $tableModel->getById((int)$id);
            $this->render('tables/edit', [
                'error' => 'Name and a valid capacity are required.',
                'table' => array_merge($table ?? [], $data),
            ]);
            return;
        }

        $tableModel = new Table();
        if ($tableModel->update((int)$id, $data)) {
            $this->redirect('/tables');
        } else {
            $table = $tableModel->getById((int)$id);
            $this->render('tables/edit', [
                'error' => 'Failed to update table.',
                'table' => array_merge($table ?? [], $data),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->requireAdmin();
        if (($csrfError = Csrf::requireValid()) !== null) {
            http_response_code(419);
            echo $csrfError;
            return;
        }

        $tableModel = new Table();
        $tableModel->delete((int)$id);
        $this->redirect('/tables');
    }

    /**
     * Admin: force-free a table (players left early).
     * Ends any active session on the table and sets status to free.
     */
    public function setFree($id)
    {
        $this->requireAdmin();
        if (($csrfError = Csrf::requireValid()) !== null) {
            http_response_code(419);
            echo $csrfError;
            return;
        }

        $id = (int)$id;
        $sessionModel = new Session();
        $tableModel   = new Table();

        // End any running session tied to this table
        $activeSession = $sessionModel->getActiveByTableId($id);
        if ($activeSession) {
            $sessionModel->endSession((int)$activeSession['id_session']);
        }

        // Force status to free
        $tableModel->setStatus($id, 'free');

        $this->redirect('/tables');
    }

    // ── Helpers ──────────────────────────────────────
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

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    private function isAdmin(): bool
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
