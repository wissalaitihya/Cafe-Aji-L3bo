<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Welcome, <?= htmlspecialchars($userName) ?>!</h1>

<div class="quick-links">
    <a href="/Cafe-Aji-L3bo/games" class="btn">Browse Games</a>
    <a href="/Cafe-Aji-L3bo/reservations/create" class="btn btn-success">Book a Table</a>
    <a href="/Cafe-Aji-L3bo/reservations/availability" class="btn btn-secondary">Check Availability</a>
</div>

<h2>My Upcoming Reservations</h2>

<?php
    $upcoming = array_filter($myReservations, function($r) {
        return $r['reservation_date'] >= date('Y-m-d') && $r['status_reservation'] !== 'cancelled';
    });
?>

<?php if (empty($upcoming)): ?>
    <p>No upcoming reservations. <a href="/Cafe-Aji-L3bo/reservations/create">Book a table now!</a></p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr><th>Date</th><th>Time</th><th>Table</th><th>Game</th><th>People</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($upcoming as $r): ?>
                <tr>
                    <td><?= $r['reservation_date'] ?></td>
                    <td><?= $r['reservation_time'] ?></td>
                    <td><?= htmlspecialchars($r['name_table'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['name_game'] ?? '-') ?></td>
                    <td><?= $r['people_count'] ?></td>
                    <td>
                        <span class="badge badge-<?= $r['status_reservation'] === 'confirmed' ? 'success' : 'warning' ?>">
                            <?= $r['status_reservation'] ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="/Cafe-Aji-L3bo/reservations/my">View all my reservations &rarr;</a></p>

<?php require __DIR__ . '/../layout/footer.php'; ?>