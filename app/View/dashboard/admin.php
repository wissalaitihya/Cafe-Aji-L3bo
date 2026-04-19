<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <div>
        <h1>&#128187; Admin Dashboard</h1>
        <p class="subtitle">Welcome back! Here&rsquo;s what&rsquo;s happening at Aji L3bo Caf&eacute;.</p>
    </div>
</div>

<!-- ── KPI Row ── -->
<div class="kpi-row">
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon">🎮</div>
        <div class="kpi-body"><div class="kpi-value"><?= $totalGames ?></div><div class="kpi-label">Total Games</div></div>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-icon">📅</div>
        <div class="kpi-body"><div class="kpi-value"><?= count($todayReservations) ?></div><div class="kpi-label">Today&rsquo;s Reservations</div></div>
    </div>
    <div class="kpi-card kpi-purple">
        <div class="kpi-icon">▶</div>
        <div class="kpi-body"><div class="kpi-value"><?= count($activeSessions) ?></div><div class="kpi-label">Active Sessions</div></div>
    </div>
    <div class="kpi-card kpi-orange">
        <div class="kpi-icon">🪑</div>
        <div class="kpi-body"><div class="kpi-value"><?= $tableStats['occupied'] ?>/<?= $tableStats['total'] ?></div><div class="kpi-label">Tables Occupied</div></div>
    </div>
    <div class="kpi-card kpi-red">
        <div class="kpi-icon">⏳</div>
        <div class="kpi-body"><div class="kpi-value"><?= $pendingCount ?></div><div class="kpi-label">Pending Approvals</div></div>
    </div>
</div>

<!-- ── Two-column row: Quick Actions + Month Stats ── -->
<div class="admin-two-col">

    <!-- Quick Actions -->
    <div class="admin-panel">
        <div class="admin-panel-title">⚡ Quick Actions</div>
        <div class="action-grid">
            <a href="<?= BASE_PATH ?>/games"        class="action-btn"><span class="action-icon">🎮</span><span>Games</span></a>
            <a href="<?= BASE_PATH ?>/tables"       class="action-btn"><span class="action-icon">🪑</span><span>Tables</span></a>
            <a href="<?= BASE_PATH ?>/reservations" class="action-btn"><span class="action-icon">📋</span><span>Reservations</span></a>
            <a href="<?= BASE_PATH ?>/sessions"     class="action-btn"><span class="action-icon">▶</span><span>Sessions</span></a>
            <a href="<?= BASE_PATH ?>/admin/stats"  class="action-btn action-btn-purple"><span class="action-icon">📊</span><span>Statistics</span></a>
            <a href="<?= BASE_PATH ?>/sessions/create" class="action-btn action-btn-green"><span class="action-icon">➕</span><span>Start Session</span></a>
            <a href="<?= BASE_PATH ?>/games/create" class="action-btn action-btn-blue"><span class="action-icon">➕</span><span>Add Game</span></a>
            <a href="<?= BASE_PATH ?>/reservations" class="action-btn action-btn-orange">
                <?php if ($pendingCount > 0): ?>
                    <span class="action-icon" style="position:relative">⏳<span class="action-badge"><?= $pendingCount ?></span></span>
                <?php else: ?>
                    <span class="action-icon">✅</span>
                <?php endif; ?>
                <span>Approve</span>
            </a>
        </div>
    </div>

    <!-- Month Stats + Top Stats -->
    <div class="admin-stats-col">
        <div class="admin-panel">
            <div class="admin-panel-title">📊 This Month</div>
            <div class="month-stats-grid">
                <div class="month-stat ms-total">
                    <div class="ms-value"><?= $monthStats['total'] ?? 0 ?></div>
                    <div class="ms-label">Total</div>
                </div>
                <div class="month-stat ms-confirmed">
                    <div class="ms-value"><?= $monthStats['confirmed'] ?? 0 ?></div>
                    <div class="ms-label">Confirmed</div>
                </div>
                <div class="month-stat ms-pending">
                    <div class="ms-value"><?= $monthStats['pending'] ?? 0 ?></div>
                    <div class="ms-label">Pending</div>
                </div>
                <div class="month-stat ms-cancelled">
                    <div class="ms-value"><?= $monthStats['cancelled'] ?? 0 ?></div>
                    <div class="ms-label">Cancelled</div>
                </div>
            </div>
        </div>
        <div class="admin-panel" style="margin-top:1rem">
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
                    <span class="top-stat-value badge badge-success"><?= $gameStats['available'] ?? $totalGames ?></span>
                </li>
                <li>
                    <span class="top-stat-label">Free tables</span>
                    <span class="top-stat-value badge badge-success"><?= $tableStats['free'] ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- ── Games Currently Being Played ── -->
<?php if (!empty($inUseGames)): ?>
<div class="admin-panel" style="margin-top:1.5rem">
    <div class="admin-panel-title" style="color:#f59e0b">🎮 Games Currently In Use (<?= count($inUseGames) ?>)</div>
    <div class="in-use-grid">
        <?php foreach ($inUseGames as $g): ?>
        <div class="in-use-card">
            <div class="in-use-dot"></div>
            <div class="in-use-name"><?= htmlspecialchars($g['name_game']) ?></div>
            <span class="badge" style="background:rgba(245,158,11,0.15);color:#d97706;border:1px solid rgba(245,158,11,0.3)">In Use</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── Active Sessions ── -->
<?php if (!empty($activeSessions)): ?>
<div class="admin-panel" style="margin-top:1.5rem">
    <div class="admin-panel-title">▶ Active Sessions (<?= count($activeSessions) ?>)</div>
    <div class="active-sessions-list">
        <?php foreach ($activeSessions as $s): ?>
        <div class="active-session-row">
            <div class="as-game">🎮 <?= htmlspecialchars($s['name_game'] ?? 'Free play') ?></div>
            <div class="as-meta">
                <span>🪑 <?= htmlspecialchars($s['name_table'] ?? '-') ?></span>
                <span>👤 <?= htmlspecialchars($s['name_user'] ?? '-') ?></span>
                <span>⏱ <?= $s['elapsed_minutes'] ?? 0 ?>m</span>
            </div>
            <form action="<?= BASE_PATH ?>/sessions/<?= $s['id_session'] ?>/end" method="POST"
                  onsubmit="return confirm('End session?')">
                <button type="submit" class="btn btn-small btn-danger">■ End</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>