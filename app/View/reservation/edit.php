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
    // Current table/game snapshot: the live-availability APIs can't see the
    // booking being edited, so they would hide its own slot — re-add if missing.
    $curTable = null;
    foreach (($tables ?? []) as $t) {
        if ((int)$t['id_table'] === (int)$pTable) { $curTable = $t; break; }
    }
    $curGame = null;
    foreach (($games ?? []) as $g) {
        if ((int)$g['id_game'] === (int)$pGame) { $curGame = $g; break; }
    }
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

    gameSelect.addEventListener('change', function() { runValidation(); fetchAvailabilityDebounced(); });
    peopleInput.addEventListener('input', runValidation);
    tableSelect.addEventListener('change', runValidation);

    // Snapshot of the booking being edited (the availability APIs exclude it).
    var origTableId   = '<?= (int)($form['id_table'] ?? 0) ?>';
    var origTableName = <?= json_encode($curTable['name_table'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var origTableCap  = <?= (int)($curTable['capacity'] ?? 0) ?>;
    var origGameId    = '<?= (int)($form['id_game'] ?? 0) ?>';
    var origGameName  = <?= json_encode($curGame['name_game'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var origGameMin   = <?= (int)($curGame['players_min'] ?? 0) ?>;
    var origGameMax   = <?= (int)($curGame['players_max'] ?? 0) ?>;
    var origGameDur   = <?= (int)($curGame['duration'] ?? 0) ?>;

    // ── Fetch available tables when date/time/game changes (debounced 250ms) ─
    var availTimer = null;
    function fetchAvailabilityDebounced() {
        clearTimeout(availTimer);
        availTimer = setTimeout(fetchAvailability, 250);
    }
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
                // Keep the booking's own table selectable (the API excludes it).
                if (origTableId && origTableId !== '0' && !tableSelect.querySelector('option[value="' + origTableId + '"]')) {
                    var keepT = document.createElement('option');
                    keepT.value = origTableId;
                    keepT.setAttribute('data-capacity', origTableCap);
                    keepT.textContent = origTableName + ' (capacity: ' + origTableCap + ') — current';
                    if (String(origTableId) === String(currentTable)) keepT.selected = true;
                    tableSelect.appendChild(keepT);
                }

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
                // Keep the booking's own game selectable (the API excludes it).
                if (origGameId && origGameId !== '0' && !gameSelect.querySelector('option[value="' + origGameId + '"]')) {
                    var keepG = document.createElement('option');
                    keepG.value = origGameId;
                    keepG.setAttribute('data-min', origGameMin);
                    keepG.setAttribute('data-max', origGameMax);
                    keepG.textContent = origGameName + ' (' + origGameMin + '–' + origGameMax + ' players, ' + origGameDur + ' min) — current';
                    if (String(origGameId) === String(currentGame)) keepG.selected = true;
                    gameSelect.appendChild(keepG);
                }
                checkPeopleVsGame();
                runValidation();
            });
    }

    dateInput.addEventListener('change', fetchAvailabilityDebounced);
    timeInput.addEventListener('change', fetchAvailabilityDebounced);
    endTimeInput.addEventListener('change', fetchAvailabilityDebounced);
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

<?php require __DIR__ . '/../layout/footer.php'; ?>
