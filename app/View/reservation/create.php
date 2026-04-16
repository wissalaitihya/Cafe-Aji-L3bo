<?php
    $pDate   = $prefill['date'] ?? '';
    $pTime   = $prefill['time'] ?? '';
    $pTable  = $prefill['id_table'] ?? '';
    $pPeople = $prefill['people_count'] ?? '';
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Book a Table</h1>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/Cafe-Aji-L3bo/reservations" method="POST" class="form-card">
    <div class="form-group">
        <label for="reservation_date">Date</label>
        <input type="date" id="reservation_date" name="reservation_date" value="<?= htmlspecialchars($pDate) ?>" min="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="form-group">
        <label for="reservation_time">Time</label>
        <input type="time" id="reservation_time" name="reservation_time" value="<?= htmlspecialchars($pTime) ?>" required>
    </div>

    <div class="form-group">
        <label for="people_count">Number of People</label>
        <input type="number" id="people_count" name="people_count" min="1" max="20" value="<?= $pPeople ?: '2' ?>" required>
    </div>

    <div class="form-group">
        <label for="id_table">Select Table</label>
        <select id="id_table" name="id_table" required>
            <option value="">-- Choose a table --</option>
            <?php foreach ($tables as $table): ?>
                <option value="<?= $table['id_table'] ?>" <?= ((int)$pTable === (int)$table['id_table']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($table['name_table']) ?> (capacity: <?= $table['capacity'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="id_game">Choose a Game (optional)</label>
        <select id="id_game" name="id_game">
            <option value="0">-- No game --</option>
            <?php foreach ($games as $game): ?>
                <option value="<?= $game['id_game'] ?>">
                    <?= htmlspecialchars($game['name_game']) ?> (<?= $game['players_min'] ?>-<?= $game['players_max'] ?> players, <?= $game['duration'] ?> min)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn">Book Now</button>
    <a href="/Cafe-Aji-L3bo/reservations/availability" class="btn btn-secondary">Check Availability First</a>
</form>

<script>
(function() {
    const dateInput = document.getElementById('reservation_date');
    const timeInput = document.getElementById('reservation_time');
    const tableSelect = document.getElementById('id_table');
    const gameSelect = document.getElementById('id_game');

    function fetchAvailability() {
        const date = dateInput.value;
        const time = timeInput.value;

        if (!date || !time) return;

        // Save current selections
        const currentTable = tableSelect.value;
        const currentGame = gameSelect.value;

        // Fetch available tables
        fetch('/Cafe-Aji-L3bo/api/available-tables?date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time))
            .then(r => r.json())
            .then(tables => {
                tableSelect.innerHTML = '<option value="">-- Choose a table --</option>';
                tables.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id_table;
                    opt.textContent = t.name_table + ' (capacity: ' + t.capacity + ')';
                    if (String(t.id_table) === String(currentTable)) opt.selected = true;
                    tableSelect.appendChild(opt);
                });
            });

        // Fetch available games
        fetch('/Cafe-Aji-L3bo/api/available-games?date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time))
            .then(r => r.json())
            .then(games => {
                gameSelect.innerHTML = '<option value="0">-- No game --</option>';
                games.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id_game;
                    opt.textContent = g.name_game + ' (' + g.players_min + '-' + g.players_max + ' players, ' + g.duration + ' min)';
                    if (String(g.id_game) === String(currentGame)) opt.selected = true;
                    gameSelect.appendChild(opt);
                });
            });
    }

    dateInput.addEventListener('change', fetchAvailability);
    timeInput.addEventListener('change', fetchAvailability);
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>