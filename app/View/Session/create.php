<?php
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <a href="<?= BASE_PATH ?>/sessions" class="btn-back">&#8592; Active Sessions</a>
    <h1>&#9654; Start New Session</h1>
</div>
<form action="<?= BASE_PATH ?>/sessions" method="POST" class="form-card">
    <div class="form-group">
        <label for="id_reservation">Reservation</label>
        <?php if (empty($reservations)): ?>
            <p class="alert alert-error" style="margin:0">No active reservations right now. Reservations appear here only during their time slot.</p>
        <?php else: ?>
        <select id="id_reservation" name="id_reservation" required>
            <option value="">-- Select a reservation --</option>
            <?php foreach ($reservations as $r): ?>
                <option value="<?= $r['id_reservation'] ?>"
                        data-game="<?= $r['id_game'] ?? '' ?>"
                        data-table="<?= $r['id_table'] ?>">
                    #<?= $r['id_reservation'] ?> - <?= htmlspecialchars($r['name_user'] ?? '') ?>
                    (<?= $r['reservation_date'] ?> <?= substr($r['reservation_time'], 0, 5) ?> → <?= substr($r['reservation_end_time'], 0, 5) ?>)
                    — <?= htmlspecialchars($r['name_table'] ?? '') ?>
                    <?= $r['name_game'] ? '— ' . htmlspecialchars($r['name_game']) : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="id_game">Game</label>
        <select id="id_game" name="id_game" required>
            <option value="">-- Select a game --</option>
            <?php foreach ($games as $game): ?>
                <option value="<?= $game['id_game'] ?>"><?= htmlspecialchars($game['name_game']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="id_table">Table</label>
        <select id="id_table" name="id_table" required>
            <option value="">-- Select a table --</option>
            <?php foreach ($tables as $table): ?>
                <option value="<?= $table['id_table'] ?>">
                    <?= htmlspecialchars($table['name_table']) ?> (capacity: <?= $table['capacity'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success" <?= empty($reservations) ? 'disabled title="No active reservation to start a session"' : '' ?>>&#9654; Start Session</button>
        <a href="<?= BASE_PATH ?>/sessions" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<script>
var gamesMap = <?= json_encode($gamesByReservation ?? []) ?>;

var resSelect = document.getElementById('id_reservation');
if (resSelect) {
    resSelect.addEventListener('change', function() {
        var opt     = this.options[this.selectedIndex];
        var resId   = parseInt(this.value) || 0;
        var gameId  = opt.getAttribute('data-game');
        var tableId = opt.getAttribute('data-table');

        var gameSelect = document.getElementById('id_game');

        var allowedIds = gamesMap[resId] ? gamesMap[resId].map(String) : null;
        for (var i = 0; i < gameSelect.options.length; i++) {
            var optVal = gameSelect.options[i].value;
            if (optVal === '') {
                gameSelect.options[i].hidden = false;
            } else {
                gameSelect.options[i].hidden = allowedIds !== null && allowedIds.indexOf(optVal) === -1;
            }
        }

        if (gameId) {
            for (var i = 0; i < gameSelect.options.length; i++) {
                if (gameSelect.options[i].value === gameId) {
                    gameSelect.selectedIndex = i;
                    break;
                }
            }
        } else {
            gameSelect.selectedIndex = 0;
        }

        if (tableId) {
            var tableSelect = document.getElementById('id_table');
            for (var i = 0; i < tableSelect.options.length; i++) {
                if (tableSelect.options[i].value === tableId) {
                    tableSelect.selectedIndex = i;
                    break;
                }
            }
        }
    });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
