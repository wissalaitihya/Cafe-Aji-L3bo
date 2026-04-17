<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aji L3bo Café</title>
    <link rel="stylesheet" href="/Cafe-Aji-L3bo/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/Cafe-Aji-L3bo/" class="logo">🎲 Aji L3bo</a>
        <div class="nav-links">
            <a href="/Cafe-Aji-L3bo/games">Games</a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/Cafe-Aji-L3bo/login">Login</a>
                <a href="/Cafe-Aji-L3bo/register">Register</a>
            <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                <a href="/Cafe-Aji-L3bo/admin/dashboard">Dashboard</a>
                <a href="/Cafe-Aji-L3bo/reservations">Reservations</a>
                <a href="/Cafe-Aji-L3bo/sessions">Sessions</a>
                <a href="/Cafe-Aji-L3bo/logout" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="/Cafe-Aji-L3bo/player/dashboard">Dashboard</a>
                <a href="/Cafe-Aji-L3bo/reservations/my">My Reservations</a>
                <a href="/Cafe-Aji-L3bo/reservations/create">Book Table</a>
                <a href="/Cafe-Aji-L3bo/logout" class="btn-logout">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container">