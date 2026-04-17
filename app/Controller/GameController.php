<?php

namespace App\Controller;

use App\Model\Game;

class GameController
{
    public function index()
    {
        $gameModel = new Game();

        $category = $_GET['category'] ?? null;
        if ($category) {
            $games = $gameModel->getByCategory($category);
        } else {
            $games = $gameModel->getAll();
        }

        $this->render('games/index', [
            'games'    => $games,
            'category' => $category,
        ]);
    }

    public function show($id)
    {
        $gameModel = new Game();
        $game = $gameModel->getById($id);

        if (!$game) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }

        $this->render('games/show', ['game' => $game]);
    }

    public function create()
    {
        $this->requireAdmin();
        $this->render('games/create');
    }

    public function store()
    {
        $this->requireAdmin();

        $data = [
            'name_game'        => trim($_POST['name_game'] ?? ''),
            'players_min'      => (int)($_POST['players_min'] ?? 2),
            'players_max'      => (int)($_POST['players_max'] ?? 4),
            'duration'         => (int)($_POST['duration'] ?? 30),
            'difficulty'       => $_POST['difficulty'] ?? 'medium',
            'description_game' => trim($_POST['description_game'] ?? ''),
            'category_game'    => $_POST['category_game'] ?? 'other',
        ];

        if (empty($data['name_game'])) {
            $this->render('games/create', ['error' => 'Game name is required', 'data' => $data]);
            return;
        }

        $gameModel = new Game();
        if ($gameModel->create($data)) {
            $this->redirect('/games');
        } else {
            $this->render('games/create', ['error' => 'Failed to create game', 'data' => $data]);
        }
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $gameModel = new Game();
        $game = $gameModel->getById($id);

        if (!$game) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }

        $this->render('games/edit', ['game' => $game]);
    }

    public function update($id)
    {
        $this->requireAdmin();

        $data = [
            'name_game'        => trim($_POST['name_game'] ?? ''),
            'players_min'      => (int)($_POST['players_min'] ?? 2),
            'players_max'      => (int)($_POST['players_max'] ?? 4),
            'duration'         => (int)($_POST['duration'] ?? 30),
            'difficulty'       => $_POST['difficulty'] ?? 'medium',
            'description_game' => trim($_POST['description_game'] ?? ''),
            'category_game'    => $_POST['category_game'] ?? 'other',
        ];

        if (empty($data['name_game'])) {
            $gameModel = new Game();
            $game = $gameModel->getById($id);
            $this->render('games/edit', ['error' => 'Game name is required', 'game' => $game]);
            return;
        }

        $gameModel = new Game();
        if ($gameModel->update($id, $data)) {
            $this->redirect('/games/' . $id);
        } else {
            $game = $gameModel->getById($id);
            $this->render('games/edit', ['error' => 'Failed to update game', 'game' => $game]);
        }
    }

    public function destroy($id)
    {
        $this->requireAdmin();

        $gameModel = new Game();
        $gameModel->delete($id);
        $this->redirect('/games');
    }

    // ========================
    // HELPER METHODS
    // ========================
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