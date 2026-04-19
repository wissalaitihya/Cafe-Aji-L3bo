<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Game Catalogue</h1>

<!-- Category Filter -->
<div class="filter-bar">
    <a href="<?= BASE_PATH ?>/games" class="btn <?= empty($category) ? 'active' : '' ?>">All</a>
    <a href="<?= BASE_PATH ?>/games?category=social_deduction" class="btn <?= ($category ?? '') === 'social_deduction' ? 'active' : '' ?>">Social Deduction</a>
    <a href="<?= BASE_PATH ?>/games?category=party" class="btn <?= ($category ?? '') === 'party' ? 'active' : '' ?>">Party</a>
    <a href="<?= BASE_PATH ?>/games?category=cooperative" class="btn <?= ($category ?? '') === 'cooperative' ? 'active' : '' ?>">Cooperative</a>
    <a href="<?= BASE_PATH ?>/games?category=team" class="btn <?= ($category ?? '') === 'team' ? 'active' : '' ?>">Team</a>
    <a href="<?= BASE_PATH ?>/games?category=trivia" class="btn <?= ($category ?? '') === 'trivia' ? 'active' : '' ?>">Trivia</a>
</div>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <a href="<?= BASE_PATH ?>/games/create" class="btn btn-success">+ Add Game</a>
<?php endif; ?>

<?php if (empty($games)): ?>
    <p>No games found.</p>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($games as $game): ?>
            <div class="card">
                <h3><?= htmlspecialchars($game['name_game']) ?></h3>
                <p><strong>Category:</strong> <?= htmlspecialchars($game['category_game']) ?></p>
                <p><strong>Players:</strong> <?= $game['players_min'] ?>-<?= $game['players_max'] ?></p>
                <p><strong>Duration:</strong> <?= $game['duration'] ?> min</p>
                <p><strong>Difficulty:</strong> <?= htmlspecialchars($game['difficulty']) ?></p>
                <span class="badge badge-<?= $game['status_game'] === 'available' ? 'success' : 'warning' ?>">
                    <?= $game['status_game'] ?>
                </span>
                <div class="card-actions">
                    <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>" class="btn btn-small">Details</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/edit" class="btn btn-small btn-warning">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>