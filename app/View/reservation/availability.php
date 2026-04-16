<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Check Table Availability</h1>

<form action="/Cafe-Aji-L3bo/reservations/availability" method="GET" class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
            <label for="time">Time</label>
            <input type="time" id="time" name="time" value="<?= htmlspecialchars($time ?? '') ?>" required>
        </div>
    </div>
    <button type="submit" class="btn">Check</button>
</form>

<?php if (!empty($searched)): ?>
    <h2>Available Tables for <?= htmlspecialchars($date) ?> at <?= htmlspecialchars($time) ?></h2>

    <?php if (empty($tables)): ?>
        <p>No tables available at this time.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Capacity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table): ?>
                    <tr>
                        <td><?= htmlspecialchars($table['name_table']) ?></td>
                        <td><?= $table['capacity'] ?> people</td>
                        <td><a href="/Cafe-Aji-L3bo/reservations/create?id_table=<?= $table['id_table'] ?>&date=<?= urlencode($date) ?>&time=<?= urlencode($time) ?>" class="btn btn-small btn-success">Book</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>