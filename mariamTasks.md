## 🎲 Member 1 — Module 1: Game Catalogue

> 🎯 Evaluation: Tracer une requête GET /games/5 depuis Router → Controller → Model → View

### Day 1 — Game model basics

- [x] **M1-01** — Créer `Game.php` avec namespace `App\Models`
- [x] **M1-02** — Implémenter `Game::all()` — fetch all games avec PDO
- [x] **M1-03** — Implémenter `Game::find($id)` — fetch un seul game par ID

### Day 2 — Controller & list/detail views

- [ ] **M1-04** — Créer `GameController.php` avec namespace `App\Controllers`
- [ ] **M1-05** — Implémenter `GameController::index()` — fetch all, render list view
- [ ] **M1-06** — Implémenter `GameController::show($id)` — fetch one, render detail view
- [ ] **M1-07** — Construire `views/games/index.php` — liste des games (name, category, players, duration)
- [ ] **M1-08** — Construire `views/games/show.php` — détail complet (description, difficulty, status)

### Day 3 — Filter & CRUD model methods

- [ ] **M1-09** — Implémenter `Game::filterByCategory($category)`
- [ ] **M1-10** — Implémenter `GameController::filter()` — gérer le filtre category GET param
- [ ] **M1-11** — Ajouter l'UI du filtre category (Stratégie, Ambiance, Famille, Experts)
- [ ] **M1-12** — Implémenter `Game::create($data)` — INSERT avec PDO prepared statement
- [ ] **M1-13** — Implémenter `Game::update($id, $data)`
- [ ] **M1-14** — Implémenter `Game::delete($id)`

### Day 4 — Admin CRUD controllers & forms

- [ ] **M1-15** — Implémenter `GameController::create()` — render add form (admin only)
- [ ] **M1-16** — Implémenter `GameController::store()` — validate + save new game
- [ ] **M1-17** — Implémenter `GameController::edit($id)` — render edit form
- [ ] **M1-18** — Implémenter `GameController::update($id)` — validate + update
- [ ] **M1-19** — Implémenter `GameController::destroy($id)` — delete game
- [ ] **M1-20** — Construire `views/games/create.php` et `views/games/edit.php`

### Day 5 — Validation & PR

- [ ] **M1-21** — Ajouter form validation (required fields, numeric checks) + affichage des erreurs
- [ ] **M1-22** — Ouvrir PR → demander review à Member 4

---