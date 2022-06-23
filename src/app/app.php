<?php

declare(strict_types=1);

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();
$dotenv->required(['DB_DATABASE', 'JWT_SECRET']);

$settings = require __DIR__ . '/settings.php';

use DI\ContainerBuilder;
use Slim\App;
use SlimFacades\Facade;

$containerBuilder = new ContainerBuilder();
$containerDefinitions = require __DIR__ . '/container.php';
$containerBuilder->addDefinitions($containerDefinitions);
$container = $containerBuilder->build();
$app = $container->get(App::class);

require __DIR__ . '/middleware.php';
require __DIR__ . '/routes.php';

$app->map(
    ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
    '/{routes:.+}',
    function ($request, $response) {
        throw new \Slim\Exception\HttpNotFoundException($request);
    }
);

Facade::setFacadeApplication($app);

return $app;
