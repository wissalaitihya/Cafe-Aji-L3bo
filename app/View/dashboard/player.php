<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <div>
        <h1>&#127968; Welcome, <?= htmlspecialchars($userName) ?>!</h1>
        <p class="subtitle">Ready to play? Browse games or manage your reservations.</p>
    </div>
    <div class="dashboard-header-actions">
        <a href="<?= BASE_PATH ?>/reservations/create" class="btn btn-success">&#43; Book a Table</a>
        <a href="<?= BASE_PATH ?>/games" class="btn btn-secondary">&#127918; Browse Games</a>
    </div>
</div>

<?php
    $today     = date('Y-m-d');
    $upcoming  = array_filter($myReservations, function($r) use ($today) {
        return $r['reservation_date'] >= $today && $r['status_reservation'] !== 'cancelled';
    });
    $confirmed = array_filter($myReservations, function($r) { return $r['status_reservation'] === 'confirmed'; });
    $pending   = array_filter($myReservations, function($r) { return $r['status_reservation'] === 'pending'; });
    $cancelled = array_filter($myReservations, function($r) { return $r['status_reservation'] === 'cancelled'; });
    $allCount  = count($myReservations);
?>

<!-- Player KPI strip -->
<div class="player-kpi-strip">
    <div class="pkpi pkpi-blue">
        <div class="pkpi-val"><?= count($upcoming) ?></div>
        <div class="pkpi-lbl">Upcoming</div>
    </div>
    <div class="pkpi pkpi-green">
        <div class="pkpi-val"><?= count($confirmed) ?></div>
        <div class="pkpi-lbl">Confirmed</div>
    </div>
    <div class="pkpi pkpi-orange">
        <div class="pkpi-val"><?= count($pending) ?></div>
        <div class="pkpi-lbl">Pending</div>
    </div>
    <div class="pkpi pkpi-red">
        <div class="pkpi-val"><?= count($cancelled) ?></div>
        <div class="pkpi-lbl">Cancelled</div>
    </div>
    <div class="pkpi pkpi-purple">
        <div class="pkpi-val"><?= $allCount ?></div>
        <div class="pkpi-lbl">All Bookings</div>
    </div>
</div>

<!-- Upcoming Reservations -->
<div class="section-block">
    <div class="section-block-header">
        <h2>&#9200; Upcoming Reservations</h2>
        <a href="<?= BASE_PATH ?>/reservations/my" class="btn btn-small btn-secondary">View All</a>
    </div>
    <?php if (empty($upcoming)): ?>
        <div class="empty-state" style="padding:1.5rem">
            <p>No upcoming reservations. <a href="<?= BASE_PATH ?>/reservations/create">Book a table now!</a></p>
        </div>
    <?php else: ?>
        <div class="upcoming-list">
            <?php foreach (array_slice($upcoming, 0, 5) as $r):
                $endT = !empty($r['reservation_end_time']) ? substr($r['reservation_end_time'], 0, 5) : null;
                $startT = substr($r['reservation_time'], 0, 5);
                $isToday = ($r['reservation_date'] === $today);
            ?>
            <div class="upcoming-card status-border-<?= $r['status_reservation'] ?>">
                <div class="upcoming-card-left">
                    <div class="upcoming-game-name"><?= htmlspecialchars($r['name_game'] ?? 'Free play') ?></div>
                    <div class="upcoming-meta">
                        <span class="um-date <?= $isToday ? 'um-today' : '' ?>">
                            <?= $isToday ? '📅 Today' : '📅 ' . date('M j, Y', strtotime($r['reservation_date'])) ?>
                        </span>
                        <span>⏰ <?= $startT ?><?= $endT ? ' → ' . $endT : '' ?></span>
                        <span>🪑 <?= htmlspecialchars($r['name_table'] ?? '-') ?></span>
                        <span>👥 <?= $r['people_count'] ?> people</span>
                    </div>
                </div>
                <div class="upcoming-card-right">
                    <span class="badge badge-<?= $r['status_reservation'] === 'confirmed' ? 'success' : 'warning' ?>">
                        <?= ucfirst($r['status_reservation']) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Featured Games -->
<?php if (!empty($featuredGames)): ?>
<div class="section-block">
    <div class="section-block-header">
        <h2>&#127918; Featured Games</h2>
        <a href="<?= BASE_PATH ?>/games" class="btn btn-small btn-secondary">See All</a>
    </div>
    <div class="card-grid">
        <?php foreach ($featuredGames as $g): ?>
            <div class="card game-card">
                <?php if (!empty($g['image_game'])): ?>
                    <div class="card-image">
                        <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($g['image_game']) ?>"
                             alt="<?= htmlspecialchars($g['name_game']) ?>">
                    </div>
                <?php else: ?>
                    <div class="card-image-placeholder">&#127918;</div>
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= htmlspecialchars($g['name_game']) ?></h3>
                    <div class="game-info-row">
                        <span>&#128101; <?= $g['players_min'] ?>&ndash;<?= $g['players_max'] ?></span>
                        <span>&#9200; <?= $g['duration'] ?>m</span>
                        <span class="badge badge-<?= $g['difficulty'] === 'easy' ? 'success' : ($g['difficulty'] === 'hard' ? 'danger' : 'warning') ?>"><?= ucfirst($g['difficulty']) ?></span>
                    </div>
                    <div class="card-actions">
                        <a href="<?= BASE_PATH ?>/games/<?= $g['id_game'] ?>" class="btn btn-small">Details</a>
                        <a href="<?= BASE_PATH ?>/reservations/create" class="btn btn-small btn-success">Book</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>