<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;

$app->get('/', 'App\Controller\IndexController:index');

// $app->post('/api/auth/login', 'App\Controller\System\AuthController:login');
// $app->post('/api/auth/refresh', 'App\Controller\System\AuthController:refresh');

$app->group('/api', function (RouteCollectorProxy $group) {
})->add('App\Middleware\JwtAuth');
