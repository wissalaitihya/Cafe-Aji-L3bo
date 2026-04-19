<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <a href="<?= BASE_PATH ?>/tables" class="btn-back">&#8592; Back to Tables</a>
    <h1>&#10133; Add New Table</h1>
</div>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= BASE_PATH ?>/tables" method="POST" class="form-card">
    <div class="form-group">
        <label for="name_table">Table Name</label>
        <input type="text" id="name_table" name="name_table"
               value="<?= htmlspecialchars($data['name_table'] ?? '') ?>"
               placeholder="e.g. Table 5" required>
    </div>

    <div class="form-group">
        <label for="capacity">Capacity (players)</label>
        <input type="number" id="capacity" name="capacity"
               value="<?= (int)($data['capacity'] ?? 4) ?>" min="1" max="30" required>
    </div>

    <div class="form-group">
        <label for="status_table">Status</label>
        <select id="status_table" name="status_table">
            <option value="free" <?= ($data['status_table'] ?? 'free') === 'free' ? 'selected' : '' ?>>Free</option>
            <option value="occupied" <?= ($data['status_table'] ?? '') === 'occupied' ? 'selected' : '' ?>>Occupied</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success">&#10003; Create Table</button>
        <a href="<?= BASE_PATH ?>/tables" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>
