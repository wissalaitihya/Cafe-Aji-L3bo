<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Rating;

class GameController
{
    public function index()
    {
        $gameModel   = new Game();
        $ratingModel = new Rating();

        $filters = [
            'q'          => trim($_GET['q'] ?? ''),
            'category'   => $_GET['category'] ?? '',
            'difficulty' => $_GET['difficulty'] ?? '',
            'players'    => (int)($_GET['players'] ?? 0) ?: '',
            'status'     => $_GET['status'] ?? '',
        ];

        $hasFilter = array_filter($filters, function($v) { return $v !== '' && $v !== 0; });
        $games = $hasFilter ? $gameModel->search($filters) : $gameModel->getAll();

        $ratingMap          = $ratingModel->getAllRatingsMap();
        $playerActiveGameId = null;
        if (!empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'player') {
            $playerActiveGameId = $gameModel->getActiveGameIdForUser((int)$_SESSION['user_id']);
        }

        $this->render('games/index', [
            'games'              => $games,
            'filters'            => $filters,
            'ratingMap'          => $ratingMap,
            'playerActiveGameId' => $playerActiveGameId,
        ]);
    }

    public function show($id)
    {
        $gameModel  = new Game();
        $ratingModel = new Rating();
        $game = $gameModel->getById($id);

        if (!$game) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }

        $related       = $gameModel->getRelated((int)$id, $game['category_game'], 3);
        $ratingSummary = $ratingModel->getSummary((int)$id);
        $recentRatings = $ratingModel->getForGame((int)$id, 5);
        $userRating    = null;
        $canRate       = false;
        $hasPlayed     = false;
        $isPlayingNow  = false;
        if (!empty($_SESSION['user_id'])) {
            $userRating   = $ratingModel->getByUserAndGame((int)$_SESSION['user_id'], (int)$id);
            $hasPlayed    = $ratingModel->hasPlayedGame((int)$_SESSION['user_id'], (int)$id);
            $isPlayingNow = $ratingModel->hasActiveSession((int)$_SESSION['user_id'], (int)$id);
            $canRate      = $hasPlayed; // includes active sessions now
        }

        $this->render('games/show', [
            'game'          => $game,
            'related'       => $related,
            'ratingSummary' => $ratingSummary,
            'recentRatings' => $recentRatings,
            'userRating'    => $userRating,
            'canRate'       => $canRate,
            'hasPlayed'     => $hasPlayed,
            'isPlayingNow'  => $isPlayingNow,
        ]);
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
            'how_to_play'      => trim($_POST['how_to_play'] ?? ''),
            'category_game'    => $_POST['category_game'] ?? 'other',
            'image_game'       => null,
        ];

        if (empty($data['name_game'])) {
            $this->render('games/create', ['error' => 'Game name is required', 'data' => $data]);
            return;
        }

        // Handle image upload
        if (!empty($_FILES['image_game']['name'])) {
            $imagePath = $this->handleImageUpload($_FILES['image_game']);
            if ($imagePath === false) {
                $this->render('games/create', ['error' => 'Invalid image. Allowed: jpg, jpeg, png, gif, webp (max 2MB)', 'data' => $data]);
                return;
            }
            $data['image_game'] = $imagePath;
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
            'how_to_play'      => trim($_POST['how_to_play'] ?? ''),
            'category_game'    => $_POST['category_game'] ?? 'other',
        ];

        if (empty($data['name_game'])) {
            $gameModel = new Game();
            $game = $gameModel->getById($id);
            $this->render('games/edit', ['error' => 'Game name is required', 'game' => $game]);
            return;
        }

        // Handle new image upload
        if (!empty($_FILES['image_game']['name'])) {
            $imagePath = $this->handleImageUpload($_FILES['image_game']);
            if ($imagePath === false) {
                $gameModel = new Game();
                $game = $gameModel->getById($id);
                $this->render('games/edit', ['error' => 'Invalid image. Allowed: jpg, jpeg, png, gif, webp (max 2MB)', 'game' => $game]);
                return;
            }
            $data['image_game'] = $imagePath;
        }
        // If no new file, don't overwrite existing image

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
    private function handleImageUpload(array $file): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $allowedMimes, true)) {
            return false;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            return false;
        }

        $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName  = bin2hex(random_bytes(8)) . '.' . strtolower($ext);
        $uploadDir = __DIR__ . '/../../public/images/games/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $safeName)) {
            return false;
        }

        return 'images/games/' . $safeName;
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

    /** GET /api/recommend?players=N  — JSON list of games suitable for N players */
    public function apiRecommend()
    {
        header('Content-Type: application/json');
        $players = (int)($_GET['players'] ?? 0);
        if ($players < 1) { echo json_encode([]); return; }

        $gameModel   = new Game();
        $ratingModel = new Rating();

        $games = $gameModel->search(['players' => $players, 'status' => 'available']);

        // Attach avg rating & sort: highest rated first, then alphabetically
        foreach ($games as &$g) {
            $summary = $ratingModel->getSummary((int)$g['id_game']);
            $g['avg_stars']    = $summary['avg'];
            $g['total_ratings'] = $summary['total'];
        }
        unset($g);

        usort($games, function($a, $b) {
            if ($b['avg_stars'] !== $a['avg_stars']) return $b['avg_stars'] <=> $a['avg_stars'];
            return $a['name_game'] <=> $b['name_game'];
        });

        echo json_encode(array_values(array_slice($games, 0, 6)));
    }
}