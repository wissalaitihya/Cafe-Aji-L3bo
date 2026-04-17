<?php

session_start();
date_default_timezone_set('Africa/Casablanca');

// Dynamic base path: works regardless of where the project is deployed
define('BASE_PATH', rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Core\Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch();