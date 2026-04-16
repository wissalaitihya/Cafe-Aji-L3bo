<?php require __DIR__ . '/../layout/header.php'; ?>

<a href="/Cafe-Aji-L3bo/games" class="btn btn-small">&larr; Back to Games</a>

<div class="detail-card">
    <h1><?= htmlspecialchars($game['name_game']) ?></h1>

    <span class="badge badge-<?= $game['status_game'] === 'available' ? 'success' : 'warning' ?>">
        <?= $game['status_game'] ?>
    </span>

    <table class="detail-table">
        <tr><th>Category</th><td><?= htmlspecialchars($game['category_game']) ?></td></tr>
        <tr><th>Players</th><td><?= $game['players_min'] ?> - <?= $game['players_max'] ?></td></tr>
        <tr><th>Duration</th><td><?= $game['duration'] ?> minutes</td></tr>
        <tr><th>Difficulty</th><td><?= htmlspecialchars($game['difficulty']) ?></td></tr>
    </table>

    <h3>Description</h3>
    <p><?= htmlspecialchars($game['description_game'] ?? 'No description') ?></p>

    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="card-actions">
            <a href="/Cafe-Aji-L3bo/games/<?= $game['id_game'] ?>/edit" class="btn btn-warning">Edit</a>
            <form action="/Cafe-Aji-L3bo/games/<?= $game['id_game'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Delete this game?')">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>