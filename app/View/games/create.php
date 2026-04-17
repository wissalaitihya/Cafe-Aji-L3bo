<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Add New Game</h1>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/Cafe-Aji-L3bo/games" method="POST" class="form-card">
    <div class="form-group">
        <label for="name_game">Game Name</label>
        <input type="text" id="name_game" name="name_game" value="<?= htmlspecialchars($data['name_game'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="players_min">Min Players</label>
            <input type="number" id="players_min" name="players_min" value="<?= $data['players_min'] ?? 2 ?>" min="1" required>
        </div>
        <div class="form-group">
            <label for="players_max">Max Players</label>
            <input type="number" id="players_max" name="players_max" value="<?= $data['players_max'] ?? 4 ?>" min="1" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="duration">Duration (min)</label>
            <input type="number" id="duration" name="duration" value="<?= $data['duration'] ?? 30 ?>" min="5" required>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <option value="easy" <?= ($data['difficulty'] ?? '') === 'easy' ? 'selected' : '' ?>>Easy</option>
                <option value="medium" <?= ($data['difficulty'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="hard" <?= ($data['difficulty'] ?? '') === 'hard' ? 'selected' : '' ?>>Hard</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="category_game">Category</label>
        <select id="category_game" name="category_game">
            <option value="social_deduction">Social Deduction</option>
            <option value="party">Party</option>
            <option value="cooperative">Cooperative</option>
            <option value="team">Team</option>
            <option value="trivia">Trivia</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="description_game">Description</label>
        <textarea id="description_game" name="description_game" rows="4"><?= htmlspecialchars($data['description_game'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn">Add Game</button>
    <a href="/Cafe-Aji-L3bo/games" class="btn btn-secondary">Cancel</a>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>