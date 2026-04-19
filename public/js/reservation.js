/**
 * Reservation form: dynamic logic
 * - Fetches available tables when date / start time / end time changes
 * - Filters tables by game's min player count (capacity >= players_min)
 * - Warns if people_count > selected table capacity
 * - Warns if people_count is outside selected game's min/max range
 * - Shows game capacity hint when a game is selected
 */
(function () {
    'use strict';

    var dateInput      = document.getElementById('reservation_date');
    var timeInput      = document.getElementById('reservation_time');
    var endTimeInput   = document.getElementById('reservation_end_time');
    var tableSelect    = document.getElementById('id_table');
    var gameSelect     = document.getElementById('id_game');
    var peopleInput    = document.getElementById('people_count');
    var gameHint       = document.getElementById('game-hint');
    var gamePlayerWarn = document.getElementById('game-player-warn');
    var capWarn        = document.getElementById('capacity-warn');
    var submitBtn      = document.getElementById('submit-btn');

    if (!dateInput || !timeInput || !tableSelect || !gameSelect) return;

    // Game ID to restore after API rebuild (from PHP prefill)
    var prefillGameId = gameSelect.dataset.prefillGame || '0';

    /* ── Auto-set end time to +2h when start time is chosen ── */
    timeInput.addEventListener('change', function () {
        if (endTimeInput && this.value && !endTimeInput.value) {
            var parts = this.value.split(':');
            var h = parseInt(parts[0], 10) + 2;
            var m = parts[1] || '00';
            if (h >= 24) h = 23;
            endTimeInput.value = (h < 10 ? '0' + h : h) + ':' + m;
        }
        fetchAvailability();
    });

    /* ── Fetch availability ── */
    function fetchAvailability() {
        var date    = dateInput.value;
        var time    = timeInput.value;
        var endTime = endTimeInput ? endTimeInput.value : '';

        if (!date || !time) return;

        var currentGame  = gameSelect.value !== '0' ? gameSelect.value : prefillGameId;
        var currentTable = tableSelect.value;

        fetch(BASE_PATH + '/api/available-games?date=' + encodeURIComponent(date) + '&time=' + encodeURIComponent(time))
            .then(function (r) { return r.json(); })
            .then(function (games) {
                gameSelect.innerHTML = '<option value="0">-- No game --</option>';
                games.forEach(function (g) {
                    var opt = document.createElement('option');
                    opt.value = g.id_game;
                    opt.textContent = g.name_game + ' (' + g.players_min + '\u2013' + g.players_max + ' players, ' + g.duration + ' min)';
                    opt.dataset.min = g.players_min;
                    opt.dataset.max = g.players_max;
                    if (String(g.id_game) === String(currentGame)) opt.selected = true;
                    gameSelect.appendChild(opt);
                });
                // Clear prefill after first successful restore
                prefillGameId = '0';
                updateGameHint();
                checkGamePlayers();
                fetchTables(date, time, endTime, currentTable, gameSelect.value);
            });
    }

    /* ── Fetch available tables ── */
    function fetchTables(date, time, endTime, currentTable, gameId) {
        var url = BASE_PATH + '/api/available-tables?date=' + encodeURIComponent(date)
                + '&time=' + encodeURIComponent(time)
                + (endTime ? '&end_time=' + encodeURIComponent(endTime) : '');

        if (gameId && gameId !== '0') {
            url += '&game_id=' + encodeURIComponent(gameId);
        }

        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (tables) {
                var prevVal = tableSelect.value || currentTable;
                tableSelect.innerHTML = '<option value="">-- Choose a table --</option>';

                if (tables.length === 0) {
                    var noOpt = document.createElement('option');
                    noOpt.disabled = true;
                    noOpt.textContent = 'No tables available for this slot';
                    tableSelect.appendChild(noOpt);
                } else {
                    tables.forEach(function (t) {
                        var opt = document.createElement('option');
                        opt.value = t.id_table;
                        opt.dataset.capacity = t.capacity;
                        opt.textContent = t.name_table + ' (capacity: ' + t.capacity + ')';
                        if (String(t.id_table) === String(prevVal)) opt.selected = true;
                        tableSelect.appendChild(opt);
                    });
                }
                checkCapacity();
            });
    }

    /* ── Game player range warning (client-side mirror of server check) ── */
    function checkGamePlayers() {
        if (!gamePlayerWarn || !peopleInput) return;
        var people = parseInt(peopleInput.value, 10) || 0;
        var opt = gameSelect.options[gameSelect.selectedIndex];

        if (!opt || !opt.value || opt.value === '0' || !opt.dataset.min) {
            gamePlayerWarn.style.display = 'none';
            updateSubmitState();
            return;
        }

        var min = parseInt(opt.dataset.min, 10);
        var max = parseInt(opt.dataset.max, 10);
        var name = opt.textContent.split('(')[0].trim();

        if (people < min || people > max) {
            gamePlayerWarn.textContent = '\u26a0 "' + name + '" requires ' + min + '\u2013' + max + ' players. Please update the number of people.';
            gamePlayerWarn.style.display = 'block';
            peopleInput.focus();
            peopleInput.select();
            if (submitBtn) submitBtn.disabled = true;
        } else {
            gamePlayerWarn.style.display = 'none';
            updateSubmitState();
        }
    }

    /* ── Live capacity warning (table seats vs people) ── */
    function checkCapacity() {
        if (!capWarn || !peopleInput) return;
        var people = parseInt(peopleInput.value, 10) || 0;
        var opt = tableSelect.options[tableSelect.selectedIndex];
        if (!opt || !opt.value) {
            capWarn.style.display = 'none';
            updateSubmitState();
            return;
        }
        var cap = parseInt(opt.dataset.capacity, 10) || 0;
        if (cap > 0 && people > cap) {
            capWarn.textContent = '\u26a0 This table seats ' + cap + ' but you have ' + people + ' people. Please choose a bigger table.';
            capWarn.style.display = 'block';
            if (submitBtn) submitBtn.disabled = true;
        } else {
            capWarn.style.display = 'none';
            updateSubmitState();
        }
    }

    /* ── Merge disabled state from both checks ── */
    function updateSubmitState() {
        if (!submitBtn) return;
        var gameWarnVisible = gamePlayerWarn && gamePlayerWarn.style.display !== 'none';
        var capWarnVisible  = capWarn && capWarn.style.display !== 'none';
        submitBtn.disabled = gameWarnVisible || capWarnVisible;
    }

    /* ── Game hint (filter info) ── */
    function updateGameHint() {
        if (!gameHint) return;
        var opt = gameSelect.options[gameSelect.selectedIndex];
        if (opt && opt.value && opt.value !== '0' && opt.dataset.min) {
            gameHint.style.display = 'block';
            gameHint.textContent = '\u2139 Tables shown have capacity between ' + opt.dataset.min + ' and ' + opt.dataset.max + ' (this game\'s player range). Tables shown are filtered.';
        } else {
            gameHint.style.display = 'none';
        }
    }

    /* ── Event listeners ── */
    dateInput.addEventListener('change', fetchAvailability);
    if (endTimeInput) endTimeInput.addEventListener('change', fetchAvailability);

    gameSelect.addEventListener('change', function () {
        updateGameHint();
        checkGamePlayers();
        var date    = dateInput.value;
        var time    = timeInput.value;
        var endTime = endTimeInput ? endTimeInput.value : '';
        if (date && time) {
            fetchTables(date, time, endTime, tableSelect.value, gameSelect.value);
        }
    });

    tableSelect.addEventListener('change', checkCapacity);

    if (peopleInput) {
        peopleInput.addEventListener('input', function () {
            checkGamePlayers();
            checkCapacity();
        });
    }

    if (dateInput.value && timeInput.value) {
        fetchAvailability();
    }
}());
