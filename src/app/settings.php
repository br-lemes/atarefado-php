<?php

declare(strict_types=1);

use Monolog\Logger;

return [
    'settings' => [
        'db' => [
            'driver' => @$_ENV['DB_DRIVER'] ?: 'sqlite',
            'database' => __DIR__ . '/../../' . $_ENV['DB_DATABASE'],
        ],
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
