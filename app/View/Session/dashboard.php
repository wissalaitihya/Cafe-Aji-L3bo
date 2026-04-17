<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Active Sessions</h1>
<a href="<?= BASE_PATH ?>/sessions/create" class="btn btn-success">+ Start Session</a>
<a href="<?= BASE_PATH ?>/sessions/history" class="btn btn-secondary">View History</a>

<?php if (empty($sessions)): ?>
    <p>No active sessions right now.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Table</th>
                <th>Game</th>
                <th>Player</th>
                <th>Started</th>
                <th>Elapsed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sessions as $s): ?>
                <?php
                    $minutes = $s['elapsed_minutes'] ?? 0;
                    $colorClass = $minutes < 60 ? 'success' : ($minutes < 120 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td><?= htmlspecialchars($s['name_table'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($s['name_game'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($s['name_user'] ?? '-') ?></td>
                    <td><?= $s['start_time'] ?></td>
                    <td>
                        <span class="badge badge-<?= $colorClass ?> elapsed-badge"
                         data-start="<?= htmlspecialchars($s['start_time']) ?>">
                            <?= floor($minutes / 60) ?>h <?= $minutes % 60 ?>m 0s
                        </span>
                    </td>
                    <td>
                        <form action="<?= BASE_PATH ?>/sessions/<?= $s['id_session'] ?>/end" method="POST" onsubmit="return confirm('End this session?')">
                            <button type="submit" class="btn btn-small btn-danger">End</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<script>
function updateElapsed() {
    document.querySelectorAll('.elapsed-badge').forEach(function(badge) {
        var start = new Date(badge.dataset.start.replace(' ', 'T'));
        var now = new Date();
        var diffMs = now - start;
        if (diffMs < 0) diffMs = 0;
        var totalSeconds = Math.floor(diffMs / 1000);
        var totalMinutes = Math.floor(totalSeconds / 60);
        var h = Math.floor(totalMinutes / 60);
        var m = totalMinutes % 60;
        var s = totalSeconds % 60;

        badge.textContent = h + 'h ' + m + 'm ' + s + 's';
        badge.className = 'badge elapsed-badge ' +
            (totalMinutes < 60 ? 'badge-success' : totalMinutes < 120 ? 'badge-warning' : 'badge-danger');
    });
}

updateElapsed();
setInterval(updateElapsed, 1000);
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>