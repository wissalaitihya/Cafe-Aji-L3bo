<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="auth-card auth-register">
    <div class="auth-header">
        <p class="auth-kicker">JOIN THE TABLE</p>
        <h1 class="auth-title">Make room for fun.</h1>
        <p class="auth-subtitle">Create your account and discover your next favorite game.</p>
    </div>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_PATH ?>/register" class="form-card">
    <?= \Core\Csrf::field() ?>
        <div class="form-group">
            <label for="name">Username</label>
            <input type="text" id="name" name="name" class="form-input" required minlength="2" maxlength="40" autocomplete="username">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required maxlength="100" autocomplete="email">
        </div>

        <div class="form-group">
            <label for="phone">Phone (optional)</label>
            <input type="tel" id="phone" name="phone" class="form-input" placeholder="06XXXXXXXX" maxlength="20" pattern="[+0-9][0-9\s.\-]{5,14}" autocomplete="tel">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Minimum 6 characters" minlength="6" maxlength="255" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Confirm your password" minlength="6" maxlength="255" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="auth-link">
        Already have an account? <a href="<?= BASE_PATH ?>/login">Connect</a>
    </p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>