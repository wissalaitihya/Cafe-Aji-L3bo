<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Active Sessions</h1>
<a href="/Cafe-Aji-L3bo/sessions/create" class="btn btn-success">+ Start Session</a>
<a href="/Cafe-Aji-L3bo/sessions/history" class="btn btn-secondary">View History</a>

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
                        <span class="badge badge-<?= $colorClass ?>">
                            <?= floor($minutes / 60) ?>h <?= $minutes % 60 ?>m
                        </span>
                    </td>
                    <td>
                        <form action="/Cafe-Aji-L3bo/sessions/<?= $s['id_session'] ?>/end" method="POST" onsubmit="return confirm('End this session?')">
                            <button type="submit" class="btn btn-small btn-danger">End</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>