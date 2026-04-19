<?php require __DIR__ . '/../layout/header.php'; ?>

<?php
// Mini helper for star display on cards
function cardStars(float $avg): string {
    $out = '<span class="card-stars">';
    for ($i = 1; $i <= 5; $i++) {
        if ($avg >= $i)         $out .= '<span class="cstar filled">★</span>';
        elseif ($avg >= $i-0.5) $out .= '<span class="cstar half">★</span>';
        else                    $out .= '<span class="cstar empty">★</span>';
    }
    return $out . '</span>';
}
?>

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
        <?php $anyFilter = array_filter($filters, function($v){ return $v !== ''; }); ?>
        <?php if ($anyFilter): ?>
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

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-info landing-cta">
        &#127881; Welcome! <a href="<?= BASE_PATH ?>/register">Create a free account</a> or <a href="<?= BASE_PATH ?>/login">log in</a> to book a table and play!
    </div>
<?php endif; ?>

<?php if (empty($games)): ?>
    <div class="empty-state"><p>No games found matching your search.</p></div>
<?php else: ?>
    <p class="results-count"><?= count($games) ?> game<?= count($games) !== 1 ? 's' : '' ?> found</p>
    <div class="card-grid">
        <?php foreach ($games as $game):
            $gid      = (int)$game['id_game'];
            $rData    = $ratingMap[$gid] ?? ['avg' => 0, 'total' => 0];
            $playing  = ($playerActiveGameId === $gid);
        ?>
            <div class="card game-card <?= $playing ? 'card-playing' : '' ?>">
                <?php if ($playing): ?>
                    <div class="card-playing-ribbon">&#127918; Playing Now</div>
                <?php endif; ?>
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
                    <!-- Star rating on card -->
                    <div class="card-rating-row">
                        <?php if ($rData['total'] > 0): ?>
                            <?= cardStars($rData['avg']) ?>
                            <span class="card-rating-num"><?= number_format($rData['avg'], 1) ?></span>
                            <span class="card-rating-cnt">(<?= $rData['total'] ?>)</span>
                        <?php else: ?>
                            <span class="card-rating-empty">No ratings yet</span>
                        <?php endif; ?>
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

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-info landing-cta">
        &#127881; Welcome! <a href="<?= BASE_PATH ?>/register">Create a free account</a> or <a href="<?= BASE_PATH ?>/login">log in</a> to book a table and play!
    </div>
<?php endif; ?>

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