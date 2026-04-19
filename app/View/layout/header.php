<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aji L3bo Café</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/css/style.css">
</head>
<body>
<?php
    $userRole = $_SESSION['user_role'] ?? 'guest';
    if ($userRole === 'admin')      $logoHref = BASE_PATH . '/admin/dashboard';
    elseif ($userRole === 'player') $logoHref = BASE_PATH . '/player/dashboard';
    else                            $logoHref = BASE_PATH . '/games';

    $cp = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    function nav_active(string $seg, string $cp): string {
        return (strpos($cp, $seg) !== false) ? ' active' : '';
    }
?>

<?php if ($userRole === 'guest'): ?>
<!-- ════ PUBLIC LAYOUT (no sidebar) ════ -->
<div class="public-layout">
    <header class="public-topbar">
        <a href="<?= BASE_PATH ?>/games" class="public-logo">🎲 <span>Aji L3bo</span></a>
        <nav class="public-nav">
            <a href="<?= BASE_PATH ?>/games"    class="public-nav-link<?= nav_active('/games', $cp) ?>">🎮 Games</a>
            <a href="<?= BASE_PATH ?>/login"    class="public-nav-link<?= nav_active('/login', $cp) ?>">Login</a>
            <a href="<?= BASE_PATH ?>/register" class="btn btn-primary" style="font-size:0.82rem;padding:0.4rem 1.1rem">Register</a>
        </nav>
    </header>
    <main class="public-content">

<?php else: ?>
<!-- ════ APP LAYOUT (sidebar) ════ -->
<div class="app-layout">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="<?= $logoHref ?>" class="sidebar-logo">🎲 <span>Aji L3bo</span></a>
            <button class="sidebar-toggle-btn" id="sidebar-toggle" title="Collapse">&#10094;</button>
        </div>

        <nav class="sidebar-nav">
            <?php if ($userRole === 'admin'): ?>
                <span class="sidebar-section-title">Admin</span>
                <a href="<?= BASE_PATH ?>/admin/dashboard" class="sidebar-link<?= nav_active('/admin/dashboard', $cp) ?>"><span class="si">🏠</span><span class="sl">Dashboard</span></a>
                <a href="<?= BASE_PATH ?>/admin/stats"     class="sidebar-link<?= nav_active('/admin/stats', $cp) ?>"><span class="si">📊</span><span class="sl">Statistics</span></a>
                <span class="sidebar-section-title">Manage</span>
                <a href="<?= BASE_PATH ?>/games"        class="sidebar-link<?= nav_active('/games', $cp) ?>"><span class="si">🎮</span><span class="sl">Games</span></a>
                <a href="<?= BASE_PATH ?>/tables"       class="sidebar-link<?= nav_active('/tables', $cp) ?>"><span class="si">🪑</span><span class="sl">Tables</span></a>
                <a href="<?= BASE_PATH ?>/reservations" class="sidebar-link<?= nav_active('/reservations', $cp) ?>"><span class="si">📋</span><span class="sl">Reservations</span></a>
                <a href="<?= BASE_PATH ?>/sessions"     class="sidebar-link<?= nav_active('/sessions', $cp) ?>"><span class="si">▶</span><span class="sl">Sessions</span></a>
            <?php else: /* player */ ?>
                <span class="sidebar-section-title">Player</span>
                <a href="<?= BASE_PATH ?>/player/dashboard"    class="sidebar-link<?= nav_active('/player/dashboard', $cp) ?>"><span class="si">🏠</span><span class="sl">Dashboard</span></a>
                <span class="sidebar-section-title">Play</span>
                <a href="<?= BASE_PATH ?>/games"               class="sidebar-link<?= nav_active('/games', $cp) ?>"><span class="si">🎮</span><span class="sl">Games</span></a>
                <a href="<?= BASE_PATH ?>/reservations/create" class="sidebar-link<?= nav_active('/reservations/create', $cp) ?>"><span class="si">➕</span><span class="sl">Book Table</span></a>
                <a href="<?= BASE_PATH ?>/reservations/my"     class="sidebar-link<?= nav_active('/reservations/my', $cp) ?>"><span class="si">📋</span><span class="sl">My Reservations</span></a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-foot">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
                <div class="sidebar-user-info">
                    <div class="sidebar-username"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></div>
                    <div class="sidebar-role"><?= ucfirst($userRole) ?></div>
                </div>
            </div>
            <a href="<?= BASE_PATH ?>/logout" class="sidebar-link sidebar-logout"><span class="si">🚪</span><span class="sl">Logout</span></a>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <button class="topbar-menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')" title="Menu">☰</button>
            <span class="topbar-logo-mobile"><a href="<?= $logoHref ?>">🎲 Aji L3bo</a></span>
            <span class="topbar-user">👤 <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
        </header>
        <main class="content">
<?php endif; ?>
