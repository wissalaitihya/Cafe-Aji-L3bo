<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1>Notre Catalogue de Jeux</h1>

<div class="games-grid">
    <?php if (empty($games)): ?>
        <p>Aucun jeu disponible.</p>
    <?php else: ?>
        <?php foreach ($games as $game): ?>
            <div class="game-card">
                <h2><?= htmlspecialchars($game['name']) ?></h2>
                <p class="category"><?= htmlspecialchars($game['category']) ?></p>
                <p class="players">
                    <?= (int)$game['min_players'] ?> - <?= (int)$game['max_players'] ?> joueurs
                </p>
                <p class="duration"><?= (int)$game['duration_minutes'] ?> min</p>
                <p class="status <?= $game['status'] ?>">
                    <?= $game['status'] === 'available' ? 'Disponible' : 'En cours' ?>
                </p>
                <a href="/games/<?= (int)$game['id'] ?>" class="btn">Voir les détails</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
