<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="auth-card">
    <div class="auth-header">
        <h1 class="auth-title">Aji L3bo</h1>
        <p class="auth-subtitle">Connect to your account</p>
    </div>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_PATH ?>/login" class="form-card">
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Your password" required>
        </div>

        <button type="submit" class="btn btn-primary">Log in</button>
    </form>

    <p class="auth-link">
        Don't have an account yet? <a href="<?= BASE_PATH ?>/register">Register</a>
    </p>
    <p class="auth-link" style="margin-top: 0.5rem;">
        <a href="<?= BASE_PATH ?>/">&larr; Return to homepage</a>
    </p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>