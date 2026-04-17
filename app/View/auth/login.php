<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobra - Rent Car</title>
    <style>
        :root {
            --primary-color: #ff5f5f; /* Couleur Corail du logo */
            --glass-bg: rgba(255, 255, 255, 0.1);
            --input-bg: #e0e0e0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'); /* Image de remplacement auto sombre */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }

        .brand-header {
            margin-bottom: 30px;
        }

        .brand-header h1 {
            font-size: 3rem;
            margin: 0;
            color: var(--primary-color);
            font-weight: 800;
            letter-spacing: -1px;
        }

        .brand-header p {
            margin: 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* La carte en verre */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 95, 95, 0.3);
            border-radius: 30px;
            padding: 40px 30px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8);
            position: relative;
        }

        /* Image de la voiture au dessus */
        .car-overlay {
            width: 80%;
            margin-top: -80px;
            margin-bottom: 20px;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.5));
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: none; /* Caché comme sur le design original */
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border-radius: 50px;
            border: none;
            background: var(--input-bg);
            box-sizing: border-box;
            font-size: 1rem;
            color: #333;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-register {
            background: transparent;
            border: 1px solid white;
            color: white;
        }

        .btn-primary {
            background: var(--input-bg);
            color: #333;
        }

        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        .separator {
            margin: 25px 0;
            display: flex;
            align-items: center;
            text-align: center;
        }

        .separator::before, .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }

        .separator span {
            padding: 0 10px;
            font-size: 0.9rem;
        }

        .fingerprint-section {
            cursor: pointer;
            color: var(--primary-color);
        }

        .fingerprint-icon {
            font-size: 40px;
            margin-bottom: 5px;
        }

        .auth-link {
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .auth-link a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .alert {
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="brand-header">
            <p>Hi, Welcome to</p>
            <h1>Cobra.</h1>
        </div>

        <div class="glass-card">
            <img src="https://i.imgur.com/8pXfXpB.png" alt="Car" class="car-overlay">

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert" style="background: rgba(255,0,0,0.2); color: #ff9999;">
                    <?php echo $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert" style="background: rgba(0,255,0,0.2); color: #99ff99;">
                    <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="POST" action="/Cafe-Aji-L3bo/public/index.php?action=handleLogin">
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-input" placeholder="✉ Email address" required>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="pass_word" class="form-input" placeholder="🔒 Password" required>
                </div>

                <div class="button-group">
                    <a href="register.php" class="btn btn-register">Register</a>
                    <button type="submit" class="btn btn-primary">Engine</button>
                </div>
            </form>

            <div class="separator">
                <span>Or</span>
            </div>

            <div class="fingerprint-section">
                <div class="fingerprint-icon">⚙️</div>
                <small>use Fingerprint</small>
            </div>
        </div>

        <p class="auth-link">
            <a href="../homepage">&larr; Return to homepage</a>
        </p>
    </div>

</body>
</html>