<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <a href="<?= BASE_PATH ?>/games" class="btn-back">&#8592; Games</a>
    <h1>&#9998; Edit Game</h1>
</div>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/update" method="POST" class="form-card" enctype="multipart/form-data">
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
                <option value="easy"   <?= $game['difficulty'] === 'easy'   ? 'selected' : '' ?>>Easy</option>
                <option value="medium" <?= $game['difficulty'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="hard"   <?= $game['difficulty'] === 'hard'   ? 'selected' : '' ?>>Hard</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="category_game">Category</label>
        <select id="category_game" name="category_game">
            <option value="social_deduction" <?= $game['category_game'] === 'social_deduction' ? 'selected' : '' ?>>Social Deduction</option>
            <option value="party"            <?= $game['category_game'] === 'party'            ? 'selected' : '' ?>>Party</option>
            <option value="cooperative"      <?= $game['category_game'] === 'cooperative'      ? 'selected' : '' ?>>Cooperative</option>
            <option value="team"             <?= $game['category_game'] === 'team'             ? 'selected' : '' ?>>Team</option>
            <option value="trivia"           <?= $game['category_game'] === 'trivia'           ? 'selected' : '' ?>>Trivia</option>
            <option value="other"            <?= $game['category_game'] === 'other'            ? 'selected' : '' ?>>Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="description_game">Description</label>
        <textarea id="description_game" name="description_game" rows="4"><?= htmlspecialchars($game['description_game'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="how_to_play">How to Play <span class="muted">(optional)</span></label>
        <textarea id="how_to_play" name="how_to_play" rows="5" placeholder="Step-by-step instructions for players..."><?= htmlspecialchars($game['how_to_play'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label>Current Image</label>
        <?php if (!empty($game['image_game'])): ?>
            <div style="margin-bottom:0.5rem">
                <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($game['image_game']) ?>"
                     alt="Current image" style="max-height:160px;border-radius:8px;border:1px solid var(--border)">
            </div>
        <?php else: ?>
            <p class="muted">No image uploaded yet.</p>
        <?php endif; ?>
        <label for="image_game">Replace Image <span class="muted">(optional, max 2MB)</span></label>
        <input type="file" id="image_game" name="image_game" accept="image/jpeg,image/png,image/gif,image/webp">
        <div id="image-preview" style="display:none;margin-top:0.5rem">
            <img id="preview-img" src="" alt="Preview" style="max-height:180px;border-radius:8px;border:1px solid var(--border)">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success">&#10003; Save Changes</button>
        <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
document.getElementById('image_game').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = '';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
