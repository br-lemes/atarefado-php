<?php

use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\App;
use Slim\Factory\AppFactory;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use Awurth\SlimValidation\Validator;
use App\Service\System\AuthService;

$settings = require __DIR__ . '/settings.php';

$capsule = new DB;
$capsule->addConnection($settings['settings']['db']);
$capsule->getConnection()->enableQueryLog();
$capsule->setAsGlobal();
$capsule->bootEloquent();

return [
    Configuration::class => function () use ($settings) {
        return new Configuration($settings);
    },

    App::class => function (ContainerInterface $container) use ($settings) {
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $app->setBasePath($basePath != '/' ? $basePath : '');
        return $app;
    },

    DB::class => function (Configuration $config) use ($capsule) {
        return $capsule;
    },

    LoggerInterface::class => function (Configuration $config) {
        $settings = $config->getArray('settings.logger');
        $monolog = new LoggerFactory($settings);
        $monolog->addFileHandler($settings['filename']);
        $monolog->addConsoleHandler($settings['level']);
        return $monolog->createInstance($settings['name']);
    },

    Validator::class => function () {
        $defaultMessages = [
            'length' => 'O campo {{name}} deve ter entre {{minValue}} e {{maxValue}} caracteres.',
            'notBlank' => 'O campo {{name}} é obrigatório.',
            'intVal' => 'O campo {{name}} deve ser um número inteiro.',
            'email' => 'O campo {{name}} deve ser um email válido.',
            'ip' => 'O campo {{name}} deve ser um endereço de IP válido.',
            'in' => 'O campo {{name}} selecionado é inválido. Opções: {{haystack}}.',
            'date' => 'O campo {{name}} não é uma data válida.',
            'numeric' => 'O campo {{name}} deve ser um número.',
            'cpf' => 'O campo {{name}} deve ser um número de CPF válido.',
            'cnpj' => 'O campo {{name}} deve ser um número de CNPJ válido.',
            'min' => 'O campo {{name}} deve ser um valor mínimo {{interval}}.',
            'max' => 'O campo {{name}} deve ser um valor máximo {{interval}}.',
            'intType' => 'O campo {{name}} deve ser do tipo inteiro.',
            'json' => 'O campo {{name}} deve ser uma string JSON válida.',
            'stringType' => 'O campo {{name}} deve ser do tipo string.',
        ];
        return new Validator(true, $defaultMessages);
    },

    TokenJwt::class => DI\autowire(),
    AuthService::class => DI\autowire(),
];
