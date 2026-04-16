<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\AuthController;

// Get action from URL
$action = $_GET['action'] ?? '';

// Create controller
$controller = new AuthController();

// Routing
switch ($action) {
    case 'register':
        $controller->register();
        break;

    case 'handleRegister':
        $controller->handleRegister();
        break;

    case 'login':
        $controller->login();
        break;

    case 'handleLogin':
        $controller->handleLogin();
        break;

    case 'playerDashboard':
          include __DIR__ . '/../app/View/dashboard/player.php';
          break;

    case 'adminDashboard':
          include __DIR__ . '/../app/View/dashboard/admin.php';
          break;

    default:
        echo "Home Page";
}