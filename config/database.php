<?php
// All secrets come from environment (.env via vlucas/phpdotenv, loaded in public/index.php).
// No hardcoded credentials here — fallbacks are dev-only and empty password is never used in prod.

return [
    'host'   => getenv('DB_HOST') ?: 'localhost',
    'port'   => (int) (getenv('DB_PORT') ?: 3306),
    'dbname' => getenv('DB_NAME') ?: 'aji_l3bo_cafe',
    'user'   => getenv('DB_USER') ?: 'root',
    'pass'   => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
];