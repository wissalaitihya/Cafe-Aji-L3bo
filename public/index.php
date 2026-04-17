<?php

session_start();
date_default_timezone_set('Africa/Casablanca');

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Core\Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch();