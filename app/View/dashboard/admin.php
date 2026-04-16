<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $totalGames ?></h3>
        <p>Total Games</p>
    </div>
    <div class="stat-card">
        <h3><?= count($todayReservations) ?></h3>
        <p>Today's Reservations</p>
    </div>
    <div class="stat-card">
        <h3><?= count($allReservations) ?></h3>
        <p>All Reservations</p>
    </div>
    <div class="stat-card">
        <h3><?= count($activeSessions) ?></h3>
        <p>Active Sessions</p>
    </div>
    <div class="stat-card">
        <h3>
            <?php
                $occupied = 0;
                foreach ($tables as $t) { if ($t['status_table'] === 'occupied') $occupied++; }
                echo $occupied . '/' . count($tables);
            ?>
        </h3>
        <p>Tables Occupied</p>
    </div>
</div>

<div class="quick-links">
    <a href="<?= BASE ?>/games" class="btn">Manage Games</a>
    <a href="<?= BASE ?>/reservations" class="btn">Manage Reservations</a>
    <a href="<?= BASE ?>/sessions" class="btn">Sessions Dashboard</a>
    <a href="<?= BASE ?>/sessions/create" class="btn btn-success">Start Session</a>
</div>

<h2>All Reservations</h2>
<?php if (empty($allReservations)): ?>
    <p>No reservations yet.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr><th>Player</th><th>Table</th><th>Game</th><th>Date</th><th>Time</th><th>People</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($allReservations as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name_user'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['name_table'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['name_game'] ?? '-') ?></td>
                    <td><?= $r['reservation_date'] ?></td>
                    <td><?= $r['reservation_time'] ?></td>
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