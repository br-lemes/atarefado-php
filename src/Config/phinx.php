<?php

use Illuminate\Database\Capsule\Manager as DB;
use Selective\Config\Configuration;

$app = require __DIR__ . '/../app/app.php';

$container = $app->getContainer();
$db = $container->get(DB::class);
$pdo = $db->connection()->getPdo();
$settings = $container->get(Configuration::class)->getArray('settings');
$database = $settings['db']['database'];
$phinx = $settings['phinx'];
$phinx['environments']['local'] = [
    'name' => $database,
    'connection' => $pdo,
];

return $phinx;
