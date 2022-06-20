<?php

declare(strict_types=1);

$app->get('/', 'App\Controller\IndexController:index');

$routeFiles = glob(__DIR__ . '/../../modulos/*/Routes.php');

foreach ($routeFiles as $routeFile) {
    require $routeFile;
}
