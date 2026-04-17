<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\AuthController;

// Get action from URL
$action = $_GET['action'] ?? '';

// Create controller
$controller = new AuthController();
