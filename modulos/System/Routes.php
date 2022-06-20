<?php

declare(strict_types=1);

$app->post('/api/auth/login', 'Modulos\System\Controller\AuthController:login');
// $app->post('/api/auth/refresh', 'App\Controller\System\AuthController:refresh');
