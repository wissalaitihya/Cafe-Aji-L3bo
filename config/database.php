<?php

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => (int) (getenv('DB_PORT') ?: 3307),
    'dbname' => getenv('DB_NAME') ?: 'aji_l3bo_cafe2',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: ''
];