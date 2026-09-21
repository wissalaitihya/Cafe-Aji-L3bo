<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Rating;
use App\Model\Table;
use Core\Csrf;
use Core\Sanitizer;
use Core\Validator;

class GameController
{
    public function index()
    {
        $gameModel   = new Game();
        $ratingModel = new Rating();
        $tableModel  = new Table();

        // Sanitize + whitelist every filter (OWASP: sanitize search/URLs).
        $rawQ = Validator::searchQuery($_GET['q'] ?? '');
        $rawCategory = Sanitizer::str($_GET['category'] ?? '', 30);
        $rawDifficulty = Sanitizer::str($_GET['difficulty'] ?? '', 10);
        $rawStatus = Sanitizer::str($_GET['status'] ?? '', 12);
        $rawPlayers = $_GET['players'] ?? '';
        $page = max(1, Sanitizer::int($_GET['page'] ?? 1, 1));
        $perPage = 48;

        $filters = [
            'q'          => mb_substr($rawQ, 0, 100),
            'category'   => in_array($rawCategory, Validator::CATEGORIES, true) ? $rawCategory : '',
            'difficulty' => in_array($rawDifficulty, Validator::DIFFICULTIES, true) ? $rawDifficulty : '',
            'players'    => '',
            'status'     => in_array($rawStatus, Validator::GAME_STATUS, true) ? $rawStatus : '',
        ];
        if ($rawPlayers !== '' && Validator::intRange($rawPlayers, 1, 30) === null) {
            $filters['players'] = (int) $rawPlayers;
        }

        $hasFilter = array_filter($filters, function($v) { return $v !== '' && $v !== 0; });
        $offset = ($page - 1) * $perPage;
        $games = $hasFilter ? $gameModel->search($filters, $perPage, $offset) : $gameModel->getAll($perPage, $offset);
        $totalGames = $hasFilter ? $gameModel->countAll($filters) : $gameModel->countAll();
        $totalPages = max(1, (int) ceil($totalGames / $perPage));

        $ratingMap          = $ratingModel->getAllRatingsMap();
        $playerActiveGameId = null;
        if (!empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'player') {
            $playerActiveGameId = $gameModel->getActiveGameIdForUser((int)$_SESSION['user_id']);
        }

        // Cheap counts for hero (no full-table fetch).
        $stats = $gameModel->getStats();

        $this->render('games/index', [
            'games'              => $games,
            'filters'            => $filters + ['page' => $page],
            'ratingMap'          => $ratingMap,
            'playerActiveGameId' => $playerActiveGameId,
            'heroStats'          => [
                'games'  => $stats['available'] ?? count($games),
                'tables' => $tableModel->getStats()['total'] ?? 0,
            ],
            'pagination' => ['page' => $page, 'totalPages' => $totalPages, 'total' => $totalGames],
        ]);
    }

    public function show($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            http_response_code(404);
            $this->render('error/404');
            return;
        }
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
        if (($csrfError = Csrf::requireValid()) !== null) {
            $this->render('games/create', ['error' => $csrfError, 'data' => $_POST]);
            return;
        }

        $data = [
            'name_game'        => Sanitizer::str($_POST['name_game'] ?? '', 50),
            'players_min'      => Sanitizer::int($_POST['players_min'] ?? 2, 2),
            'players_max'      => Sanitizer::int($_POST['players_max'] ?? 4, 4),
            'duration'         => Sanitizer::int($_POST['duration'] ?? 30, 30),
            'difficulty'       => Sanitizer::str($_POST['difficulty'] ?? 'medium', 10),
            'description_game' => Sanitizer::str($_POST['description_game'] ?? '', 5000),
            'how_to_play'      => Sanitizer::str($_POST['how_to_play'] ?? '', 8000),
            'category_game'    => Sanitizer::str($_POST['category_game'] ?? 'other', 30),
            'image_game'       => null,
        ];

        $error = $this->validateGameData($data);
        if ($error !== null) {
            $this->render('games/create', ['error' => $error, 'data' => $data]);
            return;
        }

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
        $id = (int) $id;
        if (($csrfError = Csrf::requireValid()) !== null) {
            $gameModel = new Game();
            $game = $gameModel->getById($id);
            $this->render('games/edit', ['error' => $csrfError, 'game' => $game]);
            return;
        }

        $data = [
            'name_game'        => Sanitizer::str($_POST['name_game'] ?? '', 50),
            'players_min'      => Sanitizer::int($_POST['players_min'] ?? 2, 2),
            'players_max'      => Sanitizer::int($_POST['players_max'] ?? 4, 4),
            'duration'         => Sanitizer::int($_POST['duration'] ?? 30, 30),
            'difficulty'       => Sanitizer::str($_POST['difficulty'] ?? 'medium', 10),
            'description_game' => Sanitizer::str($_POST['description_game'] ?? '', 5000),
            'how_to_play'      => Sanitizer::str($_POST['how_to_play'] ?? '', 8000),
            'category_game'    => Sanitizer::str($_POST['category_game'] ?? 'other', 30),
        ];

        $error = $this->validateGameData($data);
        if ($error !== null) {
            $gameModel = new Game();
            $game = $gameModel->getById($id);
            $this->render('games/edit', ['error' => $error, 'game' => $game]);
            return;
        }

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
        if (($csrfError = Csrf::requireValid()) !== null) {
            http_response_code(419);
            echo $csrfError;
            return;
        }

        $gameModel = new Game();
        $gameModel->delete((int)$id);
        $this->redirect('/games');
    }

    private function validateGameData(array $data): ?string
    {
        if (($e = Validator::name($data['name_game'], 50)) !== null) {
            return $e;
        }
        if (($e = Validator::intRange($data['players_min'], 1, 30, 'players_min')) !== null) {
            return $e;
        }
        if (($e = Validator::intRange($data['players_max'], 1, 30, 'players_max')) !== null) {
            return $e;
        }
        if ($data['players_min'] > $data['players_max']) {
            return 'players_min cannot exceed players_max.';
        }
        if (($e = Validator::intRange($data['duration'], 5, 480, 'duration')) !== null) {
            return $e;
        }
        if (($e = Validator::enum($data['difficulty'], Validator::DIFFICULTIES, 'difficulty')) !== null) {
            return $e;
        }
        if (($e = Validator::enum($data['category_game'], Validator::CATEGORIES, 'category')) !== null) {
            return $e;
        }
        return null;
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
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $allowedMimes, true)) {
            return false;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            return false;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return false;
        }
        // Normalize jpeg extension
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $safeName  = bin2hex(random_bytes(8)) . '.' . $ext;
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
        $players = Sanitizer::int($_GET['players'] ?? 0, 0);
        if ($players < 1 || $players > 30) { echo json_encode([]); return; }

        $gameModel   = new Game();
        $ratingModel = new Rating();

        $games = $gameModel->search(['players' => $players, 'status' => 'available'], 12);

        // One batched ratings query (no N+1), then sort in PHP.
        $ids = array_map(fn($g) => (int) $g['id_game'], $games);
        $summaries = $ratingModel->getSummariesForGames($ids);
        foreach ($games as &$g) {
            $s = $summaries[(int) $g['id_game']] ?? ['avg' => 0.0, 'total' => 0];
            $g['avg_stars']    = $s['avg'];
            $g['total_ratings'] = $s['total'];
            // Never leak TEXT blobs over the API
            unset($g['description_game'], $g['how_to_play']);
        }
        unset($g);

        usort($games, function($a, $b) {
            if ($b['avg_stars'] !== $a['avg_stars']) return $b['avg_stars'] <=> $a['avg_stars'];
            return $a['name_game'] <=> $b['name_game'];
        });

        echo json_encode(array_values(array_slice($games, 0, 6)));
    }

    /** GET /api/games/search?q=… — lightweight autocomplete (id + name only). */
    public function apiSearch()
    {
        header('Content-Type: application/json');
        $q = Validator::searchQuery($_GET['q'] ?? '');
        if (mb_strlen($q) < 2) {
            echo json_encode([]);
            return;
        }
        $rows = (new Game())->searchNames($q, 6);
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => (int) $r['id_game'],
                'name' => $r['name_game'],
                'href' => BASE_PATH . '/games/' . (int) $r['id_game'],
            ];
        }
        echo json_encode($out);
    }
}