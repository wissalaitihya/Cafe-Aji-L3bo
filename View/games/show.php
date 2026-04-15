<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h1><?= htmlspecialchars($game['name']) ?></h1>

<div class="game-detail">
    <p class="category"><strong>Catégorie:</strong> <?= htmlspecialchars($game['category']) ?></p>
    
    <p class="difficulty">
        <strong>Difficulté:</strong> 
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="<?= $i <= $game['difficulty'] ? 'filled' : '' ?>">★</span>
        <?php endfor; ?>
    </p>
    
    <p class="players">
        <strong>Joueurs:</strong> 
        <?= (int)$game['min_players'] ?> - <?= (int)$game['max_players'] ?> personnes
    </p>
    
    <p class="duration">
        <strong>Durée estimée:</strong> <?= (int)$game['duration_minutes'] ?> minutes
    </p>
    
    <p class="status <?= $game['status'] ?>">
        <strong>Statut:</strong> 
        <?= $game['status'] === 'available' ? 'Disponible' : 'En cours d\'utilisation' ?>
    </p>
    
    <div class="description">
        <strong>Description:</strong>
        <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
    </div>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="admin-actions">
            <a href="/games/<?= (int)$game['id'] ?>/edit" class="btn">Modifier</a>
            <form method="POST" action="/games/<?= (int)$game['id'] ?>/delete" style="display:inline">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
            </form>
        </div>
    <?php endif; ?>
    
    <a href="/games" class="btn">← Retour à la liste</a>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
