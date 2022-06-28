<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;

$app->post('/api/auth/login', 'Modulos\System\Controller\AuthController:login');
// $app->post('/api/auth/refresh', 'App\Controller\System\AuthController:refresh');

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/auth/info', 'Modulos\System\Controller\AuthController:info');

    $group->get('/usuarios', 'Modulos\System\Controller\UsuarioController:getAll');
    $group->get('/usuarios/{id:[0-9]+}', 'Modulos\System\Controller\UsuarioController:get');

    $group->get('/perfis', 'Modulos\System\Controller\PerfilController:getAll');
    $group->get('/perfis/{id:[0-9]+}', 'Modulos\System\Controller\PerfilController:get');
})->add('Modulos\System\Middleware\JwtAuth');
