<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <a href="<?= BASE_PATH ?>/admin/dashboard" class="btn-back">&#8592; Dashboard</a>
    <h1>&#128203; All Reservations</h1>
</div>

<?php
    $total     = count($reservations);
    $pending   = count(array_filter($reservations, fn($r) => $r['status_reservation'] === 'pending'));
    $confirmed = count(array_filter($reservations, fn($r) => $r['status_reservation'] === 'confirmed'));
    $cancelled = count(array_filter($reservations, fn($r) => $r['status_reservation'] === 'cancelled'));
?>

<?php if ($total > 0): ?>
<div class="res-summary-bar">
    <div class="res-sum-pill res-sum-all">
        <span class="res-sum-num"><?= $total ?></span>
        <span class="res-sum-lbl">Total</span>
    </div>
    <div class="res-sum-pill res-sum-pending">
        <span class="res-sum-num"><?= $pending ?></span>
        <span class="res-sum-lbl">Pending</span>
    </div>
    <div class="res-sum-pill res-sum-confirmed">
        <span class="res-sum-num"><?= $confirmed ?></span>
        <span class="res-sum-lbl">Confirmed</span>
    </div>
    <div class="res-sum-pill res-sum-cancelled">
        <span class="res-sum-num"><?= $cancelled ?></span>
        <span class="res-sum-lbl">Cancelled</span>
    </div>
</div>
<?php endif; ?>

<?php if (empty($reservations)): ?>
    <div class="empty-state">
        <p style="font-size:3rem;">&#128203;</p>
        <p>No reservations yet. Players will appear here once they book a table.</p>
    </div>
<?php else: ?>
    <div class="res-index-table-wrap">
        <table class="data-table res-index-table">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Table</th>
                    <th>Game</th>
                    <th>People</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                    <th>Session</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r):
                    $status       = $r['status_reservation'];
                    $endTime      = isset($r['reservation_end_time']) ? substr($r['reservation_end_time'], 0, 5) : '?';
                    $startTime    = substr($r['reservation_time'], 0, 5);
                    $activeSession = $sessionsByReservation[(int)$r['id_reservation']] ?? null;
                ?>
                    <tr class="res-row-<?= $status ?>">
                        <td>
                            <div class="res-player-cell">
                                <span class="res-player-name"><?= htmlspecialchars($r['name_user'] ?? 'N/A') ?></span>
                                <?php if (!empty($r['phone_number'])): ?>
                                    <span class="res-player-phone">&#128222; <?= htmlspecialchars($r['phone_number']) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($r['name_table'] ?? '-') ?></td>
                        <td><?= !empty($r['name_game']) ? htmlspecialchars($r['name_game']) : '<span class="muted">—</span>' ?></td>
                        <td><span class="res-people-badge">&#128101; <?= $r['people_count'] ?></span></td>
                        <td><span class="res-date"><?= $r['reservation_date'] ?></span></td>
                        <td>
                            <span class="res-time-slot">
                                <?= $startTime ?> <span class="res-time-arrow">&#8594;</span> <?= $endTime ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $status === 'confirmed' ? 'success' : ($status === 'cancelled' ? 'danger' : 'warning') ?>">
                                <?= $status ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($activeSession): ?>
                                <div class="session-inline">
                                    <span class="res-time-slot" style="font-size:0.75rem;">
                                        <?= substr($activeSession['start_time'], 11, 5) ?>
                                        (<?= $activeSession['elapsed_minutes'] ?>m)
                                    </span>
                                    <form action="<?= BASE_PATH ?>/sessions/<?= $activeSession['id_session'] ?>/end" method="POST" style="display:inline">
                                        <button type="submit" class="btn btn-small btn-danger"
                                                onclick="return confirm('End this session?')">&#9632; End</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($status === 'pending'): ?>
                                <div class="res-action-group">
                                    <form action="<?= BASE_PATH ?>/reservations/<?= $r['id_reservation'] ?>/status" method="POST" style="display:inline">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="btn btn-small btn-success" title="Confirm">&#10003; Confirm</button>
                                    </form>
                                    <form action="<?= BASE_PATH ?>/reservations/<?= $r['id_reservation'] ?>/status" method="POST" style="display:inline">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-small btn-danger" title="Cancel">&#10005;</button>
                                    </form>
                                </div>
                            <?php elseif ($status === 'confirmed'): ?>
                                <form action="<?= BASE_PATH ?>/reservations/<?= $r['id_reservation'] ?>/status" method="POST" style="display:inline">
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-small btn-danger" title="Cancel"
                                            onclick="return confirm('Cancel this confirmed reservation?')">&#10005; Cancel</button>
                                </form>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>