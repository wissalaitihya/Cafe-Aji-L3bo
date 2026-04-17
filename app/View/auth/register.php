<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="auth-card">
    <div class="auth-header">
        <h1 class="auth-title">Aji L3bo</h1>
        <p class="auth-subtitle">Create your account to play our games</p>
    </div>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_PATH ?>/register" class="form-card">
        <div class="form-group">
            <label for="name">Username</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Minimum 6 characters" minlength="6" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Confirm your password" minlength="6" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="auth-link">
        Already have an account? <a href="<?= BASE_PATH ?>/login">Connect</a>
    </p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>