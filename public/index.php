<?php

session_start();
date_default_timezone_set('Africa/Casablanca');

// Base path for subdirectory deployment (e.g. /Cafe-Aji-L3bo)
define('BASE', rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'));

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Core\Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch();