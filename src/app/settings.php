<?php

declare(strict_types=1);

use Monolog\Logger;

$settings = [
    'settings' => [
        'jwt' => [
            'secret' => $_ENV['JWT_SECRET'],
            'exp_sec_refresh' => 3 * 60 * 60, // 3h
            'exp_sec_access' => 50 * 60, // 50 min
        ],
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../../tmp/logs',
            'filename' => 'app.log',
            'level' => Logger::ERROR,
            'file_permission' => 0664,
        ],
        'phinx' => [
            'paths' => [
                'migrations' => __DIR__ . '/../../modulos/*/Migrations',
                'seeds' => __DIR__ . '/../../modulos/*/Seeds',
            ],
            'migration_base_class' => 'App\Database\Migration',
        ],
    ],
];

$isSQLite = !isset($_ENV['DB_DRIVER']) || $_ENV['DB_DRIVER'] == 'sqlite';
if ($isSQLite) {
    $settings['settings']['db'] = [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/../../' . $_ENV['DB_DATABASE'],
    ];
} else {
    $settings['settings']['db'] = [
        'driver' => $_ENV['DB_DRIVER'],
        'database' => $_ENV['DB_DATABASE'],
        'host' => $_ENV['DB_HOST'],
        'port' => $_ENV['DB_PORT'],
        'username' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASS'],
    ];
}

return $settings;
