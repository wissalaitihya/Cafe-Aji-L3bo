<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in-Aji l3bo</title>
</head>
<body>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <h2>connect to your account </h2>

    <form action="../../controllers/AuthController.php?action=login" method="POST">
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-input" placeholder="Your password"
          required>
      </div>

      <button type="submit" class="btn btn-primary">Log in </button>
    </form>

      <p class="auth-link">
        Don't have an account yet? <a href="register.php">Register</a>
      </p>
      <p class="auth-link" style="margin-top: 0.5rem;">
        <a href="../???????????homepage?????????????">&larr; Return to homepage</a>
      </p>
    </div>
  </div>

</body>

</html>
</body>
</html>