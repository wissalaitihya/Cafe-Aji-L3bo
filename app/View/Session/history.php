<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Session History</h1>
<a href="<?= BASE_PATH ?>/sessions" class="btn btn-secondary">&larr; Active Sessions</a>

<?php if (empty($sessions)): ?>
    <p>No past sessions.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Table</th>
                <th>Game</th>
                <th>Player</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= $s['end_time'] ?></td>
                    <td><?= htmlspecialchars($s['name_table'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($s['name_game'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($s['name_user'] ?? '-') ?></td>
                    <td><?= $s['duration'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>