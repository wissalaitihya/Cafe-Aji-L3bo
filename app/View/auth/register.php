<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register-Aji L3bo</title>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
        <h1 class="auth-title">Aji L3bo</h1>
        <p class="auth-subtitle">Create your account to play our games</p>
        </div>

        <h2>Create your account</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>


    <form method="POST" action="/Cafe-Aji-L3bo/public/index.php?action=handleRegister">
        <div class="form-group"></div>
            <label for="name">Username:</label>
            <input type="text"  name="name_user" required><br><br>
        </div>

        <label for="phone">Phone Number:</label>
        <input 
        type="text" 
        id="phone" 
        name="phone_number" 
        required><br><br>

    
        <div class="form-group"></div>
            <label for="email">Email:</label>
            <input type="email" name="email"  class="form-input" placeholder="votre@email.com" required><br><br>
         </div>

        <div class="form-group"></div>
            <label for="password">Password:</label>
            <input type="password" name="pass_word" class="form-input" placeholder="Minimum 6 caractères" minlength="6" required><br><br>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm Password:</label>
            <input type="password"
            id="pass_word_confirm"
            name="password_confirm"
            class="form-input"
            placeholder="Confirmez votre mot de passe"
            minlength="6"
            required><br><br>
        </div>

        <button type="submit">Register</button>
        <p class="auth-link">already have an account? 
            <a href="login.php">Connect</a>
        </p>
    </form>
</body>
</html>