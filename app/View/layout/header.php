<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aji L3bo Café</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="<?= BASE_PATH ?>/" class="logo">🎲 Aji L3bo</a>
        <div class="nav-links">
            <a href="<?= BASE_PATH ?>/games">Games</a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_PATH ?>/login">Login</a>
                <a href="<?= BASE_PATH ?>/register">Register</a>
            <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= BASE_PATH ?>/admin/dashboard">Dashboard</a>
                <a href="<?= BASE_PATH ?>/reservations">Reservations</a>
                <a href="<?= BASE_PATH ?>/sessions">Sessions</a>
                <a href="<?= BASE_PATH ?>/logout" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_PATH ?>/player/dashboard">Dashboard</a>
                <a href="<?= BASE_PATH ?>/reservations/my">My Reservations</a>
                <a href="<?= BASE_PATH ?>/reservations/create">Book Table</a>
                <a href="<?= BASE_PATH ?>/logout" class="btn-logout">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container">