<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1>&#128203; My Reservations</h1>
    <a href="<?= BASE_PATH ?>/reservations/create" class="btn btn-success">&#43; Book a Table</a>
</div>

<?php if (!empty($_GET['cancelled'])): ?>
    <div class="alert alert-success">&#10003; Your reservation has been cancelled.</div>
<?php endif; ?>
<?php if (!empty($_GET['error']) && $_GET['error'] === 'session_started'): ?>
    <div class="alert alert-error">&#10007; Cannot cancel &mdash; your session has already started. Ask staff to end it.</div>
<?php endif; ?>

<?php if (empty($reservations)): ?>
    <div class="empty-state"><p>You have no reservations yet. <a href="<?= BASE_PATH ?>/reservations/create">Book one now!</a></p></div>
<?php else: ?>

<!-- Summary strip -->
<?php
    $total     = count($reservations);
    $confirmed = count(array_filter($reservations, function($r){ return $r['status_reservation']==='confirmed'; }));
    $pending   = count(array_filter($reservations, function($r){ return $r['status_reservation']==='pending'; }));
    $cancelled = count(array_filter($reservations, function($r){ return $r['status_reservation']==='cancelled'; }));
?>
<div class="res-summary-bar" style="margin-bottom:1.25rem">
    <div class="res-sum-pill res-sum-all">
        <span class="res-sum-num"><?= $total ?></span><span class="res-sum-lbl">Total</span>
    </div>
    <div class="res-sum-pill res-sum-confirmed">
        <span class="res-sum-num"><?= $confirmed ?></span><span class="res-sum-lbl">Confirmed</span>
    </div>
    <div class="res-sum-pill res-sum-pending">
        <span class="res-sum-num"><?= $pending ?></span><span class="res-sum-lbl">Pending</span>
    </div>
    <div class="res-sum-pill res-sum-cancelled">
        <span class="res-sum-num"><?= $cancelled ?></span><span class="res-sum-lbl">Cancelled</span>
    </div>
</div>

<div class="my-res-list">
    <?php foreach ($reservations as $r):
        $status  = $r['status_reservation'];
        $startT  = substr($r['reservation_time'], 0, 5);
        $endT    = !empty($r['reservation_end_time']) ? substr($r['reservation_end_time'], 0, 5) : null;
        $dateStr = date('D, M j Y', strtotime($r['reservation_date']));
        $today   = date('Y-m-d');
        $isToday = ($r['reservation_date'] === $today);
        $isPast  = ($r['reservation_date'] < $today);
    ?>
    <div class="my-res-card status-border-<?= $status ?> <?= $isPast ? 'res-past' : '' ?>">
        <div class="my-res-card-main">
            <!-- Left: game name + status badge -->
            <div class="my-res-game">
                <span class="my-res-game-name">&#127918; <?= htmlspecialchars($r['name_game'] ?? 'Free play') ?></span>
                <span class="badge badge-<?= $status === 'confirmed' ? 'success' : ($status === 'cancelled' ? 'danger' : 'warning') ?>">
                    <?= ucfirst($status) ?>
                </span>
            </div>
            <!-- Meta row -->
            <div class="my-res-meta">
                <span class="<?= $isToday ? 'meta-today' : '' ?>">
                    &#128197; <?= $isToday ? '<strong>Today</strong>' : $dateStr ?>
                </span>
                <span>
                    &#9200; <?= $startT ?>
                    <?php if ($endT): ?>
                        <span class="time-arrow">&#8594;</span> <?= $endT ?>
                    <?php endif; ?>
                </span>
                <span>&#129681; <?= htmlspecialchars($r['name_table'] ?? '-') ?></span>
                <span>&#128101; <?= (int)$r['people_count'] ?> people</span>
            </div>
        </div>
        <!-- Action -->
        <div class="my-res-card-action">
            <?php if (in_array($status, ['pending', 'confirmed'])): ?>
                <form method="POST" action="<?= BASE_PATH ?>/reservations/<?= $r['id_reservation'] ?>/cancel"
                      onsubmit="return confirm('Cancel this reservation?')">
                    <button type="submit" class="btn btn-small btn-danger">&#10005; Cancel</button>
                </form>
            <?php else: ?>
                <span class="muted text-sm">—</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>