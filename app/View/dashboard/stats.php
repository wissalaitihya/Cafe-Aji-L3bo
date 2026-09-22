<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <div>
        <h1>&#128202; Statistics</h1>
        <p class="subtitle">Bookings, peak hours, popular games and table usage.</p>
    </div>
    <div class="dashboard-header-actions">
        <a href="<?= BASE_PATH ?>/admin/dashboard" class="btn btn-secondary">&#8592; Dashboard</a>
    </div>
</div>

<!-- ── KPI Row (all time) ── -->
<div class="kpi-row">
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon">📋</div>
        <div class="kpi-body"><div class="kpi-value"><?= (int)$total ?></div><div class="kpi-label">Total Reservations</div></div>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-icon">✅</div>
        <div class="kpi-body"><div class="kpi-value"><?= (int)$confirmed ?></div><div class="kpi-label">Confirmed</div></div>
    </div>
    <div class="kpi-card kpi-orange">
        <div class="kpi-icon">⏳</div>
        <div class="kpi-body"><div class="kpi-value"><?= (int)$pending ?></div><div class="kpi-label">Pending</div></div>
    </div>
    <div class="kpi-card kpi-red">
        <div class="kpi-icon">❌</div>
        <div class="kpi-body"><div class="kpi-value"><?= (int)$cancelled ?></div><div class="kpi-label">Cancelled</div></div>
    </div>
</div>

<!-- ── Two-column row: This Month + Top Stats ── -->
<div class="admin-two-col">
    <div class="admin-panel">
        <div class="admin-panel-title">📊 This Month</div>
        <div class="month-stats-grid">
            <div class="month-stat ms-total">
                <div class="ms-value"><?= (int)($monthStats['total'] ?? 0) ?></div>
                <div class="ms-label">Total</div>
            </div>
            <div class="month-stat ms-confirmed">
                <div class="ms-value"><?= (int)($monthStats['confirmed'] ?? 0) ?></div>
                <div class="ms-label">Confirmed</div>
            </div>
            <div class="month-stat ms-pending">
                <div class="ms-value"><?= (int)($monthStats['pending'] ?? 0) ?></div>
                <div class="ms-label">Pending</div>
            </div>
            <div class="month-stat ms-cancelled">
                <div class="ms-value"><?= (int)($monthStats['cancelled'] ?? 0) ?></div>
                <div class="ms-label">Cancelled</div>
            </div>
        </div>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-title">🏆 Top Stats</div>
        <ul class="top-stat-list">
            <?php if (!empty($gameStats['popular'])): ?>
            <li>
                <span class="top-stat-label">Most reserved</span>
                <span class="top-stat-value">🎮 <?= htmlspecialchars($gameStats['popular']['name_game']) ?></span>
            </li>
            <?php endif; ?>
            <?php if (!empty($tableStats['mostUsed'])): ?>
            <li>
                <span class="top-stat-label">Most used table</span>
                <span class="top-stat-value">🪑 <?= htmlspecialchars($tableStats['mostUsed']['name_table']) ?></span>
            </li>
            <?php endif; ?>
            <li>
                <span class="top-stat-label">Available games</span>
                <span class="top-stat-value badge badge-success"><?= (int)($gameStats['available'] ?? 0) ?></span>
            </li>
            <li>
                <span class="top-stat-label">Free tables</span>
                <span class="top-stat-value badge badge-success"><?= (int)($tableStats['free'] ?? 0) ?></span>
            </li>
        </ul>
    </div>
</div>

<!-- ── Two-column row: Most Booked + Peak Hours ── -->
<div class="admin-two-col" style="margin-top:1.5rem">
    <div class="admin-panel">
        <div class="admin-panel-title">🎮 Most Booked Games</div>
        <?php
            $maxBooked = 0;
            foreach (($mostBooked ?? []) as $mb) { $maxBooked = max($maxBooked, (int)$mb['bookings']); }
        ?>
        <?php if (empty($mostBooked)): ?>
            <p class="muted">No confirmed bookings yet.</p>
        <?php else: ?>
        <ul class="top-stat-list">
            <?php foreach ($mostBooked as $mb):
                $pct = $maxBooked > 0 ? round(((int)$mb['bookings'] / $maxBooked) * 100) : 0;
            ?>
            <li style="display:block">
                <div style="display:flex;justify-content:space-between;gap:.5rem">
                    <span class="top-stat-label"><?= htmlspecialchars($mb['name_game']) ?></span>
                    <span class="top-stat-value"><?= (int)$mb['bookings'] ?> booking<?= ((int)$mb['bookings'] !== 1) ? 's' : '' ?></span>
                </div>
                <div style="height:8px;border-radius:4px;background:rgba(99,102,241,.12);margin-top:.35rem">
                    <div style="height:100%;width:<?= $pct ?>%;border-radius:4px;background:#818cf8"></div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-title">⏰ Peak Hours (confirmed)</div>
        <?php
            $peakList = [];
            foreach (($peakHours ?? []) as $h => $c) {
                if ((int)$c > 0) { $peakList[(int)$h] = (int)$c; }
            }
            arsort($peakList);
            $peakList = array_slice($peakList, 0, 8, true);
            $maxPeak = $peakList ? max($peakList) : 0;
        ?>
        <?php if (empty($peakList)): ?>
            <p class="muted">No confirmed bookings yet.</p>
        <?php else: ?>
        <ul class="top-stat-list">
            <?php foreach ($peakList as $h => $c):
                $pct = $maxPeak > 0 ? round(($c / $maxPeak) * 100) : 0;
            ?>
            <li style="display:block">
                <div style="display:flex;justify-content:space-between;gap:.5rem">
                    <span class="top-stat-label"><?= sprintf('%02d:00', $h) ?></span>
                    <span class="top-stat-value"><?= $c ?> booking<?= ($c !== 1) ? 's' : '' ?></span>
                </div>
                <div style="height:8px;border-radius:4px;background:rgba(169,117,61,.12);margin-top:.35rem">
                    <div style="height:100%;width:<?= $pct ?>%;border-radius:4px;background:#c98a3d"></div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</div>

<!-- ── Two-column row: Last 7 Days + Table Occupancy ── -->
<div class="admin-two-col" style="margin-top:1.5rem">
    <div class="admin-panel">
        <div class="admin-panel-title">📅 Last 7 Days</div>
        <ul class="top-stat-list">
            <?php foreach (($last7Days ?? []) as $d): ?>
            <li>
                <span class="top-stat-label"><?= htmlspecialchars(date('D, M j', strtotime($d['day']))) ?></span>
                <span class="top-stat-value"><?= (int)$d['total'] ?> total · <?= (int)$d['confirmed'] ?> conf. · <?= (int)$d['cancelled'] ?> canc.</span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-title">🪑 Table Occupancy (confirmed)</div>
        <ul class="top-stat-list">
            <?php foreach (($tableOccupancy ?? []) as $t):
                $hours = round(((int)($t['total_minutes'] ?? 0)) / 60, 1);
            ?>
            <li>
                <span class="top-stat-label"><?= htmlspecialchars($t['name_table']) ?> <span class="muted">(<?= (int)$t['capacity'] ?> seats)</span></span>
                <span class="top-stat-value"><?= (int)$t['total_bookings'] ?> bookings · <?= $hours ?>h</span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
