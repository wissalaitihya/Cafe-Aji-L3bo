<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ── Hero Section ── -->
<section class="hero">
    <div class="hero-glow hero-glow-a"></div>
    <div class="hero-glow hero-glow-b"></div>
    <div class="hero-inner">
        <p class="hero-eyebrow">&#129351; Casablanca's Board Game Café</p>
        <h1 class="hero-title">Play.<br><span class="hero-title-accent">Laugh.</span> Repeat.</h1>
        <p class="hero-sub">From social deduction to cooperative quests — grab your squad, pick a table, and let the games begin.</p>
        <div class="hero-actions">
            <a href="<?= BASE_PATH ?>/games" class="btn btn-primary btn-hero">&#127918; Browse Games</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_PATH ?>/reservations/create" class="btn btn-hero btn-outline">&#128197; Book a Table</a>
            <?php else: ?>
                <a href="<?= BASE_PATH ?>/register" class="btn btn-hero btn-outline">&#127881; Create Account</a>
            <?php endif; ?>
        </div>
        <div class="hero-stats">
            <div class="hero-stat"><span class="hero-stat-num"><?= count($games) ?>+</span><span class="hero-stat-label">Board Games</span></div>
            <div class="hero-stat"><span class="hero-stat-num">20+</span><span class="hero-stat-label">Comfortable Tables</span></div>
            <div class="hero-stat"><span class="hero-stat-num">2–18</span><span class="hero-stat-label">Players per Game</span></div>
            <div class="hero-stat"><span class="hero-stat-num">24/7</span><span class="hero-stat-label">Game Service</span></div>
        </div>
    </div>
</section>

<div class="page-header">
    <h1>&#127918; Game Catalogue</h1>
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="<?= BASE_PATH ?>/games/create" class="btn btn-success">+ Add Game</a>
    <?php endif; ?>
</div>

<!-- Advanced Search / Filter -->
<form method="GET" action="<?= BASE_PATH ?>/games" class="search-bar" id="search-form">
    <div class="search-row">
        <div class="search-input-wrap">
            <span class="search-icon">&#128269;</span>
            <input type="text" name="q" placeholder="Search games by name or description…"
                   value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="search-input" autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if (array_filter($filters, function($v){ return $v !== ''; })): ?>
            <a href="<?= BASE_PATH ?>/games" class="btn btn-secondary">&#10005; Clear</a>
        <?php endif; ?>
    </div>
    <div class="filter-chips">
        <select name="category" class="filter-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach (['social_deduction'=>'Social Deduction','party'=>'Party','cooperative'=>'Cooperative','team'=>'Team','trivia'=>'Trivia','other'=>'Other'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($filters['category'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <select name="difficulty" class="filter-select" onchange="this.form.submit()">
            <option value="">Any Difficulty</option>
            <option value="easy"   <?= ($filters['difficulty'] ?? '') === 'easy'   ? 'selected' : '' ?>>&#128994; Easy</option>
            <option value="medium" <?= ($filters['difficulty'] ?? '') === 'medium' ? 'selected' : '' ?>>&#128992; Medium</option>
            <option value="hard"   <?= ($filters['difficulty'] ?? '') === 'hard'   ? 'selected' : '' ?>>&#128308; Hard</option>
        </select>
        <div class="filter-players-wrap">
            <label>&#128101; Players:</label>
            <input type="number" name="players" min="1" max="20"
                   value="<?= htmlspecialchars($filters['players'] ?? '') ?>"
                   placeholder="e.g. 4" class="filter-input-small">
        </div>
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>&#9989; Available</option>
            <option value="in_use"    <?= ($filters['status'] ?? '') === 'in_use'    ? 'selected' : '' ?>>&#128308; In Use</option>
        </select>
    </div>
</form>

<?php if (empty($games)): ?>
    <div class="empty-state"><p>No games found matching your search.</p></div>
<?php else: ?>
    <p class="results-count"><?= count($games) ?> game<?= count($games) !== 1 ? 's' : '' ?> found</p>
    <div class="card-grid">
        <?php foreach ($games as $game): ?>
            <div class="card game-card">
                <?php if (!empty($game['image_game'])): ?>
                    <div class="card-image">
                        <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($game['image_game']) ?>"
                             alt="<?= htmlspecialchars($game['name_game']) ?>">
                    </div>
                <?php else: ?>
                    <div class="card-image-placeholder">&#127918;</div>
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= htmlspecialchars($game['name_game']) ?></h3>
                    <div class="game-info-row">
                        <span>&#128101; <?= $game['players_min'] ?>–<?= $game['players_max'] ?></span>
                        <span>&#9200; <?= $game['duration'] ?>m</span>
                        <span class="badge badge-<?= $game['difficulty'] === 'easy' ? 'success' : ($game['difficulty'] === 'hard' ? 'danger' : 'warning') ?>"><?= ucfirst($game['difficulty']) ?></span>
                    </div>
                    <span class="badge badge-<?= $game['status_game'] === 'available' ? 'success' : 'warning' ?>">
                        <?= $game['status_game'] === 'available' ? 'Available' : 'In Use' ?>
                    </span>
                    <div class="card-actions">
                        <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>" class="btn btn-small">Details</a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/edit" class="btn btn-small btn-warning">&#9998; Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
