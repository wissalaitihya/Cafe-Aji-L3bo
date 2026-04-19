<?php
    $pDate    = $prefill['date']         ?? '';
    $pTime    = $prefill['time']         ?? '';
    $pEndTime = $prefill['end_time']     ?? '';
    $pTable   = $prefill['id_table']     ?? '';
    $pPeople  = $prefill['people_count'] ?? '';
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1>&#128197; Book a Table</h1>
</div>

<?php if (!empty($blockBooking) && !empty($existingRes)): ?>
    <?php $r = $existingRes; ?>
    <div class="alert alert-warning" style="border-left-color:#d97706;background:rgba(217,119,6,0.08)">
        <strong>&#9888; You already have an active reservation.</strong><br>
        You can only hold one reservation at a time.<br><br>
        <strong>Current booking:</strong>
        <?= htmlspecialchars($r['name_table'] ?? 'Table') ?> &mdash;
        <?= htmlspecialchars($r['name_game'] ?? 'Free play') ?> &mdash;
        <?= htmlspecialchars($r['reservation_date']) ?>
        <?= htmlspecialchars($r['reservation_time']) ?>&ndash;<?= htmlspecialchars($r['reservation_end_time'] ?? '') ?>
        &nbsp;
        <span class="badge badge-<?= $r['status_reservation'] === 'confirmed' ? 'success' : 'warning' ?>">
            <?= ucfirst($r['status_reservation']) ?>
        </span>
        <br><br>
        <?php if ($r['status_reservation'] === 'pending'): ?>
            <form action="<?= BASE_PATH ?>/reservations/<?= $r['id_reservation'] ?>/cancel" method="POST" style="display:inline"
                  onsubmit="return confirm('Cancel your current reservation so you can book a new one?')">
                <button type="submit" class="btn btn-danger btn-small">&#10005; Cancel my current reservation</button>
            </form>
            &nbsp;
        <?php endif; ?>
        <a href="<?= BASE_PATH ?>/reservations/my" class="btn btn-secondary btn-small">&#128203; View my reservations</a>
    </div>
<?php else: ?>

<?php if (!empty($error)): ?>
    <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= BASE_PATH ?>/reservations" method="POST" class="form-card">
    <div class="form-group">
        <label for="reservation_date">Date</label>
        <input type="date" id="reservation_date" name="reservation_date" value="<?= htmlspecialchars($pDate) ?>" min="<?= date('Y-m-d') ?>" required>
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
                <option value="<?= $game['id_game'] ?>"
                        data-min="<?= $game['players_min'] ?>"
                        data-max="<?= $game['players_max'] ?>">
                    <?= htmlspecialchars($game['name_game']) ?> (<?= $game['players_min'] ?>-<?= $game['players_max'] ?> players, <?= $game['duration'] ?> min)
                </option>
            <?php endforeach; ?>
        </select>
        <small class="field-hint text-warning" id="game-people-hint" style="display:none"></small>
    </div>

    <div class="form-group">
        <label for="people_count">Number of People</label>
        <input type="number" id="people_count" name="people_count" min="1" max="30" value="<?= $pPeople ?: '2' ?>" required>
        <small class="field-hint text-warning" id="people-hint" style="display:none"></small>
    </div>

    <!-- ── Game Recommendations ── -->
    <div id="recommend-box" class="recommend-box" style="display:none">
        <div class="recommend-header">&#129302; Suggested games for <strong id="recommend-count"></strong> players</div>
        <div id="recommend-list" class="recommend-list"></div>
    </div>

    <div class="form-group">
        <label for="id_table">Select Table</label>
        <div id="table-hint-row" style="display:none">
            <small class="field-hint" id="table-capacity-hint"></small>
        </div>
        <select id="id_table" name="id_table" required>
            <option value="">-- Choose a table --</option>
            <?php foreach ($tables as $table): ?>
                <option value="<?= $table['id_table'] ?>"
                        data-capacity="<?= $table['capacity'] ?>"
                        <?= ((int)$pTable === (int)$table['id_table']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($table['name_table']) ?> (capacity: <?= $table['capacity'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <small class="field-hint text-warning" id="table-people-hint" style="display:none"></small>
    </div>

    <!-- Validation error banner (shown above submit when blocked) -->
    <div id="submit-error" class="alert alert-error" style="display:none;margin-bottom:0.75rem"></div>

    <div class="form-actions">
        <button type="submit" id="submit-btn" class="btn btn-success">&#128197; Book Now</button>
        <a href="<?= BASE_PATH ?>/reservations/availability" class="btn btn-secondary">Check Availability</a>
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
    var gamePeopleHint  = document.getElementById('game-people-hint');
    var tablePeopleHint = document.getElementById('table-people-hint');
    var submitBtn    = document.getElementById('submit-btn');
    var submitError  = document.getElementById('submit-error');

    // ── Duration hint (start→end) ──────────────────────
    function updateDurationHint() {
        var start = timeInput.value;
        var end   = endTimeInput.value;
        if (start && end) {
            var startMins = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
            var endMins   = parseInt(end.split(':')[0])   * 60 + parseInt(end.split(':')[1]);
            var diff = endMins - startMins;
            if (diff > 0) {
                var h = Math.floor(diff / 60);
                var m = diff % 60;
                durationHint.textContent = 'Duration: ' + (h > 0 ? h + 'h ' : '') + (m > 0 ? m + 'm' : '');
                durationHint.style.display = '';
            } else {
                durationHint.textContent = diff < 0 ? '⚠ End time must be after start time.' : '';
                durationHint.className = 'field-hint text-danger';
                durationHint.style.display = diff < 0 ? '' : 'none';
            }
        }
        runValidation();
    }
    timeInput.addEventListener('change', updateDurationHint);
    endTimeInput.addEventListener('change', updateDurationHint);
    updateDurationHint();

    // ── People count vs game min/max ───────────────────
    function checkPeopleVsGame() {
        var gameOpt = gameSelect.options[gameSelect.selectedIndex];
        var min = parseInt(gameOpt.getAttribute('data-min') || '0');
        var max = parseInt(gameOpt.getAttribute('data-max') || '999');
        var people = parseInt(peopleInput.value) || 0;

        // Update native min so browser tooltip also works
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

        // Time check
        var start = timeInput.value, end = endTimeInput.value;
        var timeOk = true;
        if (start && end) {
            var s = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
            var e = parseInt(end.split(':')[0])   * 60 + parseInt(end.split(':')[1]);
            timeOk = e > s;
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
            submitError.innerHTML = '&#9888; Cannot book: ' + msgs.join(' ');
            submitError.style.display = '';
            submitError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });

    gameSelect.addEventListener('change', function() { runValidation(); fetchAvailability(); });
    peopleInput.addEventListener('input', runValidation);
    tableSelect.addEventListener('change', runValidation);

    // ── Fetch available tables when date/time/game changes ─
    function fetchAvailability() {
        var date    = dateInput.value;
        var time    = timeInput.value;
        var endTime = endTimeInput.value;
        var gameId  = gameSelect.value;

        if (!date || !time) return;

        var currentTable = tableSelect.value;
        var url = '<?= BASE_PATH ?>/api/available-tables?date=' + encodeURIComponent(date) +
                  '&time=' + encodeURIComponent(time) +
                  (endTime ? '&end_time=' + encodeURIComponent(endTime) : '') +
                  (gameId && gameId !== '0' ? '&game_id=' + encodeURIComponent(gameId) : '');

        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(tables) {
                tableSelect.innerHTML = '<option value="">-- Choose a table --</option>';
                tables.forEach(function(t) {
                    var opt = document.createElement('option');
                    opt.value = t.id_table;
                    opt.setAttribute('data-capacity', t.capacity);
                    opt.textContent = t.name_table + ' (capacity: ' + t.capacity + ')';
                    if (String(t.id_table) === String(currentTable)) opt.selected = true;
                    tableSelect.appendChild(opt);
                });

                var hintRow = document.getElementById('table-hint-row');
                var hintEl  = document.getElementById('table-capacity-hint');
                if (gameId && gameId !== '0') {
                    var gameOpt = gameSelect.options[gameSelect.selectedIndex];
                    var min = gameOpt.getAttribute('data-min');
                    var max = gameOpt.getAttribute('data-max');
                    hintEl.textContent = 'Tables shown have capacity between ' + min + ' and ' + max + " (this game's player range)";
                    hintRow.style.display = '';
                } else {
                    hintRow.style.display = 'none';
                }
                checkPeopleVsTable();
                runValidation();
            });

        // Also refresh games
        fetch('<?= BASE_PATH ?>/api/available-games?date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time))
            .then(function(r) { return r.json(); })
            .then(function(games) {
                var currentGame = gameSelect.value;
                gameSelect.innerHTML = '<option value="0">-- No game --</option>';
                games.forEach(function(g) {
                    var opt = document.createElement('option');
                    opt.value = g.id_game;
                    opt.setAttribute('data-min', g.players_min);
                    opt.setAttribute('data-max', g.players_max);
                    opt.textContent = g.name_game + ' (' + g.players_min + '–' + g.players_max + ' players, ' + g.duration + ' min)';
                    if (String(g.id_game) === String(currentGame)) opt.selected = true;
                    gameSelect.appendChild(opt);
                });
                checkPeopleVsGame();
                runValidation();
            });
    }

    dateInput.addEventListener('change', fetchAvailability);
    timeInput.addEventListener('change', fetchAvailability);
    endTimeInput.addEventListener('change', fetchAvailability);
    // Note: gameSelect change already handled above (runValidation + fetchAvailability)

    // ── Game Recommendations ──────────────────────────
    var recommendBox   = document.getElementById('recommend-box');
    var recommendList  = document.getElementById('recommend-list');
    var recommendCount = document.getElementById('recommend-count');
    var recommendTimer = null;

    function fetchRecommendations() {
        var n = parseInt(peopleInput.value) || 0;
        if (n < 1) { recommendBox.style.display = 'none'; return; }

        clearTimeout(recommendTimer);
        recommendTimer = setTimeout(function() {
            fetch('<?= BASE_PATH ?>/api/recommend?players=' + n)
                .then(function(r) { return r.json(); })
                .then(function(games) {
                    recommendList.innerHTML = '';
                    if (!games.length) { recommendBox.style.display = 'none'; return; }

                    recommendCount.textContent = n;
                    recommendBox.style.display = '';

                    games.forEach(function(g) {
                        var stars = '';
                        if (g.avg_stars > 0) {
                            for (var i = 1; i <= 5; i++) {
                                stars += '<span style="color:' + (i <= Math.round(g.avg_stars) ? '#f59e0b' : '#555') + '">★</span>';
                            }
                            stars += ' <span style="color:#aaa;font-size:0.78rem">(' + g.total_ratings + ')</span>';
                        }

                        var diffColor = g.difficulty === 'easy' ? '#22c55e' : g.difficulty === 'hard' ? '#ef4444' : '#f59e0b';
                        var html = '<div class="recommend-item" data-id="' + g.id_game + '" data-min="' + g.players_min + '" data-max="' + g.players_max + '">' +
                            '<div class="recommend-item-name">' + g.name_game + '</div>' +
                            '<div class="recommend-item-meta">' +
                                '<span>👥 ' + g.players_min + '–' + g.players_max + '</span>' +
                                '<span>⏰ ' + g.duration + 'm</span>' +
                                '<span style="color:' + diffColor + '">' + g.difficulty.charAt(0).toUpperCase() + g.difficulty.slice(1) + '</span>' +
                                (stars ? '<span class="recommend-stars">' + stars + '</span>' : '') +
                            '</div>' +
                        '</div>';
                        recommendList.innerHTML += html;
                    });

                    // Click a recommendation to pre-select that game
                    recommendList.querySelectorAll('.recommend-item').forEach(function(item) {
                        item.addEventListener('click', function() {
                            var gameId = item.dataset.id;
                            var opt = gameSelect.querySelector('option[value="' + gameId + '"]');
                            if (opt) {
                                gameSelect.value = gameId;
                                runValidation();
                                item.style.borderColor = 'var(--purple)';
                            }
                        });
                    });
                });
        }, 300);
    }

    peopleInput.addEventListener('input', fetchRecommendations);
    fetchRecommendations();
})();
</script>

<?php endif; // end blockBooking check ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
