<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>My Reservations</h1>
<a href="/Cafe-Aji-L3bo/reservations/create" class="btn">+ Book a Table</a>

<?php if (empty($reservations)): ?>
    <p>You have no reservations yet.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Table</th>
                <th>Game</th>
                <th>People</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= $r['reservation_date'] ?></td>
                    <td><?= $r['reservation_time'] ?></td>
                    <td><?= htmlspecialchars($r['name_table'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['name_game'] ?? '-') ?></td>
                    <td><?= $r['people_count'] ?></td>
                    <td>
                        <span class="badge badge-<?= $r['status_reservation'] === 'confirmed' ? 'success' : ($r['status_reservation'] === 'cancelled' ? 'danger' : 'warning') ?>">
                            <?= $r['status_reservation'] ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>