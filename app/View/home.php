<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aji L3bo Café - Jeux de Société</title>
    <link rel="stylesheet" href="../app/View/home.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- 🔹 Navbar -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-dice"></i> Aji L3bo
            </div>
            <ul class="nav-links">
                <li><a href="/">Accueil</a></li>
                <li><a href="/games">Jeux</a></li>
                <li><a href="/reservations/create">Réserver</a></li>
                <li><a href="/login">Connexion</a></li>
            </ul>
        </nav>
    </header>

    <!-- 🔹 Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue chez Aji L3bo Café</h1>
            <p>Découvrez une large sélection de jeux de société et vivez des moments inoubliables entre amis et en famille.</p>
            <a href="/reservations/create" class="btn-primary">Réserver une table</a>
        </div>
    </section>

    <!-- 🔹 Section Jeux Populaires -->
    <section class="games-section">
        <h2>Jeux Populaires</h2>
        <div class="games-grid">
            <!-- Carte Jeu 1 -->
            <div class="game-card">
                <img src="assets/images/catan.jpg" alt="Catan">
                <div class="game-info">
                    <h3>Catan</h3>
                    <p>Stratégie • 3-4 joueurs • 90 min</p>
                    <a href="/games/1" class="btn-secondary">Voir détails</a>
                </div>
            </div>

            <!-- Carte Jeu 2 -->
            <div class="game-card">
                <img src="assets/images/uno.jpg" alt="UNO">
                <div class="game-info">
                    <h3>UNO</h3>
                    <p>Ambiance • 2-10 joueurs • 30 min</p>
                    <a href="/games/2" class="btn-secondary">Voir détails</a>
                </div>
            </div>

            <!-- Carte Jeu 3 -->
            <div class="game-card">
                <img src="assets/images/dixit.jpg" alt="Dixit">
                <div class="game-info">
                    <h3>Dixit</h3>
                    <p>Famille • 3-6 joueurs • 45 min</p>
                    <a href="/games/3" class="btn-secondary">Voir détails</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 🔹 Section Pourquoi Nous -->
    <section class="features">
        <h2>Pourquoi choisir Aji L3bo ?</h2>
        <div class="features-grid">
            <div class="feature">
                <i class="fas fa-gamepad"></i>
                <h3>Large choix de jeux</h3>
                <p>Des jeux pour tous les âges et tous les goûts.</p>
            </div>
            <div class="feature">
                <i class="fas fa-users"></i>
                <h3>Ambiance conviviale</h3>
                <p>Un espace chaleureux pour partager des moments uniques.</p>
            </div>
            <div class="feature">
                <i class="fas fa-calendar-check"></i>
                <h3>Réservation facile</h3>
                <p>Réservez votre table en quelques clics.</p>
            </div>
        </div>
    </section>

    <!-- 🔹 Call To Action -->
    <section class="cta">
        <h2>Prêt à jouer ?</h2>
        <p>Réservez votre table dès maintenant et vivez une expérience ludique exceptionnelle.</p>
        <a href="/reservations/create" class="btn-primary">Réserver maintenant</a>
    </section>

    <!-- 🔹 Footer -->
    <footer>
        <p>&copy; 2026 Aji L3bo Café - Tous droits réservés</p>
        <div class="social-icons">
            <i class="fab fa-facebook"></i>
            <i class="fab fa-instagram"></i>
            <i class="fab fa-twitter"></i>
        </div>
    </footer>

</body>
</html>