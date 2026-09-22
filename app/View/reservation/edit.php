<?php
    $pDate    = $form['date']         ?? '';
    $pTime    = $form['time']         ?? '';
    $pEndTime = $form['end_time']     ?? '';
    $pTable   = $form['id_table']     ?? '';
    $pPeople  = $form['people_count'] ?? '';
    $pGame    = $form['id_game']      ?? 0;
    $backUrl  = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
        ? BASE_PATH . '/reservations'
        : BASE_PATH . '/reservations/my';
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <a href="<?= $backUrl ?>" class="btn-back">&#8592; Reservations</a>
    <h1>&#9998; Modify Reservation</h1>
</div>

<?php if (!empty($reservation)): ?>
    <p class="page-intro">
        Currently:
        <strong><?= htmlspecialchars($reservation['name_table'] ?? 'Table') ?></strong> &mdash;
        <?= htmlspecialchars($reservation['name_game'] ?? 'Free play') ?> &mdash;
        <?= htmlspecialchars($reservation['reservation_date'] ?? '') ?>
        <?= htmlspecialchars(substr($reservation['reservation_time'] ?? '', 0, 5)) ?>&ndash;<?= htmlspecialchars(substr($reservation['reservation_end_time'] ?? '', 0, 5)) ?>
        &nbsp;
        <span class="badge badge-<?= ($reservation['status_reservation'] ?? '') === 'confirmed' ? 'success' : 'warning' ?>">
            <?= htmlspecialchars(ucfirst($reservation['status_reservation'] ?? '')) ?>
        </span>
    </p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= BASE_PATH ?>/reservations/<?= (int)$reservation['id_reservation'] ?>/update" method="POST" class="form-card">
    <?= \Core\Csrf::field() ?>
    <div class="form-group">
        <label for="reservation_date">Date</label>
        <input type="date" id="reservation_date" name="reservation_date" value="<?= htmlspecialchars($pDate) ?>" min="<?= date('Y-m-d') ?>" required>
        <small class="field-hint" id="date-hint">&#128197; Select the date first, then choose the start &amp; end times.</small>
    </div>

    <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label for="reservation_time">Start Time</label>
            <input type="time" id="reservation_time" name="reservation_time" value="<?= htmlspecialchars($pTime) ?>" required>
        </div>
        <div class="form-group">
            <label for="reservation_end_time">End Time</label>
            <input type="time" id="reservation_end_time" name="reservation_end_time" value="<?= htmlspecialchars($pEndTime) ?>" required>
            <small class="field-hint" id="duration-hint"></small>
        </div>
    </div>

    <div class="form-group">
        <label for="id_game">Choose a Game <span class="muted">(optional)</span></label>
        <select id="id_game" name="id_game">
            <option value="0">-- No game --</option>
            <?php foreach ($games as $game): ?>
                <option value="<?= (int)$game['id_game'] ?>"
                        data-min="<?= $game['players_min'] ?>"
                    data-max="<?= $game['players_max'] ?>"
                    <?= ((int)$pGame === (int)$game['id_game']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($game['name_game']) ?> (<?= (int)$game['players_min'] ?>-<?= (int)$game['players_max'] ?> players, <?= (int)$game['duration'] ?> min)
                </option>
            <?php endforeach; ?>
        </select>
        <small class="field-hint text-warning" id="game-people-hint" style="display:none"></small>
    </div>

    <div class="form-group">
        <label for="people_count">Number of People</label>
        <input type="number" id="people_count" name="people_count" min="1" max="30" value="<?= htmlspecialchars((string)($pPeople ?: '2')) ?>" required>
        <small class="field-hint text-warning" id="people-hint" style="display:none"></small>
    </div>

    <div class="form-group">
        <label for="id_table">Select Table</label>
        <div id="table-hint-row" style="display:none">
            <small class="field-hint" id="table-capacity-hint"></small>
        </div>
        <select id="id_table" name="id_table" required>
            <option value="">-- Choose a table --</option>
            <?php foreach ($tables as $table): ?>
                <option value="<?= (int)$table['id_table'] ?>"
                        data-capacity="<?= $table['capacity'] ?>"
                        <?= ((int)$pTable === (int)$table['id_table']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($table['name_table']) ?> (capacity: <?= (int)$table['capacity'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <small class="field-hint text-warning" id="table-people-hint" style="display:none"></small>
    </div>

    <!-- Validation error banner (shown above submit when blocked) -->
    <div id="submit-error" class="alert alert-error" style="display:none;margin-bottom:0.75rem"></div>

    <div class="form-actions">
        <button type="submit" id="submit-btn" class="btn btn-success">&#10003; Save Changes</button>
        <a href="<?= $backUrl ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
(function() {
    var dateInput    = document.getElementById('reservation_date');
    var timeInput    = document.getElementById('reservation_time');
    var endTimeInput = document.getElementById('reservation_end_time');
    var tableSelect  = document.getElementById('id_table');
    var gameSelect   = document.getElementById('id_game');
    var peopleInput  = document.getElementById('people_count');
    var durationHint = document.getElementById('duration-hint');
    var dateHint     = document.getElementById('date-hint');
    var gamePeopleHint  = document.getElementById('game-people-hint');
    var tablePeopleHint = document.getElementById('table-people-hint');
    var submitBtn    = document.getElementById('submit-btn');
    var submitError  = document.getElementById('submit-error');

    // ── Date first: block time inputs until a date is chosen ──
    function updateDateGate() {
        var locked = !dateInput.value;
        timeInput.disabled    = locked;
        endTimeInput.disabled = locked;
        if (locked) {
            dateHint.textContent = 'Select the date first, then choose the start & end times.';
            dateHint.className = 'field-hint';
            dateHint.style.display = '';
        } else {
            dateHint.style.display = 'none';
        }
        runValidation();
    }
    dateInput.addEventListener('change', updateDateGate);

    // ── Duration hint (start→end) ──────────────────────
    function updateDurationHint() {
        var start = timeInput.value;
        var end   = endTimeInput.value;
        if (start && end) {
            var startMins = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
            var endMins   = parseInt(end.split(':')[0])   * 60 + parseInt(end.split(':')[1]);
            var diff = endMins - startMins;
            durationHint.className = 'field-hint';
            if (diff >= 30) {
                var h = Math.floor(diff / 60);
                var m = diff % 60;
                durationHint.textContent = 'Duration: ' + (h > 0 ? h + 'h ' : '') + (m > 0 ? m + 'm' : '');
                durationHint.style.display = '';
            } else if (diff > 0) {
                durationHint.className = 'field-hint text-danger';
                durationHint.textContent = '⚠ Booking must be at least 30 minutes.';
                durationHint.style.display = '';
            } else {
                durationHint.className = 'field-hint text-danger';
                durationHint.textContent = '⚠ End time must be after start time.';
                durationHint.style.display = '';
            }
        }
        runValidation();
    }
    timeInput.addEventListener('change', updateDurationHint);
    endTimeInput.addEventListener('change', updateDurationHint);
    updateDurationHint();
    updateDateGate();

    // ── People count vs game min/max ───────────────────
    function checkPeopleVsGame() {
        var gameOpt = gameSelect.options[gameSelect.selectedIndex];
        var min = parseInt(gameOpt.getAttribute('data-min') || '0');
        var max = parseInt(gameOpt.getAttribute('data-max') || '999');
        var people = parseInt(peopleInput.value) || 0;

        if (gameSelect.value !== '0') {
            peopleInput.min = min;
            peopleInput.max = max;
        } else {
            peopleInput.min = 1;
            peopleInput.removeAttribute('max');
        }

        if (gameSelect.value === '0' || !people) {
            gamePeopleHint.style.display = 'none';
            return true;
        }
        if (people < min) {
            gamePeopleHint.innerHTML = '&#9888; This game requires <strong>at least ' + min + ' players</strong>. Please increase the number of people to ' + min + ' or more.';
            gamePeopleHint.className = 'field-hint text-danger';
            gamePeopleHint.style.display = '';
            return false;
        } else if (people > max) {
            gamePeopleHint.innerHTML = '&#9888; This game supports <strong>max ' + max + ' players</strong>. Please reduce your group to ' + max + ' or fewer, or choose a different game.';
            gamePeopleHint.className = 'field-hint text-danger';
            gamePeopleHint.style.display = '';
            return false;
        } else {
            gamePeopleHint.innerHTML = '&#10004; Player count fits this game (' + min + '&ndash;' + max + ' players).';
            gamePeopleHint.className = 'field-hint text-success';
            gamePeopleHint.style.display = '';
            return true;
        }
    }

    // ── People count vs table capacity ────────────────
    function checkPeopleVsTable() {
        var tableOpt = tableSelect.options[tableSelect.selectedIndex];
        var cap = parseInt(tableOpt.getAttribute('data-capacity') || '0');
        var people = parseInt(peopleInput.value) || 0;

        if (!tableSelect.value || !people) {
            tablePeopleHint.style.display = 'none';
            return true;
        }
        if (people > cap) {
            tablePeopleHint.innerHTML = '&#9888; <strong>' + tableOpt.textContent.trim().split('(')[0].trim() + '</strong> only seats <strong>' + cap + ' people</strong>. Please choose a table with enough capacity or reduce your group size.';
            tablePeopleHint.className = 'field-hint text-danger';
            tablePeopleHint.style.display = '';
            return false;
        } else {
            tablePeopleHint.style.display = 'none';
            return true;
        }
    }

    // ── Master validation → enable/disable submit ─────
    function runValidation() {
        var gameOk  = checkPeopleVsGame();
        var tableOk = checkPeopleVsTable();

        var start = timeInput.value, end = endTimeInput.value;
        var datePicked = !!dateInput.value;
        var timeOk = datePicked;
        if (datePicked && start && end) {
            var s = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
            var e = parseInt(end.split(':')[0])   * 60 + parseInt(end.split(':')[1]);
            timeOk = (e - s) >= 30;
        }

        var allOk = gameOk && tableOk && timeOk;
        submitBtn.disabled = !allOk;
        submitBtn.style.opacity = allOk ? '' : '0.5';
        submitBtn.style.cursor  = allOk ? '' : 'not-allowed';
        submitError.style.display = 'none';
        return allOk;
    }

    // ── Block form submit as final safety net ─────────
    document.querySelector('form.form-card').addEventListener('submit', function(e) {
        if (!runValidation()) {
            e.preventDefault();
            var msgs = [];
            var gameOpt = gameSelect.options[gameSelect.selectedIndex];
            var gameMin = parseInt(gameOpt.getAttribute('data-min') || '0');
            var gameMax = parseInt(gameOpt.getAttribute('data-max') || '999');
            var people  = parseInt(peopleInput.value) || 0;
            if (gameSelect.value !== '0' && people < gameMin)
                msgs.push('Number of people must be at least ' + gameMin + ' for this game.');
            if (gameSelect.value !== '0' && people > gameMax)
                msgs.push('Number of people must be at most ' + gameMax + ' for this game.');
            var tableOpt = tableSelect.options[tableSelect.selectedIndex];
            var cap = parseInt(tableOpt.getAttribute('data-capacity') || '0');
            if (tableSelect.value && people > cap)
                msgs.push('Table capacity (' + cap + ') is less than your group size (' + people + ').');
            if (!dateInput.value)
                msgs.push('Select a date first.');
            if (start && end) {
                var s2 = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
                var e2 = parseInt(end.split(':')[0])   * 60 + parseInt(end.split(':')[1]);
                if (e2 - s2 >= 0 && e2 - s2 < 30)
                    msgs.push('Booking must be at least 30 minutes.');
                else if (e2 - s2 < 0)
                    msgs.push('End time must be after start time.');
            }
            submitError.innerHTML = '&#9888; Cannot save: ' + msgs.join(' ');
            submitError.style.display = '';
            submitError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });

    gameSelect.addEventListener('change', runValidation);
    peopleInput.addEventListener('input', runValidation);
    tableSelect.addEventListener('change', runValidation);
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
