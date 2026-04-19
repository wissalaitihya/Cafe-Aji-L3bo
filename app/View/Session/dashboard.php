<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1>&#9654; Active Sessions</h1>
    <div>
        <a href="<?= BASE_PATH ?>/sessions/create" class="btn btn-success">+ Start Session</a>
        <a href="<?= BASE_PATH ?>/sessions/history" class="btn btn-secondary">View History</a>
    </div>
</div>

<?php if (empty($sessions)): ?>
    <div class="empty-state"><p>No active sessions right now.</p></div>
<?php else: ?>

<!-- ── Session Cards with Visual Timers ── -->
<div class="session-cards-grid">
    <?php foreach ($sessions as $s):
        $endDatetime = (!empty($s['reservation_date']) && !empty($s['reservation_end_time']))
            ? $s['reservation_date'] . 'T' . $s['reservation_end_time']
            : '';
        $durationMins = $endDatetime
            ? round((strtotime($endDatetime) - strtotime($s['start_time'])) / 60)
            : 0;
    ?>
    <div class="session-card">
        <!-- Radial countdown timer -->
        <div class="timer-ring-wrap">
            <svg class="timer-ring" viewBox="0 0 120 120" width="120" height="120">
                <circle class="timer-ring-bg" cx="60" cy="60" r="52"/>
                <circle class="timer-ring-progress" cx="60" cy="60" r="52"
                        data-start="<?= htmlspecialchars($s['start_time']) ?>"
                        data-end="<?= htmlspecialchars($endDatetime) ?>"
                        data-duration="<?= $durationMins ?>"/>
            </svg>
            <div class="timer-ring-label">
                <div class="timer-time" data-start="<?= htmlspecialchars($s['start_time']) ?>"
                     data-end="<?= htmlspecialchars($endDatetime) ?>">…</div>
                <div class="timer-sublabel" data-start="<?= htmlspecialchars($s['start_time']) ?>"
                     data-end="<?= htmlspecialchars($endDatetime) ?>"></div>
            </div>
        </div>

        <!-- Session info -->
        <div class="session-card-info">
            <div class="session-card-title">
                <span class="session-table">&#127918; <?= htmlspecialchars($s['name_table'] ?? '-') ?></span>
                <span class="session-game muted"><?= htmlspecialchars($s['name_game'] ?? 'Free play') ?></span>
            </div>
            <div class="session-card-meta">
                <span>&#128100; <?= htmlspecialchars($s['name_user'] ?? '-') ?></span>
                <span>&#9200; Started <?= substr($s['start_time'], 11, 5) ?></span>
                <?php if ($endDatetime): ?>
                    <span>&#128679; Ends <?= substr($s['reservation_end_time'], 0, 5) ?></span>
                <?php endif; ?>
            </div>

            <!-- Pulsing overtime warning (shown by JS when time is up) -->
            <div class="overtime-alert" style="display:none" data-session="<?= $s['id_session'] ?>">
                &#9888; Time&apos;s up &mdash; please end this session!
            </div>

            <form action="<?= BASE_PATH ?>/sessions/<?= $s['id_session'] ?>/end" method="POST"
                  onsubmit="return confirm('End session for <?= htmlspecialchars(addslashes($s['name_table'] ?? 'this table')) ?>?')">
                <button type="submit" class="btn btn-danger btn-small end-btn"
                        data-session="<?= $s['id_session'] ?>">&#9632; End Session</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<script>
(function() {
    'use strict';

    var CIRCUMFERENCE = 2 * Math.PI * 52; // r=52

    function formatTime(totalSeconds) {
        if (totalSeconds <= 0) return '00:00';
        var h = Math.floor(totalSeconds / 3600);
        var m = Math.floor((totalSeconds % 3600) / 60);
        var s = totalSeconds % 60;
        if (h > 0) return h + ':' + pad(m) + ':' + pad(s);
        return pad(m) + ':' + pad(s);
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function updateAll() {
        var now = Date.now();

        document.querySelectorAll('.timer-ring-progress').forEach(function(circle) {
            var startMs   = new Date(circle.dataset.start.replace(' ', 'T')).getTime();
            var endMs     = circle.dataset.end ? new Date(circle.dataset.end).getTime() : 0;
            var totalMs   = endMs ? (endMs - startMs) : 0;
            var elapsedMs = now - startMs;

            var pct = totalMs > 0 ? Math.min(elapsedMs / totalMs, 1) : 0;
            var dashOffset = CIRCUMFERENCE * (1 - pct);

            circle.style.strokeDasharray  = CIRCUMFERENCE;
            circle.style.strokeDashoffset = dashOffset;

            // Color: green → amber → red as time runs out
            if (pct >= 1) {
                circle.style.stroke = '#ef4444';
            } else {
                var hue = Math.round(120 * (1 - pct));
                circle.style.stroke = 'hsl(' + hue + ', 90%, 55%)';
            }
        });

        document.querySelectorAll('.timer-time').forEach(function(label) {
            var startMs = new Date(label.dataset.start.replace(' ', 'T')).getTime();
            var endMs   = label.dataset.end ? new Date(label.dataset.end).getTime() : 0;
            var elapsedMs = now - startMs;
            var remainMs  = endMs ? (endMs - now) : 0;

            if (endMs && now >= endMs) {
                label.textContent = 'OVER';
                label.style.color = '#ef4444';
            } else if (endMs) {
                label.textContent = formatTime(Math.floor(remainMs / 1000));
                label.style.color = remainMs < 300000 ? '#ef4444' : remainMs < 900000 ? '#f59e0b' : '#22c55e';
            } else {
                label.textContent = formatTime(Math.floor(elapsedMs / 1000));
                label.style.color = '#a78bfa';
            }
        });

        document.querySelectorAll('.timer-sublabel').forEach(function(sub) {
            var endMs = sub.dataset.end ? new Date(sub.dataset.end).getTime() : 0;
            if (!endMs) { sub.textContent = 'elapsed'; return; }
            if (now >= endMs) {
                sub.textContent = 'overtime!';
                sub.style.color = '#ef4444';
            } else {
                sub.textContent = (endMs - now) < 300000 ? 'urgent' : 'remaining';
            }
        });

        // Overtime alerts + pulsing End button
        document.querySelectorAll('.overtime-alert').forEach(function(alert) {
            var card   = alert.closest('.session-card');
            var circle = card ? card.querySelector('.timer-ring-progress') : null;
            var endMs  = circle && circle.dataset.end ? new Date(circle.dataset.end).getTime() : 0;
            if (endMs && now >= endMs) {
                alert.style.display = '';
                var btn = card.querySelector('.end-btn');
                if (btn) btn.classList.add('pulse');
            }
        });
    }

    updateAll();
    setInterval(updateAll, 1000);

    // Auto-reload 5s after first session expires (triggers server-side autoEndOverdue)
    setInterval(function() {
        var expired = false;
        document.querySelectorAll('.timer-ring-progress').forEach(function(c) {
            if (c.dataset.end && Date.now() >= new Date(c.dataset.end).getTime() + 5000) expired = true;
        });
        if (expired) location.reload();
    }, 10000);
}());
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
