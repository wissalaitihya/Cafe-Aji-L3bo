<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Edit Game</h1>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/Cafe-Aji-L3bo/games/<?= $game['id_game'] ?>/update" method="POST" class="form-card">
    <div class="form-group">
        <label for="name_game">Game Name</label>
        <input type="text" id="name_game" name="name_game" value="<?= htmlspecialchars($game['name_game']) ?>" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="players_min">Min Players</label>
            <input type="number" id="players_min" name="players_min" value="<?= $game['players_min'] ?>" min="1" required>
        </div>
        <div class="form-group">
            <label for="players_max">Max Players</label>
            <input type="number" id="players_max" name="players_max" value="<?= $game['players_max'] ?>" min="1" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="duration">Duration (min)</label>
            <input type="number" id="duration" name="duration" value="<?= $game['duration'] ?>" min="5" required>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <option value="easy" <?= $game['difficulty'] === 'easy' ? 'selected' : '' ?>>Easy</option>
                <option value="medium" <?= $game['difficulty'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="hard" <?= $game['difficulty'] === 'hard' ? 'selected' : '' ?>>Hard</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="category_game">Category</label>
        <select id="category_game" name="category_game">
            <option value="social_deduction" <?= $game['category_game'] === 'social_deduction' ? 'selected' : '' ?>>Social Deduction</option>
            <option value="party" <?= $game['category_game'] === 'party' ? 'selected' : '' ?>>Party</option>
            <option value="cooperative" <?= $game['category_game'] === 'cooperative' ? 'selected' : '' ?>>Cooperative</option>
            <option value="team" <?= $game['category_game'] === 'team' ? 'selected' : '' ?>>Team</option>
            <option value="trivia" <?= $game['category_game'] === 'trivia' ? 'selected' : '' ?>>Trivia</option>
            <option value="other" <?= $game['category_game'] === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="description_game">Description</label>
        <textarea id="description_game" name="description_game" rows="4"><?= htmlspecialchars($game['description_game'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn">Save Changes</button>
    <a href="/Cafe-Aji-L3bo/games/<?= $game['id_game'] ?>" class="btn btn-secondary">Cancel</a>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>