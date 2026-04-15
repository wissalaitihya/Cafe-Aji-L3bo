<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Game;

class GameController extends Controller {
    public function index(): void {
        $games = Game::all();
        $this->render('games/index', ['games' => $games]);
    }

    public function show(int $id): void {
        $game = Game::find($id);
        
        if (!$game) {
            $this->redirect('/games');
        }
        
        $this->render('games/show', ['game' => $game]);
    }
}