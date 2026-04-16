<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>All Reservations</h1>

<?php if (empty($reservations)): ?>
    <p>No reservations yet.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Player</th>
                <th>Phone</th>
                <th>Table</th>
                <th>Game</th>
                <th>People</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= $r['id_reservation'] ?></td>
                    <td><?= htmlspecialchars($r['name_user'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($r['phone_number'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['name_table'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['name_game'] ?? '-') ?></td>
                    <td><?= $r['people_count'] ?></td>
                    <td><?= $r['reservation_date'] ?></td>
                    <td><?= $r['reservation_time'] ?></td>
                    <td>
                        <span class="badge badge-<?= $r['status_reservation'] === 'confirmed' ? 'success' : ($r['status_reservation'] === 'cancelled' ? 'danger' : 'warning') ?>">
                            <?= $r['status_reservation'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($r['status_reservation'] === 'pending'): ?>
                            <form action="<?= BASE ?>/reservations/<?= $r['id_reservation'] ?>/status" method="POST" style="display:inline">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-small btn-success">Confirm</button>
                            </form>
                            <form action="<?= BASE ?>/reservations/<?= $r['id_reservation'] ?>/status" method="POST" style="display:inline">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-small btn-danger">Cancel</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>