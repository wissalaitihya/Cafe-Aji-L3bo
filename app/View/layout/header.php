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
    function game_image_url(array $game): string {
        if (!empty($game['image_game'])) {
            return BASE_PATH . '/' . htmlspecialchars($game['image_game']);
        }
        $seed = max(1, (int)($game['id_game'] ?? 1));
        $category = rawurlencode(str_replace('_', '-', $game['category_game'] ?? 'board-game'));
        return 'https://loremflickr.com/900/600/boardgame,' . $category . '?lock=' . $seed;
    }
    $mainPageClass = '';
    if ($userRole !== 'guest') {
        if (in_array($cp, [BASE_PATH . '/player/dashboard', BASE_PATH . '/reservations/my', BASE_PATH . '/reservations/create'], true)
            || str_ends_with($cp, '/player/dashboard')
            || str_ends_with($cp, '/reservations/my')
            || str_ends_with($cp, '/reservations/create')) {
            $mainPageClass = ' page-full';
        }
        if (str_ends_with($cp, '/reservations/create')) {
            $mainPageClass .= ' page-form-center';
        }
    }
?>

<?php if ($userRole === 'guest'): ?>
<!-- ════ PUBLIC LAYOUT (no sidebar) ════ -->
<div class="public-layout">
    <header class="public-topbar">
        <a href="<?= BASE_PATH ?>/games" class="public-logo">🎲 <span>Aji L3bo</span></a>
        <form method="GET" action="<?= BASE_PATH ?>/games" class="global-search" role="search">
            <span class="global-search-icon" aria-hidden="true">&#128269;</span>
            <input type="search" name="q" placeholder="Search games" aria-label="Search games">
            <div class="global-search-results" hidden></div>
        </form>
        <button class="public-nav-toggle" id="public-nav-toggle" aria-label="Menu" aria-expanded="false">☰</button>
        <nav class="public-nav" id="public-nav">
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
            <button class="sidebar-toggle-btn" id="sidebar-toggle" title="Collapse navigation" aria-label="Collapse navigation">&#10094;</button>
        </div>

        <nav class="sidebar-nav">
            <?php if ($userRole === 'admin'): ?>
                <span class="sidebar-section-title">Admin</span>
                <a href="<?= BASE_PATH ?>/admin/dashboard" data-tooltip="Dashboard" class="sidebar-link<?= nav_active('/admin/dashboard', $cp) ?>"><span class="si">🏠</span><span class="sl">Dashboard</span></a>
                <a href="<?= BASE_PATH ?>/admin/stats"     data-tooltip="Statistics" class="sidebar-link<?= nav_active('/admin/stats', $cp) ?>"><span class="si">📊</span><span class="sl">Statistics</span></a>
                <span class="sidebar-section-title">Manage</span>
                <a href="<?= BASE_PATH ?>/games"        data-tooltip="Games" class="sidebar-link<?= nav_active('/games', $cp) ?>"><span class="si">🎮</span><span class="sl">Games</span></a>
                <a href="<?= BASE_PATH ?>/tables"       data-tooltip="Tables" class="sidebar-link<?= nav_active('/tables', $cp) ?>"><span class="si">🪑</span><span class="sl">Tables</span></a>
                <a href="<?= BASE_PATH ?>/reservations" data-tooltip="Reservations" class="sidebar-link<?= nav_active('/reservations', $cp) ?>"><span class="si">📋</span><span class="sl">Reservations</span></a>
                <a href="<?= BASE_PATH ?>/sessions"     data-tooltip="Sessions" class="sidebar-link<?= nav_active('/sessions', $cp) ?>"><span class="si">▶</span><span class="sl">Sessions</span></a>
            <?php else: /* player */ ?>
                <span class="sidebar-section-title">Player</span>
                <a href="<?= BASE_PATH ?>/player/dashboard"    data-tooltip="Dashboard" class="sidebar-link<?= nav_active('/player/dashboard', $cp) ?>"><span class="si">🏠</span><span class="sl">Dashboard</span></a>
                <span class="sidebar-section-title">Play</span>
                <a href="<?= BASE_PATH ?>/games"               data-tooltip="Games" class="sidebar-link<?= nav_active('/games', $cp) ?>"><span class="si">🎮</span><span class="sl">Games</span></a>
                <a href="<?= BASE_PATH ?>/reservations/create" data-tooltip="Book Table" class="sidebar-link<?= nav_active('/reservations/create', $cp) ?>"><span class="si">➕</span><span class="sl">Book Table</span></a>
                <a href="<?= BASE_PATH ?>/reservations/my"     data-tooltip="My Reservations" class="sidebar-link<?= nav_active('/reservations/my', $cp) ?>"><span class="si">📋</span><span class="sl">My Reservations</span></a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-foot">
            <a href="<?= BASE_PATH ?>/logout" data-tooltip="Logout" class="sidebar-link sidebar-logout"><span class="si">🚪</span><span class="sl">Logout</span></a>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <button class="topbar-menu-btn" id="topbar-menu" aria-controls="sidebar" aria-expanded="false" title="Open navigation" aria-label="Open navigation">☰</button>
            <div class="profile-anchor">
                <button type="button" class="topbar-profile profile-trigger" id="profile-trigger" aria-expanded="false" aria-controls="profile-popover" title="Open profile">
                    <span class="sidebar-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></span>
                    <span class="topbar-profile-info">
                        <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></strong>
                        <small><?= ucfirst($userRole) ?></small>
                    </span>
                    <span class="profile-chevron" aria-hidden="true">&#8250;</span>
                </button>
                <div class="profile-popover" id="profile-popover" hidden>
                    <span class="profile-popover-kicker">Signed in as</span>
                    <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></strong>
                    <span><?= ucfirst($userRole) ?> account</span>
                    <a href="<?= $logoHref ?>">Open dashboard</a>
                </div>
            </div>
            <span class="topbar-logo-mobile"><a href="<?= $logoHref ?>">🎲 Aji L3bo</a></span>
            <form method="GET" action="<?= BASE_PATH ?>/games" class="global-search" role="search">
                <span class="global-search-icon" aria-hidden="true">&#128269;</span>
                <input type="search" name="q" placeholder="Search games" aria-label="Search games">
                <div class="global-search-results" hidden></div>
            </form>
        </header>
        <main class="content<?= $mainPageClass ?>">
<?php endif; ?>
