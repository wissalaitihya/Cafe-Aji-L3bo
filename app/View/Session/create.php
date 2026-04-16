<?php
require __DIR__ . '/../layout/header.php';
?>
<h1>Create New Session</h1>
<form action="/Cafe-Aji-L3bo/sessions" method="POST" class="form-card">
    <div class="form-group">
        <label for="id_reservation">Reservation (optional)</label>
        <select id="id_reservation" name="id_reservation">
            <option value="">-- No reservation --</option>
            <?php foreach ($reservations as $r): ?>
                <option value="<?= $r['id_reservation'] ?>"
                        data-game="<?= $r['id_game'] ?? '' ?>"
                        data-table="<?= $r['id_table'] ?>">
                    #<?= $r['id_reservation'] ?> - <?= htmlspecialchars($r['name_user'] ?? '') ?>
                    (<?= $r['reservation_date'] ?> <?= $r['reservation_time'] ?>)
                    — <?= htmlspecialchars($r['name_table'] ?? '') ?>
                    <?= $r['name_game'] ? '— ' . htmlspecialchars($r['name_game']) : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
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

    <button type="submit" class="btn">Start Session</button>
    <a href="/Cafe-Aji-L3bo/sessions" class="btn btn-secondary">Cancel</a>
</form>
<script>
document.getElementById('id_reservation').addEventListener('change', 
function() {
    var opt = this.options[this.selectedIndex];
    var gameId = opt.getAttribute('data-game');
    var tableId = opt.getAttribute('data-table');

    if (gameId) {
        var gameSelect = document.getElementById('id_game');
        for (var i = 0; i < gameSelect.options.length; i++) {
            if (gameSelect.options[i].value === gameId) {
                gameSelect.selectedIndex = i;
                break;
            }
        }
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
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
