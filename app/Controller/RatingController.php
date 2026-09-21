<?php

namespace App\Controller;

use App\Model\Game;
use App\Model\Rating;
use Core\Csrf;
use Core\Sanitizer;

class RatingController
{
    /** POST /games/{id}/rate */
    public function store($id)
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }

        if (($this->isAdmin())) {
            http_response_code(403);
            require __DIR__ . '/../View/error/403.php';
            return;
        }

        if (($csrfError = Csrf::requireValid()) !== null) {
            $this->redirect('/games/' . (int)$id . '?rating_error=1');
            return;
        }

        $gameId = (int)$id;
        if ($gameId < 1) {
            $this->redirect('/games');
            return;
        }
        $ratingModel = new Rating();

        // Stars: 0.5 → 5.0, half-stars allowed
        $stars = (float)($_POST['stars'] ?? 0);
        if ($stars < 0.5 || $stars > 5.0 || ($stars * 2) != floor($stars * 2)) {
            $this->redirect('/games/' . $gameId . '?rating_error=1');
            return;
        }

        $comment = Sanitizer::str($_POST['comment_rating'] ?? '', 2000);
        $comment = $comment !== '' ? $comment : null;

        $gameModel = new Game();
        if (!$gameModel->getById($gameId)) {
            $this->redirect('/games/' . $gameId . '?rating_error=1');
            return;
        }

        // Only players who played (or are playing) the game can rate
        $userId = (int)$_SESSION['user_id'];
        if (!$ratingModel->hasPlayedGame($userId, $gameId)) {
            $this->redirect('/games/' . $gameId . '?rating_error=1');
            return;
        }

        if ($ratingModel->upsert($userId, $gameId, $stars, $comment)) {
            $this->redirect('/games/' . $gameId . '?rated=1');
        } else {
            $this->redirect('/games/' . $gameId . '?rating_error=1');
        }
    }

    // ========================
    // HELPER METHODS
    // ========================
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
}