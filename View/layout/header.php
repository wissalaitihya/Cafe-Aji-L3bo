<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Aji L3bo Café' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav>
        <a href="/games">Jeux</a>
        <a href="/reservations/create">Réserver</a>
        <a href="/reservations/my">Mes Réservations</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="/sessions/dashboard">Dashboard</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/logout" method="POST">Déconnexion</a>
        <?php else: ?>
            <a href="/login">Connexion</a>
            <a href="/register">Inscription</a>
        <?php endif; ?>
    </nav>
    <main>